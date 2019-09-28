<?php

/**
 * EntityParser.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-ORM
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\ORM\Utils;

use XEAF\ORM\Core\EntityManager;
use XEAF\ORM\Models\EntityFromModel;
use XEAF\ORM\Models\EntityJoinModel;
use XEAF\ORM\Models\EntityModel;
use XEAF\ORM\Models\EntityOrderModel;
use XEAF\ORM\Models\EntityProperty;
use XEAF\ORM\Models\EntityTokenModel;
use XEAF\ORM\Utils\Exceptions\EntityException;

/**
 * Реализует методы разбора XQL запроса
 *
 * @package  XEAF\ORM\Utils
 */
class EntityParser {

    /**
     * Зарезервированные слова
     */
    private const WORDS = [
        'asc',
        'ascending',
        'by',
        'desc',
        'descending',
        'from',
        'inner',
        'join',
        'left',
        'on',
        'order',
        'outer',
        'right',
        'where',
    ];

    /**
     * Символ пробела
     */
    protected const SPACE = "\x20";

    /**
     * Символ перевода каретки
     */
    protected const CR = "\x0D";

    /**
     * Символ перехода на начало строки
     */
    protected const LF = "\x0A";

    /**
     * Одинарная кавычка (апостроф)
     */
    protected const SQ = "\x27";

    /**
     * Стоп-смвол
     */
    protected const END_CHAR = "\xDA";

    /**
     * Массив символов исходного выражения
     * @var array
     */
    private static $chars = [];

    /**
     * Разобранные лексемы
     * @var array
     */
    private static $tokens = [];

    /**
     * Позиция текущего проверяемго символа
     * @var int
     */
    private static $charPos = 0;

    /**
     * Номер фазы
     * @var int
     */
    private static $phase = 0;

    /**
     * Уровень вложенности скобок
     * @var int
     */
    private static $brackets = 0;

    /**
     * Менеджер сущностей
     * @var \XEAF\ORM\Core\EntityManager
     */
    private static $em = null;

    /**
     * Выбранные псевдонимы
     * @var array
     */
    private static $alias = [];

    /**
     * Сущности конструкции FROM
     * @var array
     */
    private static $from = [];

    /**
     * Сущности сонструкции JOIN
     * @var array
     */
    private static $join = [];

    /**
     * Условия конструкции WHERE
     * @var array
     */
    private static $where = [];

    /**
     * Порядок сортровки конструкции ORDER BY
     * @var array
     */
    private static $order = [];

    /**
     * Сущности и псевдонимы
     * @var array
     */
    private static $resolve = [];

    /**
     * Инициализирует парзер
     *
     * @param string                       $xql           Текст XQL запроса
     * @param \XEAF\ORM\Core\EntityManager $entityManager Менеджер сущностей
     *
     * @return void
     */
    protected static function initialize(string $xql, EntityManager $entityManager): void {
        self::$chars    = preg_split('//u', $xql, null, PREG_SPLIT_NO_EMPTY);
        self::$chars[]  = self::END_CHAR;
        self::$tokens   = [];
        self::$tokens[] = new EntityTokenModel(EntityTokenModel::T_UNKNOWN, '_', 0);
        self::$charPos  = 0;
        self::$brackets = 0;
        self::$phase    = 0;
        self::$em       = $entityManager;
        self::$alias    = [];
        self::$from     = [];
        self::$join     = [];
        self::$where    = [];
        self::$order    = [];
        self::$resolve  = [];
    }

    /**
     * Разбирает исходный текст на токены
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function tokenize(): void {
        do {
            $ch = self::$chars[self::$charPos];
            if ($ch == self::SPACE || $ch == self::CR || $ch == self::LF) {
                self::skipEmptySpace();
            } else if ($ch == '%' || $ch == '>' || $ch == '<' || $ch == '!' || $ch == '=' || $ch == '|' || $ch == '&') {
                self::readOperator();
            } else if (($ch >= 'a' && $ch <= 'z') || ($ch >= 'A' && $ch <= 'Z') || $ch == '_') {
                self::readIdentifier();
            } else if ($ch == self::SQ) {
                self::readStringConstant();
            } else if ($ch >= '0' && $ch <= '9') {
                self::readNumericConstant();
            } else if ($ch == '.' || $ch == ',' || $ch == ':') {
                self::readDivider();
            } else if ($ch == '(' || $ch == ')') {
                self::readBrackets();
            } else if ($ch == self::END_CHAR) {
                return;
            }
            self::$charPos++;
        } while (true);
    }

    /**
     * Пропускает пространство из пустых символов
     *
     * @return void
     */
    protected static function skipEmptySpace(): void {
        $ch = self::$chars[self::$charPos];
        while ($ch == self::SPACE || $ch == self::CR || $ch == self::LF) {
            $ch = self::$chars[++self::$charPos];
        }
        self::$charPos--;
    }

