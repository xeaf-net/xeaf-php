<?php

/**
 * EntityQuery.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-ORM
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\ORM\Utils;

use XEAF\API\Core\DataObject;
use XEAF\API\Utils\DateTime;
use XEAF\API\Utils\Exceptions\CollectionException;
use XEAF\API\Utils\Strings;
use XEAF\DB\Utils\Database;
use XEAF\DB\Utils\Exceptions\DatabaseException;
use XEAF\ORM\Core\Entity;
use XEAF\ORM\Core\EntityManager;
use XEAF\ORM\Models\EntityFilterModel;
use XEAF\ORM\Models\EntityFromModel;
use XEAF\ORM\Models\EntityJoinModel;
use XEAF\ORM\Models\EntityOrderModel;
use XEAF\ORM\Models\EntityProperty;
use XEAF\ORM\Utils\Exceptions\EntityException;

/**
 * Построитель запросов к сущностям
 *
 * @package  XEAF\ORM\Utils
 */
class EntityQuery {

    /**
     * Менеджер сущностей
     * @var \XEAF\ORM\Core\EntityManager
     */
    private $_entityManager = null;

    /**
     * Псевдонимы возвращаемых сущностей
     * @var array
     */
    private $_alias = [];

    /**
     * Список сущностей конструкции FROM
     * @var array
     */
    private $_from = [];

    /**
     * Список сущностей объединений
     * @var array
     */
    private $_join = [];

    /**
     * Список условий отбора
     * @var array
     */
    private $_where = [];

    /**
     * Список фильтров
     * @var array
     */
    private $_filter = [];

    /**
     * Порядок сортировки
     * @var array
     */
    private $_order = [];

    /**
     * Конструктор класса
     *
     * @param \XEAF\ORM\Core\EntityManager $entityManager Менеджер сущностей
     */
    public function __construct(EntityManager $entityManager) {
        $this->_entityManager = $entityManager;
    }

