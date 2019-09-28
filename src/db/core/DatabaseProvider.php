<?php

/**
 * DatabaseProvider.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-DB
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\DB\Core;

use PDO;
use Throwable;
use XEAF\API\Core\FactoryObject;
use XEAF\API\Utils\Language;
use XEAF\API\Utils\Logger;
use XEAF\DB\Utils\Exceptions\DatabaseException;

/**
 * Реализует методы достпа к базам данных
 *
 * @package  XEAF\DB\Core
 */
abstract class DatabaseProvider extends FactoryObject {

    /**
     * Реусурс подключения к базе данных
     * @var \PDO
     */
    private $_dbh = null;

    /**
     * Строка подключения к базе данных
     * @var string
     */
    private $_dsn = '';

    /**
     * Пользователь
     * @var string
     */
    private $_userName = '';

    /**
     * Пароль
     * @var string
     */
    private $_password = '';

    /**
     * Дополнительные параметры подключения
     * @var array
     */
    private $_options = [];

    /**
     * Инициализирует значения полей класса
     *
     * @param string      $name     Идентфикатор базы данных
     * @param string      $dsn      Строка подключения к базе данных
     * @param string|null $userName Имя пользователя базы данных
     * @param string|null $password Пароль пользователя базы данных
     * @param array       $options  Дополнительные параметры подключения
     */
    public function __construct(string $name, string $dsn, string $userName = null, string $password = null, array $options = []) {
        parent::__construct($name);
        $this->_dsn      = $dsn;
        $this->_userName = $userName;
        $this->_password = $password;
        $this->_options  = $this->defaultOptions($options);
        Language::loadClassLanguageFile($this->className);
    }

    /**
     * Метод уничтожения объекта класса
     */
    public function __destruct() {
        $this->disconnect();
    }

    /**
     * Открывает подключение к базе данных
     *
     * @return void
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function connect(): void {
        if (!$this->connected()) {
            try {
                $this->_dbh = new PDO($this->_dsn, $this->_userName, $this->_password, $this->_options);
            } catch (Throwable $reason) {
                throw DatabaseException::connectionError($this->name, $reason);
            }
        } else {
            throw DatabaseException::connectionAlreadyOpened($this->name);
        }
    }

    /**
     * Закрывает соединение с базой данных
     *
     * @return void
     */
    public function disconnect(): void {
        if ($this->connected()) {
            try {
                if ($this->inTransaction()) {
                    $this->_dbh->rollBack();
                }
                $this->_dbh = null;
            } catch (Throwable $reason) {
                Logger::error('Database disconnect error.');
            }
        }
    }

    /**
     * Возвращает признак открытого соединения
     *
     * @return bool
     */
    public function connected(): bool {
        return $this->_dbh != null;
    }

    /**
     * Возвращает признак открытой транзакции
     *
     * @return bool
     */
    public function inTransaction(): bool {
        return $this->connected() && $this->_dbh->inTransaction();
    }

