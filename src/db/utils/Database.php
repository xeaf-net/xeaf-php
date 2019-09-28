<?php

/**
 * Database.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-DB
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\DB\Utils;

use Throwable;
use XEAF\API\App\Factory;
use XEAF\API\Core\FactoryObject;
use XEAF\DB\Core\DatabaseProvider;
use XEAF\DB\Models\Config\DatabaseConfig;
use XEAF\DB\Utils\Exceptions\DatabaseException;

/**
 * Реализует методы работы с базой данных
 *
 * @package  XEAF\DB\Utils\Database
 */
class Database extends FactoryObject {

    /**
     * Идентификатор секции файла конфигурации
     */
    protected const CONFIG_SECTION = 'database';

    /**
     * Провайдер подключения к базе данных
     * @var \XEAF\DB\Core\DatabaseProvider
     */
    private $_provider = null;

    /**
     * Массив зарегистрированных провайдеров
     * @var array
     */
    private static $_registeredProviders = [];

    /**
     * Конструктор класса
     *
     * @param string $name Идентификатор объекта
     *
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function __construct(string $name) {
        parent::__construct($name);
        $config          = $this->loadConfig($name);
        $this->_provider = $this->createProvider($name, $config);
    }

    /**
     * Загружает параметры конфигурации
     *
     * @param string $name Имя объекта
     *
     * @return \XEAF\DB\Models\Config\DatabaseConfig
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    protected function loadConfig(string $name): DatabaseConfig {
        try {
            $config = Factory::getConfiguration();
            $data   = $config->getNamedSection(self::CONFIG_SECTION, $name);
            return new DatabaseConfig($data);
        } catch (Throwable $reason) {
            throw DatabaseException::configurationError($name, $reason);
        }
    }

    /**
     * Разрывает соединение с базой данных
     */
    public function __destruct() {
        $this->disconnect();
    }

    /**
     * Создает объект провайдера подключения к базе данных
     *
     * @param string                                 $name   Идентификатор базы данных
     * @param \XEAF\DB\Models\Config\DatabaseConfig $config Параметры конфигурации
     *
     * @return \XEAF\DB\Core\DatabaseProvider
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    protected function createProvider(string $name, DatabaseConfig $config): DatabaseProvider {
        $provider  = $this->providerName($config->dsn);
        $className = self::$_registeredProviders[$provider] ?? null;
        if ($className) {
            return new $className($name, $config->dsn, $config->user, $config->password);
        }
        throw DatabaseException::unknownDatabaseProvider($provider);
    }

    /**
     * Возвращает идентификатор провайдера из строки подключения
     *
     * @param string $dns Строка подключения к базе данных
     *
     * @return string
     */
    protected function providerName(string $dns): string {
        $arr = explode(':', $dns);
        return $arr[0];
    }

    /**
     * Открывает подключение к базе данных
     *
     * @return void
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function connect(): void {
        $this->_provider->connect();
    }

    /**
     * закрывает соединение с базой данных
     *
     * @return void
     */
    public function disconnect(): void {
        $this->_provider->disconnect();
    }

    /**
     * Возвращает признак открытого соединения
     *
     * @return bool
     */
    public function connected(): bool {
        return $this->_provider->connected();
    }

    /**
     * Возвращает признак открытой транзакции
     *
     * @return bool
     */
    public function inTransaction(): bool {
        return $this->_provider->inTransaction();
    }

    /**
     * Открывает транзакцию
     *
     * @param bool $useTransaction Признак использования транзакции
     *
     * @return void
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function startTransaction(bool $useTransaction = true): void {
        if ($useTransaction) {
            $this->_provider->startTransaction();
        }
    }

    /**
     * Подтверждает изменения в транзакции
     *
     * @param bool $useTransaction Признак использования транзакции
     *
     * @return void
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function commit(bool $useTransaction = true): void {
        if ($useTransaction) {
            $this->_provider->commitTransaction();
        }
    }

    /**
     * Откатывает изменения в транзакции
     *
     * @param bool $useTransaction Признак использования транзакции
     *
     * @return void
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function rollback(bool $useTransaction = true): void {
        if ($useTransaction) {
            $this->_provider->rollbackTransaction();
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
        return $this->_provider->select($sql, $params, $count, $offset);
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
        return $this->_provider->selectFirst($sql, $params);
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
        return $this->_provider->execute($sql, $params);
    }

    /**
     * Возвращает идентификатор последней созданной записи
     *
     * @return mixed
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function lastInsertId() {
        return $this->_provider->lastInsertId();
    }

    /**
     * Выполняет блок действий внутри транзакции и возвращает исключение или null
     *
     * @param callable $block Блок действий
     *
     * @return void
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function executeInTransaction(callable $block): void {
        $useTransaction = !$this->inTransaction();
        try {
            $this->startTransaction($useTransaction);
            $block();
            $this->commit($useTransaction);
        } catch (DatabaseException $exception) {
            $this->rollback($useTransaction);
            throw $exception;
        }
    }

    /**
     * Возвращает SQL выражение преобразования к верхнему регистру
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    public function toLowerCase(string $expression): string {
        return $this->_provider->toLowerCase($expression);
    }

    /**
     * Возвращает SQL выражение преобразования к верхнему регистру
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    public function toUpperCase(string $expression): string {
        return $this->_provider->toUpperCase($expression);
    }

    /**
     * Возвращает SQL выражение форматирования даты
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    public function formatDate(string $expression): string {
        return $this->_provider->formatDate($expression);
    }

    /**
     * Возвращает SQL выражение форматирования времени
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    public function formatTime(string $expression): string {
        return $this->_provider->formatTime($expression);
    }

    /**
     * Возвращает SQL выражение форматирования даты и времени
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    public function formatDateTime(string $expression): string {
        return $this->_provider->formatDateTime($expression);
    }

    /**
     * Регистрирует новый провайдер подключения к базе данных
     *
     * @param string $name      Идентификатор провайдера
     * @param string $className Идентификатор класса провайдера
     *
     * @return void
     */
    public static function registerProvider(string $name, string $className): void {
        self::$_registeredProviders[$name] = $className;
    }

    /**
     * Возвращает именованный объект подключения к базе данных
     *
     * @param string $name Имя объекта
     *
     * @return \XEAF\DB\Utils\Database
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public static function getInstance(string $name = FactoryObject::DEFAULT_NAME): self {
        $result = Factory::getFactoryObject(self::class, $name);
        assert($result instanceof Database);
        if (!$result->connected()) {
            $result->connect();
        }
        return $result;
    }
}