    /**
     * Читает идентификатор
     *
     * @return void
     */
    protected static function readIdentifier(): void {
        $token = '';
        $ch    = self::$chars[self::$charPos];
        $pos   = self::$charPos;
        while (($ch >= 'A' && $ch <= 'Z') || ($ch >= 'a' && $ch <= 'z') || ($ch >= '0' && $ch <= '9') || $ch == '_') {
            $token .= $ch;
            $ch    = self::$chars[++self::$charPos];
        }
        self::$charPos--;
        self::$tokens[] = new EntityTokenModel(EntityTokenModel::T_UNKNOWN, $token, $pos);
    }

    /**
     * Читает оператор
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function readOperator(): void {
        $token = '';
        $ch    = self::$chars[self::$charPos];
        $pos   = self::$charPos;
        switch ($ch) {
            case '%':
                if (self::$chars[self::$charPos + 1] == '%') {
                    $token = '%%';
                    self::$charPos++;
                }
                break;
            case '>':
                $token = $ch;
                if (self::$chars[self::$charPos + 1] == '=') {
                    $token = '>=';
                    self::$charPos++;
                }
                break;
            case '<':
                $token = $ch;
                if (self::$chars[self::$charPos + 1] == '=') {
                    $token = '<=';
                    self::$charPos++;
                }
                break;
            case '!':
                $token = $ch;
                if (self::$chars[self::$charPos + 1] == '=') {
                    $token = '!=';
                    self::$charPos++;
                }
                break;
            case '=':
                if (self::$chars[self::$charPos + 1] == '=') {
                    $token = '==';
                    self::$charPos++;
                } else {
                    throw EntityException::syntaxError($pos);
                }
                break;
            case '|':
                if (self::$chars[self::$charPos + 1] == '|') {
                    $token = '||';
                    self::$charPos++;
                } else {
                    throw EntityException::syntaxError($pos);
                }
                break;
            case '&':
                if (self::$chars[self::$charPos + 1] == '&') {
                    $token = '&&';
                    self::$charPos++;
                } else {
                    throw EntityException::syntaxError($pos);
                }
                break;
        }
        if ($token != '') {
            self::$tokens[] = new EntityTokenModel(EntityTokenModel::T_OPERATOR, $token, $pos);
        }
    }

    /**
     * Читает строковую константу
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function readStringConstant(): void {
        $token = self::SQ;
        $pos   = self::$charPos;
        $ch    = self::$chars[++self::$charPos];
        while ($ch != self::CR && $ch != self::LF && $ch != self::SQ && $ch != self::END_CHAR) {
            $ch    = self::$chars[self::$charPos++];
            $token .= $ch;
        }
        if ($ch != self::SQ) {
            throw EntityException::unclosedSingleQuote($pos);
        }
        self::$charPos--;
        self::$tokens[] = new EntityTokenModel(EntityTokenModel::T_CONSTANT, $token, $pos);
    }

    /**
     * Читает цифровую константу
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function readNumericConstant(): void {
        $token  = '';
        $ch     = self::$chars[self::$charPos];
        $pos    = self::$charPos;
        $points = 0;
        while (($ch >= '0' && $ch <= '9') || $ch == '.') {
            if ($ch == '.') {
                $points++;
                if ($points > 1) {
                    throw EntityException::syntaxError($pos);
                }
            }
            $token .= $ch;
            $ch    = self::$chars[++self::$charPos];
        }
        self::$charPos--;
        self::$tokens[] = new EntityTokenModel(EntityTokenModel::T_CONSTANT, $token, $pos);
    }

    /**
     * Читает разделитель
     *
     * @return void
     */
    protected static function readDivider(): void {
        $ch   = self::$chars[self::$charPos];
        $type = EntityTokenModel::T_UNKNOWN;
        switch ($ch) {
            case '.':
                $type = EntityTokenModel::T_POINT;
                break;
            case ',':
                $type = EntityTokenModel::T_COMMA;
                break;
            case ':':
                $type = EntityTokenModel::T_COLON;
                break;
        }
        self::$tokens[] = new EntityTokenModel($type, $ch, self::$charPos);
    }