    /**
     * Открывает транзакцию
     *
     * @return void
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function startTransaction(): void {
        if (!$this->connected()) {
            throw DatabaseException::noOpenConnection($this->name);
        }
        if (!$this->inTransaction()) {
            try {
                $this->_dbh->beginTransaction();
            } catch (Throwable $reason) {
                throw DatabaseException::transactionError($this->name, $reason);
            }
        } else {
            throw  DatabaseException::transactionAlreadyStarted($this->name);
        }
    }

    /**
     * Подтверждает изменения в транзакции
     *
     * @return void
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function commitTransaction(): void {
        if (!$this->connected()) {
            throw DatabaseException::noOpenConnection($this->name);
        }
        if ($this->inTransaction()) {
            try {
                $this->_dbh->commit();
            } catch (Throwable $reason) {
                throw DatabaseException::transactionError($this->name, $reason);
            }
        }
    }

    /**
     * Откатывает изменения в транзакции
     *
     * @return void
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function rollbackTransaction(): void {
        if (!$this->connected()) {
            throw DatabaseException::noOpenConnection($this->name);
        }
        if ($this->inTransaction()) {
            try {
                $this->_dbh->rollBack();
            } catch (Throwable $reason) {
                throw DatabaseException::transactionError($this->name, $reason);
            }
        }
    }

    /**
     * Возвращает массив записей результата SQL запроса
     *
     * @param string $sql    Текст SQL запроса
     * @param array  $params Массив значений параметров
     * @param int    $count  Количество записей
     * @param int    $offset Смещение
     *
     * @return array
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function select(string $sql, array $params = [], int $count = 0, int $offset = 0): array {
        if (!$this->connected()) {
            throw DatabaseException::noOpenConnection($this->name);
        } else {
            try {
                $qry = $this->limitSQL($sql, $count, $offset);
                $stm = $this->_dbh->prepare($qry);
                $stm->execute($params);
                return $stm->fetchAll();
            } catch (Throwable $reason) {
                throw DatabaseException::sqlQueryError($this->name, $reason);
            }
        }
    }

    /**
     * Возвращает массив значений полей первой записи
     *
     * @param string $sql    Текст SQL запроса
     * @param array  $params Массив значений параметров
     *
     * @return null|array
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function selectFirst(string $sql, array $params = []): ?array {
        $result  = null;
        $records = $this->select($sql, $params, 1);
        if ($records && count($records) > 0) {
            $result = $records[0];
        }
        return $result;
    }

    /**
     * Исполняет SQL команду к базе данных
     *
     * @param string $sql    Текст SQL команды
     * @param array  $params Массив значений параметров
     *
     * @return int Количество затронутых записей
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function execute(string $sql, array $params = []): int {
        if (!$this->inTransaction()) {
            throw DatabaseException::noActiveTransaction($this->name);
        }
        try {
            $stm = $this->_dbh->prepare($sql);
            $stm->execute($params);
            return $stm->rowCount();
        } catch (Throwable $reason) {
            throw DatabaseException::sqlCommandError($this->name, $reason);
        }
    }

    /**
     * Возвращает идентификатор последней созданной записи
     *
     * @return mixed
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function lastInsertId() {
        try {
            return $this->_dbh->lastInsertId();
        } catch (Throwable $reason) {
            throw DatabaseException::sqlCommandError($this->name, $reason);
        }
    }

    /**
     * Возвращает обязательные параметры подключения
     *
     * @param array $options Дополнительные параметры подключения
     *
     * @return array
     */
    protected function defaultOptions(array $options = []): array {
        $result                               = $options;
        $result[PDO::ATTR_CASE]               = PDO::CASE_NATURAL;
        $result[PDO::ATTR_ERRMODE]            = PDO::ERRMODE_EXCEPTION;
        $result[PDO::ATTR_ORACLE_NULLS]       = PDO::NULL_TO_STRING;
        $result[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_ASSOC;
        $result[PDO::MYSQL_ATTR_FOUND_ROWS]   = true;
        return $result;
    }

    /**
     * Возвращает параметры подключения
     *
     * @param array $options Дополнительные параметры
     *
     * @return array
     */
    protected function connectionOptions(array $options): array {
        $result = $this->defaultOptions();
        foreach ($options as $name => $value) {
            $result[$name] = $value;
        }
        return $result;
    }

    /**
     * Добавляет ктексту SQL звароса условия количественного отбора
     *
     * @param string $sql    Исходный текст запроса
     * @param int    $count  Количество записей
     * @param int    $offset Смещение
     *
     * @return string
     */
    protected function limitSQL(string $sql, int $count, int $offset): string {
        $limit = $count == 0 ? '' : "limit $count";
        if ($offset != 0) {
            $limit .= " offset $offset";
        }
        return "$sql $limit";
    }

    /**
     * Возвращает SQL выражение преобразования к верхнему регистру
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    abstract public function toLowerCase(string $expression): string;

    /**
     * Возвращает SQL выражение преобразования к верхнему регистру
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    abstract public function toUpperCase(string $expression): string;

    /**
     * Возвращает SQL выражение форматирования даты
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    abstract public function formatDate(string $expression): string;

    /**
     * Возвращает SQL выражение форматирования времени
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    abstract public function formatTime(string $expression): string;

    /**
     * Возвращает SQL выражение форматирования даты и времени
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    abstract public function formatDateTime(string $expression): string;
}