    /**
     * Возвращает коллекцию сущностей
     *
     * @param array $args   Аргументы запроса
     * @param int   $limit  Лимит
     * @param int   $offset Смещение
     *
     * @return \XEAF\ORM\Utils\EntityCollection
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function get(array $args = [], int $limit = 0, int $offset = 0): EntityCollection {
        try {
            $sql  = $this->generateSQL();
            $db   = Database::getInstance($this->_entityManager->name);
            $data = $db->select($sql, $args, $limit, $offset);
            if (count($this->_alias) == 1) {
                return $this->processSingleEntity($data);
            } else {
                return $this->processMultipleEntities($data);
            }
        } catch (DatabaseException $dbe) {
            throw EntityException::internalError($dbe);
        }
    }

    /**
     * Возвращает количество записей в выборке
     *
     * @param array $args   Аргументы запроса
     * @param bool  $filter Признак использования условий фильтрации
     *
     * @return int
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function count(array $args = [], bool $filter = true): int {
        try {
            $sql  = $this->generateCountSQL($filter);
            $db   = Database::getInstance($this->_entityManager->name);
            $data = $db->selectFirst($sql, $args);
            return $data['result'] ?? 0;
        } catch (DatabaseException $dbe) {
            throw EntityException::internalError($dbe);
        }
    }

    /**
     * Обрабатывает набор данных с одним типом сущностей
     *
     * @param array $data Набор данных
     *
     * @return \XEAF\ORM\Utils\EntityCollection
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected function processSingleEntity(array &$data): EntityCollection {
        $result    = new EntityCollection();
        $resolve   = $this->resolveAliases();
        $className = $resolve[$this->_alias[0]];
        foreach ($data as $record) {
            $tmp = new $className();
            assert($tmp instanceof Entity);
            $tmp->assignFields($record);
            $entity = $this->_entityManager->watch($tmp);
            $result->push($entity);
        }
        return $result;
    }

    /**
     * Обрабатывает набор данных с множественными сущностями
     *
     * @param array $data Набор данных
     *
     * @return \XEAF\ORM\Utils\EntityCollection
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected function processMultipleEntities(array &$data): EntityCollection {
        $result  = new EntityCollection();
        $resolve = $this->resolveAliases();
        foreach ($data as $record) {
            $item = [];
            foreach ($resolve as $alias => $className) {
                $tmp = new $className();
                assert($tmp instanceof Entity);
                $tmp->assignFields($record);
                $item[$alias] = $this->_entityManager->watch($tmp);
            }
            $entity = new DataObject($item);
            $result->push($entity);
        }
        return $result;
    }

    /**
     * Возвращает имя сущности по псевдониму
     *
     * @param string $alias Псевдоним
     *
     * @return string
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected function entityNameByAlias(string $alias): string {
        foreach ($this->_from as $from) {
            assert($from instanceof EntityFromModel);
            if ($from->alias == $alias) {
                return $from->name;
            }
        }
        foreach ($this->_join as $join) {
            assert($join instanceof EntityJoinModel);
            if ($join->alias == $alias) {
                return $join->name;
            }
        }
        throw EntityException::unknownEntityAlias($alias);
    }

    /**
     * Стоит массив соответствия между псевдонимами и классами сущностей
     *
     * @return array
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected function resolveAliases(): array {
        $result = [];
        foreach ($this->_alias as $alias) {
            $name           = $this->entityNameByAlias($alias);
            $model          = $this->_entityManager->entityModelByName($name);
            $className      = $model->entityClass;
            $result[$alias] = $className;
        }
        return $result;
    }

    /**
     * Возвращает сущности первой удовлетворяющей условиям записи
     *
     * @param array $args Аргументы запроса
     *
     * @return \XEAF\API\Core\DataObject|null
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function getFirst(array $args = []): ?DataObject {
        try {
            $result = null;
            $list   = $this->get($args, 1, 0);
            if ($list->count() > 0) {
                $result = $list->first();
                assert($result instanceof DataObject);
            }
            return $result;
        } catch (CollectionException $ce) {
            throw EntityException::internalError($ce);
        }
    }

    /**
     * Возвращает единичныю сущность первой удовлетворяющей условиям записи
     *
     * @param array $args Аргументы запроса
     *
     * @return \XEAF\ORM\Core\Entity|null
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function getFirstEntity(array $args = []): ?Entity {
        $result = $this->getFirst($args);
        assert($result instanceof Entity);
        return $result;
    }

    /**
     * Добавляет псевдоним возвращаемой сущности
     *
     * @param string $alias Псевдоним возвращаемой сущности
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     */
    public function select(string $alias): EntityQuery {
        if (!in_array($alias, $this->_alias)) {
            $this->_alias[] = $alias;
        }
        return $this;
    }

    /**
     * Добавляет сущность в конструкцию FROM
     *
     * @param string $name  Имя сущности
     * @param string $alias Псевдоним
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     */
    public function from(string $name, string $alias): EntityQuery {
        $this->_from[] = new EntityFromModel($name, $alias);
        return $this;
    }

    /**
     * Добавляет сущность в конструкцию LEFT JOIN
     *
     * @param string $name          Имя сущности
     * @param string $alias         Псевдоним
     * @param string $leftProperty  Свойство слева
     * @param string $rightProperty Свойство справа
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     */
    public function leftJoinOn(string $name, string $alias, string $leftProperty, string $rightProperty): EntityQuery {
        return $this->addJoin(EntityJoinModel::LEFT, $name, $alias, $leftProperty, $rightProperty);
    }

    /**
     * Добавляет сущность в конструкцию RIGHT JOIN
     *
     * @param string $name          Имя сущности
     * @param string $alias         Псевдоним
     * @param string $leftProperty  Свойство слева
     * @param string $rightProperty Свойство справа
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     */
    public function rightJoinOn(string $name, string $alias, string $leftProperty, string $rightProperty): EntityQuery {
        return $this->addJoin(EntityJoinModel::RIGHT, $name, $alias, $leftProperty, $rightProperty);
    }

    /**
     * Добавляет сущность в конструкцию INNER JOIN
     *
     * @param string $name          Имя сущности
     * @param string $alias         Псевдоним
     * @param string $leftProperty  Свойство слева
     * @param string $rightProperty Свойство справа
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     */
    public function innerJoinOn(string $name, string $alias, string $leftProperty, string $rightProperty): EntityQuery {
        return $this->addJoin(EntityJoinModel::INNER, $name, $alias, $leftProperty, $rightProperty);
    }