    /**
     * Читает и проверяет скобки
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function readBrackets(): void {
        $ch = self::$chars[self::$charPos];
        switch ($ch) {
            case '(':
                self::$brackets++;
                break;
            case ')':
                self::$brackets--;
                if (self::$brackets < 0) {
                    throw EntityException::unpairedClosingBracket(self::$charPos);
                }
                break;
        }
        self::$tokens[] = new EntityTokenModel(EntityTokenModel::T_BRACKET, $ch, self::$charPos);
    }

    /**
     * Категориирует разобранные лексемы
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function categorize(): void {
        $n = count(self::$tokens);
        for ($i = 1; $i < $n; $i++) {
            $token    = self::$tokens[$i];
            $previous = self::$tokens[$i - 1];
            $next     = self::$tokens[$i + 1] ?? null;
            if (in_array($token->token, self::WORDS)) {
                self::categorizedWords($token, $previous);
            } else {
                switch (self::$phase) {
                    case 0:
                        self::categorizedPhase0($token, $previous);
                        break;
                    case 1:
                        self::categorizedPhase1($token, $previous);
                        break;
                    case 2:
                        self::categorizedPhase2($token, $previous, $next);
                        break;
                    case 3:
                        self::categorizedPhase3($token, $previous, $next);
                        break;
                    case 4:
                        self::categorizedPhase4($token, $previous, $next);
                        break;
                }
            }
            $token->phase = self::$phase;
        }
    }

    /**
     * Категоризация по зарезервированным словам
     *
     * @param \XEAF\ORM\Models\EntityTokenModel $token    Текущая тексема
     * @param \XEAF\ORM\Models\EntityTokenModel $previous Предыдущая лексема
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function categorizedWords(EntityTokenModel $token, EntityTokenModel $previous): void {
        $token->type = EntityTokenModel::T_WORD;
        switch ($token->token) {
            case 'asc':
            case 'ascending':
                if (self::$phase != 4 && $previous->type != EntityTokenModel::T_PROPERTY) {
                    throw EntityException::invalidToken($token->token, $token->position);
                }
                $n = count(self::$order) - 1;
                if ($n >= 0) {
                    $order = self::$order[$n];
                    assert($order instanceof EntityOrderModel);
                    $order->direction = EntityOrderModel::ORDER_ASCENDING;
                }
                break;
            case 'by':
                if (self::$phase != 4 || $previous->token != 'order') {
                    throw EntityException::invalidToken($token->token, $token->position);
                }
                break;
            case 'desc':
            case 'descending':
                if (self::$phase != 4 && $previous->type != EntityTokenModel::T_PROPERTY) {
                    throw EntityException::invalidToken($token->token, $token->position);
                }
                $n = count(self::$order) - 1;
                if ($n >= 0) {
                    $order = self::$order[$n];
                    assert($order instanceof EntityOrderModel);
                    $order->direction = EntityOrderModel::ORDER_DESCENDING;
                }
                break;
            case 'from':
                if (self::$phase != 0) {
                    throw EntityException::invalidToken($token->token, $token->position);
                }
                if ($previous->type != EntityTokenModel::T_ALIAS) {
                    throw EntityException::syntaxError($token->position);
                }
                self::$phase = 1;
                break;
            case 'join':
                if (self::$phase != 2 || ($previous->token != 'inner' && $previous->token != 'outer' && $previous->token != 'left' && $previous->token != 'right')) {
                    throw EntityException::invalidToken($token->token, $token->position);
                }
                $token->extra = $previous->token;
                break;
            case 'inner':
            case 'left':
            case 'right':
            case 'outer':
                if (self::$phase != 1) {
                    throw EntityException::invalidToken($token->token, $token->position);
                }
                self::$phase = 2;
                break;
            case 'on':
                break;
            case 'order':
                if (self::$phase == 0) {
                    throw EntityException::invalidToken($token->token, $token->position);
                }
                self::$phase = 4;
                break;
            case 'where':
                if (self::$phase != 1 && self::$phase != 2) {
                    throw EntityException::invalidToken($token->token, $token->position);
                }
                self::$phase = 3;
                break;
        }
    }

    /**
     * Категоризирует лексемы фазы 0
     *
     * @param \XEAF\ORM\Models\EntityTokenModel $token    Текущая тексема
     * @param \XEAF\ORM\Models\EntityTokenModel $previous Предыдущая лексема
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function categorizedPhase0(EntityTokenModel $token, EntityTokenModel $previous): void {
        switch ($token->type) {
            case EntityTokenModel::T_UNKNOWN:
                if ($previous->token == '_' || $previous->type == EntityTokenModel::T_COMMA) {
                    self::$alias[] = $token->token;
                    $token->type   = EntityTokenModel::T_ALIAS;
                } else {
                    throw EntityException::syntaxError($token->position);
                }
                break;
            case EntityTokenModel::T_COMMA:
                if ($previous->type != EntityTokenModel::T_ALIAS) {
                    throw EntityException::syntaxError($token->position);
                }
                break;
            default:
                throw EntityException::syntaxError($token->position);
        }
    }

    /**
     * Категоризирует лексемы фазы 1
     *
     * @param \XEAF\ORM\Models\EntityTokenModel $token    Текущая тексема
     * @param \XEAF\ORM\Models\EntityTokenModel $previous Предыдущая лексема
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function categorizedPhase1(EntityTokenModel $token, EntityTokenModel $previous): void {
        switch ($previous->type) {
            case EntityTokenModel::T_WORD:
                if ($previous->token == 'from') {
                    $token->type = EntityTokenModel::T_ENTITY;
                }
                break;
            case EntityTokenModel::T_ENTITY:
                $previous->extra = $token->token;
                $token->extra    = $previous->token;
                $token->type     = EntityTokenModel::T_ALIAS;

                self::$from[]                 = new EntityFromModel($previous->token, $token->token);
                self::$resolve[$token->token] = $previous->token;

                break;
            case EntityTokenModel::T_COMMA:
                if (self::$em->entityExists($token->token)) {
                    $token->type = EntityTokenModel::T_ENTITY;
                } else {
                    throw EntityException::unknownEntity($token->token, $token->position);
                }
                break;
        }
    }

    /**
     * Категоризирует лексемы фазы 2
     *
     * @param \XEAF\ORM\Models\EntityTokenModel $token    Текущая тексема
     * @param \XEAF\ORM\Models\EntityTokenModel $previous Предыдущая лексема
     * @param \XEAF\ORM\Models\EntityTokenModel $next     Следующая лексема
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function categorizedPhase2(EntityTokenModel $token, EntityTokenModel $previous, EntityTokenModel $next): void {
        switch ($previous->type) {
            case EntityTokenModel::T_WORD:
                if ($previous->token == 'join') {
                    if (self::$em->entityExists($token->token)) {
                        $token->type  = EntityTokenModel::T_ENTITY;
                        $token->extra = $previous->extra;
                    } else {
                        throw EntityException::unknownEntity($token->token, $token->position);
                    }
                } else if ($previous->token == 'on') {
                    $token->type = EntityTokenModel::T_ALIAS;
                } else {
                    throw EntityException::syntaxError($token->position);
                }
                break;
            case EntityTokenModel::T_ENTITY:

                self::$join[]                 = new EntityJoinModel($previous->extra, $previous->token, $token->token, '', '', '', '');
                self::$resolve[$token->token] = $previous->token;

                $previous->extra = $token->token;
                $token->type     = EntityTokenModel::T_ALIAS;
                $token->extra    = $previous->token;
                break;
            case EntityTokenModel::T_ALIAS:
                if ($token->type != EntityTokenModel::T_POINT) {
                    throw EntityException::syntaxError($token->position);
                }
                $next->extra = $previous->token;
                break;
            case EntityTokenModel::T_POINT:
                if ($token->type != EntityTokenModel::T_UNKNOWN) {
                    throw EntityException::syntaxError($token->position);
                }
                $token->type = EntityTokenModel::T_PROPERTY;
                $n           = count(self::$join) - 1;
                if ($n >= 0) {
                    $join = self::$join[$n];
                    assert($join instanceof EntityJoinModel);
                    if ($join->leftAlias == '') {
                        $join->leftAlias    = $token->extra;
                        $join->leftProperty = $token->token;
                    } else {
                        $join->rightAlias    = $token->extra;
                        $join->rightProperty = $token->token;
                    }
                }
                break;
            case EntityTokenModel::T_PROPERTY:
                if ($token->type != EntityTokenModel::T_OPERATOR && $token != '==') {
                    throw EntityException::syntaxError($token->position);
                }
                break;
            case EntityTokenModel::T_OPERATOR:
                $token->type = EntityTokenModel::T_ALIAS;
                break;
        }
    }

    /**
     * Категоризирует лексемы фазы 3
     *
     * @param \XEAF\ORM\Models\EntityTokenModel      $token    Текущая тексема
     * @param \XEAF\ORM\Models\EntityTokenModel      $previous Предыдущая лексема
     * @param \XEAF\ORM\Models\EntityTokenModel|null $next     Следующая лексема
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function categorizedPhase3(EntityTokenModel $token, EntityTokenModel $previous, ?EntityTokenModel $next): void {
        $error = true;
        switch ($token->type) {
            case EntityTokenModel::T_UNKNOWN:
                switch ($token->token) {
                    case 'null':
                        if ($previous->type != EntityTokenModel::T_OPERATOR) {
                            throw EntityException::syntaxError($token->position);
                        }
                        $token->type = EntityTokenModel::T_CONSTANT;
                        break;
                    case 'true':
                    case 'false':
                        $token->type = EntityTokenModel::T_CONSTANT;
                        break;
                    /*
                    case 'false':
                        $token->type = EntityTokenModel::T_CONSTANT;
                        break;
                    */
                }
                $error = false;
                break;
            case EntityTokenModel::T_POINT:
                if ($previous->type == EntityTokenModel::T_UNKNOWN) {
                    $previous->type = EntityTokenModel::T_ALIAS;
                    if ($next->type == EntityTokenModel::T_UNKNOWN) {
                        $next->type = EntityTokenModel::T_PROPERTY;

                        $entityName = self::$resolve[$previous->token] ?? null;
                        if (!$entityName) {
                            throw EntityException::unknownEntityAlias($previous->token, $previous->position);
                        }
                        if (!self::$em->entityPropertyExists($entityName, $next->token)) {
                            throw EntityException::unknownEntityProperty($previous->token, $next->token, $previous->position);
                        }

                        $error = false;
                    }
                }
                break;
            case EntityTokenModel::T_COLON:
                if ($next->type == EntityTokenModel::T_UNKNOWN) {
                    $next->type = EntityTokenModel::T_PARAMETER;
                    $error      = false;
                }
                break;
            default:
                $error = false;
                break;
        }
        if ($error) {
            throw EntityException::syntaxError($token->position);
        }
    }

    /**
     * Категоризирует лексемы фазы 4
     *
     * @param \XEAF\ORM\Models\EntityTokenModel $token    Текущая тексема
     * @param \XEAF\ORM\Models\EntityTokenModel $previous Предыдущая лексема
     * @param \XEAF\ORM\Models\EntityTokenModel $next     Следующая лексема
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function categorizedPhase4(EntityTokenModel $token, EntityTokenModel $previous, EntityTokenModel $next): void {
        $error = true;
        switch ($token->type) {
            case EntityTokenModel::T_WORD:
                break;
            case EntityTokenModel::T_COMMA:
                if ($previous->type == EntityTokenModel::T_WORD || $previous->type == EntityTokenModel::T_PROPERTY) {
                    $error = false;
                }
                break;
            case EntityTokenModel::T_POINT:
                if ($previous->type == EntityTokenModel::T_UNKNOWN) {
                    $previous->type = EntityTokenModel::T_ALIAS;
                    if ($next->type == EntityTokenModel::T_UNKNOWN) {
                        $next->type = EntityTokenModel::T_PROPERTY;
                        $entityName = self::$resolve[$previous->token] ?? null;
                        if (!$entityName) {
                            throw EntityException::unknownEntityAlias($previous->token, $previous->position);
                        }
                        if (!self::$em->entityPropertyExists($entityName, $next->token)) {
                            throw EntityException::unknownEntityProperty($previous->token, $next->token, $previous->position);
                        }
                        self::$order[] = new EntityOrderModel($previous->token, $next->token);

                        $error = false;
                    }
                }
                break;
            default:
                $error = false;
                break;
        }
        if ($error) {
            throw EntityException::syntaxError($token->position);
        }
    }

    /**
     * Разрешает ссылки на псевдонимы
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function resolveAliases(): void {
        foreach (self::$alias as $alias) {
            $entityName = self::$resolve[$alias] ?? null;
            if (!$entityName) {
                throw EntityException::unknownEntityAlias($alias);
            }
            if (!isset(self::$resolve[$alias])) {
                throw EntityException::unknownEntity($entityName);
            }
        }
    }

    /**
     * Выполняет подготовительную работу
     *
     * @param string                       $xql           Текст запроса XQL
     * @param \XEAF\ORM\Core\EntityManager $entityManager Менеджер сущностей
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected static function prepare(string $xql, EntityManager $entityManager): void {
        self::initialize($xql, $entityManager);
        self::tokenize();
        self::categorize();
        self::resolveAliases();
    }

    /**
     * Создает объект запроса
     *
     * @param string                       $xql           Текст запроса XQL
     * @param \XEAF\ORM\Core\EntityManager $entityManager Менеджер сущностей
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function createEntityQuery(string $xql, EntityManager $entityManager): EntityQuery {
        self::prepare($xql, $entityManager);
        $result = new EntityQuery($entityManager);
        self::queryInitAlias($result);
        self::queryInitFrom($result);
        self::queryInitJoin($result);
        self::queryInitWhere($result);
        self::queryInitOrder($result);
        return $result;
    }

    /**
     * Инициализирует псевдонимы объекта запроса
     *
     * @param \XEAF\ORM\Utils\EntityQuery $entityQuery Объект запроса
     *
     * @return void
     */
    protected static function queryInitAlias(EntityQuery $entityQuery): void {
        foreach (self::$alias as $alias) {
            $entityQuery->select($alias);
        }
    }

    /**
     * Инициализирует конструкцию FROM объекта запроса
     *
     * @param \XEAF\ORM\Utils\EntityQuery $entityQuery Объект запроса
     *
     * @return void
     */
    protected static function queryInitFrom(EntityQuery $entityQuery): void {
        foreach (self::$from as $from) {
            assert($from instanceof EntityFromModel);
            $entityQuery->from($from->name, $from->alias);
        }
    }

    /**
     * Инициализирует конструкцию JOIN объекта запроса
     *
     * @param \XEAF\ORM\Utils\EntityQuery $entityQuery Объект запроса
     *
     * @return void
     */
    protected static function queryInitJoin(EntityQuery $entityQuery): void {
        foreach (self::$join as $join) {
            assert($join instanceof EntityJoinModel);
            switch ($join->type) {
                case 'left':
                    $entityQuery->leftJoinOn($join->name, $join->alias, $join->leftAlias . '.' . $join->leftProperty, $join->rightAlias . '.' . $join->rightProperty);
                    break;
                case 'right':
                    $entityQuery->rightJoinOn($join->name, $join->alias, $join->leftAlias . '.' . $join->leftProperty, $join->rightAlias . '.' . $join->rightProperty);
                    break;
                case 'inner':
                    $entityQuery->innerJoinOn($join->name, $join->alias, $join->leftAlias . '.' . $join->leftProperty, $join->rightAlias . '.' . $join->rightProperty);
                    break;
                case 'outer':
                    $entityQuery->outerJoinOn($join->name, $join->alias, $join->leftAlias . '.' . $join->leftProperty, $join->rightAlias . '.' . $join->rightProperty);
                    break;
            }
        }
    }

    /**
     * Инициализирует конструкцию WHERE объекта запроса
     *
     * @param \XEAF\ORM\Utils\EntityQuery $entityQuery Объект запроса
     *
     * @return void
     */
    protected static function queryInitWhere(EntityQuery $entityQuery): void {
        $where = '';
        foreach (self::$tokens as $token) {
            assert($token instanceof EntityTokenModel);
            if ($token->phase == 3) { // where
                if ($token->type != EntityTokenModel::T_WORD && $token->token != 'where') {
                    if ($token->type != EntityTokenModel::T_PROPERTY && $token->type != EntityTokenModel::T_PARAMETER && $token->type != EntityTokenModel::T_POINT) {
                        $where .= ' ';
                    }
                    $where .= $token->token;
                }
            }
        }
        if ($where) {
            $entityQuery->where(trim($where));
        }
    }

    /**
     * Инициализирует конструкцию ORDER объекта запроса
     *
     * @param \XEAF\ORM\Utils\EntityQuery $entityQuery Объект запроса
     *
     * @return void
     */
    protected static function queryInitOrder(EntityQuery $entityQuery): void {
        foreach (self::$order as $order) {
            assert($order instanceof EntityOrderModel);
            $entityQuery->andOrderBy($order->alias . '.' . $order->property, $order->direction);
        }
    }

    /**
     * Генерирует текст SQL запроса по XQL
     *
     * @param string                       $xql           Текст запроса XQL
     * @param \XEAF\ORM\Core\EntityManager $entityManager Менеджер сущностей
     *
     * @return string
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function generateSQL(string $xql, EntityManager $entityManager): string {
        $result = '';
        self::prepare($xql, $entityManager);
        if ($xql) {
            $result .= 'select ';
            $result .= ' ' . self::sqlAliases();
            $result .= ' ' . self::sqlFrom();
            $result .= ' ' . self::sqlJoin();
            $result .= ' ' . self::sqlWhere();
            $result .= ' ' . self::sqlOrderBy();
        }
        return trim($result);
    }

    /**
     * Генерирует текст SQL запроса выбора количества записей
     *
     * @param string                       $xql           Текст запроса XQL
     * @param \XEAF\ORM\Core\EntityManager $entityManager Менеджер сущностей
     *
     * @return string
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function generateCountSQL(string $xql, EntityManager $entityManager): string {
        $result = '';
        self::prepare($xql, $entityManager);
        if ($xql) {
            $result .= 'select count(*) as result';
            $result .= ' ' . self::sqlFrom();
            $result .= ' ' . self::sqlJoin();
            $result .= ' ' . self::sqlWhere();
        }
        return trim($result);
    }

    /**
     * Возвращает текст списка полей SQL запроса
     *
     * @return string
     */
    protected static function sqlAliases(): string {
        $result = '';
        foreach (self::$alias as $alias) {
            $entityName  = self::$resolve[$alias];
            $entityModel = self::$em->entityModelByName($entityName);
            $properties  = $entityModel->entityProperties;
            foreach ($properties as $property) {
                assert($property instanceof EntityProperty);
                $result .= $alias . '.' . $property->fieldName . ', ';
            }
        }
        return rtrim($result, ', ') . ' ';
    }

    /**
     * Возвращает текст констркуции FROM SQL запроса
     *
     * @return string
     */
    protected static function sqlFrom(): string {
        $result = 'from ';
        foreach (self::$from as $from) {
            assert($from instanceof EntityFromModel);
            $entityModel = self::$em->entityModelByName($from->name);
            $result      .= $entityModel->tableName . ' ' . $from->alias . ', ';
        }
        return rtrim($result, ', ') . ' ';
    }

    /**
     * Возвращает текст констркуции JOIN SQL запроса
     *
     * @return string
     */
    protected static function sqlJoin(): string {
        $result = '';
        if (self::$join) {
            foreach (self::$join as $join) {
                assert($join instanceof EntityJoinModel);
                $entityModel = self::$em->entityModelByName($join->name);
                $result      .= $join->type . ' join ' . $entityModel->tableName . ' ' . $join->alias;
                $entityModel = self::$em->entityModelByName(self::$resolve[$join->leftAlias]);
                $result      .= ' on ' . $join->leftAlias . '.' . $entityModel->entityProperty($join->leftProperty)->fieldName . '=';
                $entityModel = self::$em->entityModelByName(self::$resolve[$join->rightAlias]);
                $result      .= $join->rightAlias . '.' . $entityModel->entityProperty($join->rightProperty)->fieldName . ', ';
            }
        }
        return rtrim($result, ', ') . ' ';
    }

    /**
     * Возвращает текст конструкции WHERE SQL запроса
     *
     * @return string
     */
    protected static function sqlWhere(): string {
        $result      = '';
        $entityName  = '';
        $entityModel = null;
        $previous    = null;
        foreach (self::$tokens as $token) {
            assert($token instanceof EntityTokenModel);
            if ($token->phase == 3) {
                if ($token->type != EntityTokenModel::T_WORD && $token->token != 'where') {
                    if ($token->type != EntityTokenModel::T_PROPERTY && $token->type != EntityTokenModel::T_PARAMETER && $token->type != EntityTokenModel::T_POINT) {
                        $result .= ' ';
                    }
                    switch ($token->type) {
                        case EntityTokenModel::T_CONSTANT:
                            switch ($token->token) {
                                case 'null':
                                    $op     = trim(mb_substr($result, -3));
                                    $result = mb_substr($result, 0, -3);
                                    if ($op == '<>') {
                                        $result .= 'is not ';
                                    } else {
                                        $result .= 'is ';
                                    }
                                    break;
                                case 'false':
                                    $token->token = '0';
                                    break;
                                case 'true':
                                    $token->token = '1';
                                    break;
                            }
                            break;
                        case EntityTokenModel::T_OPERATOR:
                            switch ($token->token) {
                                case '!':
                                    $token->token = 'not';
                                    break;
                                case '==':
                                    $token->token = '=';
                                    break;
                                case '!=':
                                    $token->token = '<>';
                                    break;
                                case '&&':
                                    $token->token = ' and ';
                                    break;
                                case '||':
                                    $token->token = ' or ';
                                    break;
                                case '%%':
                                    $token->token = ' like ';
                                    $result       = self::adjustLike($previous, $entityModel, $result);
                                    break;
                            }
                            break;
                        case EntityTokenModel::T_ALIAS:
                            $entityName = self::$resolve[$token->token];
                            break;
                        case EntityTokenModel::T_PROPERTY:
                            $entityModel  = self::$em->entityModelByName($entityName);
                            $token->token = $entityModel->entityProperty($token->token)->fieldName;
                            break;
                    }
                    $result .= $token->token;
                }
                $previous = $token;
            }
        }
        return $result == '' ? $result : 'where ' . rtrim($result, ', ') . ' ';
    }

    /**
     * Уточняет параметр оператора LIKE
     *
     * @param \XEAF\ORM\Models\EntityTokenModel|null $token Модель лексемы
     * @param \XEAF\ORM\Models\EntityModel|null      $model Модуль сущности
     * @param string                                 $where Текущее значение результата построения WHERE
     *
     * @return string
     */
    protected static function adjustLike(?EntityTokenModel $token, ?EntityModel $model, string $where): string {
        $result = rtrim($where);
        if ($token && $model && $token->type == EntityTokenModel::T_PROPERTY) {
            $property = $model->entityPropertyByField($token->token);
            switch ($property->dataType) {
                case EntityProperty::DT_DATE:
                    $pos = mb_strrpos($result, ' ');
                    if ($pos !== false) {
                        $result = mb_substr($where, 0, $pos);
                        $entity = mb_substr($where, $pos);
                        $result .= ' ' . self::$em->db->formatDate($entity);
                    }
                    break;
                case EntityProperty::DT_DATETIME:
                    $pos = mb_strrpos($result, ' ');
                    if ($pos !== false) {
                        $result = mb_substr($where, 0, $pos);
                        $entity = mb_substr($where, $pos);
                        $result .= ' ' . self::$em->db->formatDateTime($entity);
                    }
                    break;
                case EntityProperty::DT_STRING:
                    $pos = mb_strrpos($result, ' ');
                    if ($pos !== false) {
                        $result = mb_substr($where, 0, $pos);
                        $entity = mb_substr($where, $pos);
                        $result .= ' ' . self::$em->db->toUpperCase($entity);
                    }
                    break;
            }
        }
        return $result . ' ';
    }

    /**
     * Возвращает текст констркуции ORDER BY SQL запроса
     *
     * @return string
     */
    protected static function sqlOrderBy(): string {
        $result = '';
        if (self::$order) {
            $result .= 'order by ';
            foreach (self::$order as $order) {
                assert($order instanceof EntityOrderModel);
                $entityModel = self::$em->entityModelByName(self::$resolve[$order->alias]);
                $direction   = $order->direction;
                if ($direction == EntityOrderModel::ORDER_ASCENDING) {
                    $direction = '';
                } else {
                    $direction = 'desc';
                }
                $result .= $order->alias . '.' . $entityModel->entityProperty($order->property)->fieldName . ' ' . $direction . ', ';
            }
        }
        return rtrim($result, ', ') . ' ';
    }

    /**
     * Возвращает текст XQL запроса выбора записи
     *
     * @param \XEAF\ORM\Core\EntityManager $entityManager Менеджер сущностей
     * @param string                       $name          Имя сущности
     *
     * @return string
     */
    public static function defaultSelectEntityXQL(EntityManager $entityManager, string $name): string {
        $where = '';
        $model = $entityManager->entityModelByName($name);
        foreach ($model->primaryKeys as $primaryKey) {
            if ($where != '') {
                $where .= ' and ';
            }
            $where .= 'e.' . $primaryKey . ' == :' . $primaryKey;
        }
        return 'e from ' . $name . ' e where ' . $where;
    }

    /**
     * Возвращает стандартный текст SQL команды добавления записи сущности
     *
     * @param \XEAF\ORM\Models\EntityModel $entityModel Модуль сущности
     *
     * @return string
     */
    public static function defaultInsertEntitySQL(EntityModel $entityModel): string {
        $fields = '';
        $values = '';
        foreach ($entityModel->entityProperties as $name => $property) {
            assert($property instanceof EntityProperty);
            if (!$property->readOnly && !$property->autoIncrement) {
                $fields .= $property->fieldName . ',';
                $values .= ':' . $name . ',';
            }
        }
        $fields = rtrim($fields, ',');
        $values = rtrim($values, ',');
        return 'insert into ' . $entityModel->tableName . '(' . $fields . ')values(' . $values . ')';
    }

    /**
     * Возвращает стандартный текст SQL команды обновления записи сущности
     *
     * @param \XEAF\ORM\Models\EntityModel $entityModel Модуль сущности
     *
     * @return string
     */
    public static function defaultUpdateEntitySQL(EntityModel $entityModel): string {
        $fields = '';
        $where  = '';
        foreach ($entityModel->entityProperties as $name => $property) {
            assert($property instanceof EntityProperty);
            if (!$property->primaryKey) {
                if (!$property->readOnly && !$property->autoIncrement) {
                    $fields .= $property->fieldName . '=:' . $name . ',';
                }
            } else {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= $property->fieldName . '=:' . $name;
            }
        }
        $fields = rtrim($fields, ',');
        return 'update ' . $entityModel->tableName . ' set ' . $fields . ' where ' . $where;
    }

    /**
     * Возвращает стандартный текст SQL команды удаления записи сущности
     *
     * @param \XEAF\ORM\Models\EntityModel $entityModel Модуль сущности
     *
     * @return string
     */
    public static function defaultDeleteEntitySQL(EntityModel $entityModel): string {
        $where = '';
        foreach ($entityModel->primaryKeys as $primaryKey) {
            if ($where != '') {
                $where .= ' and ';
            }
            $prop = $entityModel->entityProperties[$primaryKey];
            assert($prop instanceof EntityProperty);
            $where .= $prop->fieldName . '=:' . $primaryKey;
        }
        return 'delete from ' . $entityModel->tableName . ' where ' . $where;
    }
}