    /**
     * Добавляет сущность в конструкцию INNER JOIN
     *
     * @param string $name          Имя сущности
     * @param string $alias         Псевдоним
     * @param string $leftProperty  Свойство слева
     * @param string $rightProperty Свойство справа
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     */
    public function outerJoinOn(string $name, string $alias, string $leftProperty, string $rightProperty): EntityQuery {
        return $this->addJoin(EntityJoinModel::OUTER, $name, $alias, $leftProperty, $rightProperty);
    }

    /**
     * Добавляет сущность в конструкцию JOIN
     *
     * @param string $type          Тип соединения
     * @param string $name          Имя сущности
     * @param string $alias         Псевдоним
     * @param string $leftProperty  Свойство слева
     * @param string $rightProperty Свойство справа
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     */
    protected function addJoin(string $type, string $name, string $alias, string $leftProperty, string $rightProperty): EntityQuery {
        list ($a1, $p1) = explode('.', $leftProperty);
        list ($a2, $p2) = explode('.', $rightProperty);
        $this->_join[] = new EntityJoinModel($type, $name, $alias, $a1, $p1, $a2, $p2);
        return $this;
    }

    /**
     * Задает условие отбора данных
     *
     * @param string $condition Условия отбора данных
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     */
    public function where(string $condition): EntityQuery {
        $this->_where = [];
        return $this->andWhere($condition);
    }

    /**
     * Добавляет условия отбора данных
     *
     * @param string $condition Условия отбора данных
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     */
    public function andWhere(string $condition): EntityQuery {
        $this->_where[] = $condition;
        return $this;
    }

    /**
     * Задает условие фильтрации данных
     *
     * @param array       $properties Список свойств
     * @param string|null $value      Значение фильтра
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     */
    public function filter(array $properties, ?string $value): EntityQuery {
        $this->_filter = [];
        $this->addFilter($properties, $value);
        // Logger::debug('filter: ', $this->_filter);
        return $this;
    }

    /**
     * Добавляет условие фильтрации данных
     *
     * @param array       $properties Список свойств
     * @param string|null $value      Значение фильтра
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     */
    public function addFilter(array $properties, ?string $value): EntityQuery {
        if ($value) {
            $this->_filter[] = new EntityFilterModel($properties, $value);
        }
        return $this;
    }

    /**
     * Задает порядок сортировки
     *
     * @param string $property  Свойство сущности
     * @param string $direction Направление
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     */
    public function orderBy(string $property, string $direction = EntityOrderModel::ORDER_ASCENDING): EntityQuery {
        $this->_order = [];
        return $this->andOrderBy($property, $direction);
    }

    /**
     * Добавляет порядок сортировки
     *
     * @param string $property  Свойство сущности
     * @param string $direction Направление
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     */
    public function andOrderBy(string $property, string $direction = EntityOrderModel::ORDER_ASCENDING): EntityQuery {
        list($a, $p) = explode('.', $property);
        $this->_order[] = new EntityOrderModel($a, $p, $direction);
        return $this;
    }

    /**
     * Возвращает текст XQL запроса
     *
     * @param bool $filter Использовать условие фильтрации
     *
     * @return string
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function generateXQL(bool $filter = true): string {
        $result = $this->buildSelectClause() . $this->buildFromClause() . $this->buildJoinClause() . $this->buildWhereClause();
        if ($filter) {
            $result .= $this->buildFilterClause();
        }
        $result .= $this->buildOrderBy();
        return $result;
    }

    /**
     * Строит текст конструкции списка возвращаемых сущностей
     *
     * @return string
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected function buildSelectClause(): string {
        $result = '';
        if ($this->_alias) {
            foreach ($this->_alias as $clause) {
                $result .= $clause . ', ';
            }
        } else {
            throw EntityException::noEntitySpecified();
        }
        return rtrim($result, ', ') . ' ';
    }

    /**
     * Строит текст конструкции FROM
     *
     * @return string
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected function buildFromClause(): string {
        $result = 'from ';
        if ($this->_from) {
            foreach ($this->_from as $clause) {
                assert($clause instanceof EntityFromModel);
                $result .= $clause->name . ' ' . $clause->alias . ', ';
            }
        } else {
            throw EntityException::missedFromClause();
        }
        return rtrim($result, ', ') . ' ';
    }

    /**
     * Строит текст конструкции JOIN
     *
     * @return string
     */
    protected function buildJoinClause(): string {
        $result = '';
        foreach ($this->_join as $clause) {
            assert($clause instanceof EntityJoinModel);
            $result .= $clause->type . ' join ' . $clause->name . ' ' . $clause->alias;
            $result .= ' on ' . $clause->leftAlias . '.' . $clause->leftProperty . '==' . $clause->rightAlias . '.' . $clause->rightProperty . ' ';
        }
        return $result;
    }

    /**
     * Строит текст конструкции WHERE
     *
     * @return string
     */
    protected function buildWhereClause(): string {
        $result = '';
        $i      = 0;
        $n      = count($this->_where) - 1;
        foreach ($this->_where as $clause) {
            if ($i == 0) {
                $result = 'where ';
            }
            $result .= '(' . $clause . ')';
            if ($i++ != $n) {
                $result .= ' && ';
            }
        }
        return $result . ' ';
    }

    /**
     * Строит текст конфтрукции фильтрации данных
     *
     * @return string
     */
    protected function buildFilterClause(): string {
        $result = '';
        foreach ($this->_filter as $filter) {
            assert($filter instanceof EntityFilterModel);
            if (!Strings::isEmpty($filter->filterValue)) {
                $tmp   = '';
                $value = '%' . $filter->filterValue . '%';
                foreach ($filter->filterProperties as $property) {
                    if ($tmp != '') {
                        $tmp .= ' || ';
                    }
                    // $tmp .= "to_char(" . $property . ", 'YYYY-MM-DD') %% '" . $value . "'";
                    $tmp .= $property . " %% '" . $value . "'";
                }
                if ($tmp != '') {
                    if ($result != '') {
                        $result .= ' && ';
                    }
                    $result .= $tmp;
                }
            }
        }
        if ($result != '' && count($this->_where) > 0) {
            $result = ' && (' . $result . ')';
        }
        return $result;
    }

    /**
     * Строит текст конструкции ORDER BY
     *
     * @return string
     */
    protected function buildOrderBy(): string {
        $result = '';
        $i      = 0;
        foreach ($this->_order as $clause) {
            assert($clause instanceof EntityOrderModel);
            if ($i++ == 0) {
                $result = 'order by ';
            }
            $result .= $clause->alias . '.' . $clause->property . ' ' . $clause->direction . ', ';
        }
        return rtrim($result, ', ') . ' ';
    }

    /**
     * Возвращает текст SQL запроса
     *
     * @param bool $filter Признак использования условий фильтрации
     *
     * @return string
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function generateSQL(bool $filter = true): string {
        $xql = $this->generateXQL($filter);
        return EntityParser::generateSQL($xql, $this->_entityManager);
    }

    /**
     * Возвращает текст SQL запроса количества записей
     *
     * @param bool $filter Признак использования условий фильтрации
     *
     * @return string
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function generateCountSQL(bool $filter = true): string {
        $xql = $this->generateXQL($filter);
        return EntityParser::generateCountSQL($xql, $this->_entityManager);
    }

    /**
     * Конвертация параметра даты
     *
     * @param int $date Значение параметра
     *
     * @return string
     */
    public static function dateParam(int $date): string {
        return DateTime::dateToSQL($date);
    }

    /**
     * Конвертация параметра даты и времени
     *
     * @param int $dateTime Значение параметра
     *
     * @return string
     */
    public static function dateTimeParam(int $dateTime): string {
        return DateTime::dateTimeToSQL($dateTime);
    }

    /**
     * Конвертация логического параметра
     *
     * @param bool $flag Значение параметра
     *
     * @return string
     */
    public static function boolParam(bool $flag): string {
        return $flag ? '1' : '0';
    }

    /**
     * Преобразует значение параметра с учетом типа данных
     *
     * @param mixed $value    Значение параметра
     * @param int   $dataType Тип данных
     *
     * @return string|null
     */
    public static function convertParameter($value, int $dataType): ?string {
        switch ($dataType) {
            case EntityProperty::DT_DATE:
                $result = self::dateParam($value);
                break;
            case EntityProperty::DT_DATETIME:
                $result = self::dateTimeParam($value);
                break;
            case EntityProperty::DT_BOOL:
                $result = self::boolParam($value);
                break;
            default:
                $result = $value;
                break;
        }
        return $result;
    }
}
