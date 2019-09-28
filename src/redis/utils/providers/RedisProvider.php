<?php

/**
 * RedisProvider.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-REDIS
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\Redis\Utils\Providers;

use Redis;
use Throwable;
use XEAF\API\Core\FactoryObject;
use XEAF\API\Core\StdObject;
use XEAF\Redis\Models\Config\RedisConfig;
use XEAF\Redis\Utils\Exceptions\RedisException;

/**
 * Провайдер подключения к серверу Redis
 *
 * @package  XEAF\Redis\Utils\Providers
 */
class RedisProvider extends StdObject {

    /**
     * Имя подключения
     * @var string
     */
    private $_name = FactoryObject::DEFAULT_NAME;

    /**
     * Хост
     * @var string
     */
    private $_host = 'localhost';

    /**
     * Порт
     * @var int
     */
    private $_port = RedisConfig::DEFAULT_PORT;

    /**
     * Данные авторизации
     * @var string
     */
    private $_auth = '';

    /**
     * Индекс базы данных
     * @var int
     */
    private $_dbindex = RedisConfig::DEFAULT_DBINDEX;

    /**
     * Подключение к серверу
     *
     * @noinspection PhpComposerExtensionStubsInspection
     * @var \Redis
     */
    private $_redis = null;

    /**
     * Время жизни сессии в секндах
     * @var int
     */
    private $_ttl = 0;

    /**
     * Конструктор класса
     *
     * @param string $name    Имя подключения
     * @param string $host    Хост
     * @param int    $port    Порт
     * @param string $auth    Данные авторизации
     * @param int    $dbindex Индекс базы данных
     * @param int    $ttl     Максимальное время жизни в секундах
     *
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public function __construct(string $name = FactoryObject::DEFAULT_NAME, string $host = 'localhost', int $port = RedisConfig::DEFAULT_PORT, string $auth = '', int $dbindex = RedisConfig::DEFAULT_DBINDEX, int $ttl = 0) {
        $this->_name    = $name;
        $this->_host    = $host;
        $this->_port    = $port;
        $this->_auth    = $auth;
        $this->_dbindex = $dbindex;
        $this->_ttl     = $ttl;
        $this->_redis   = new Redis();
        $this->connect();
        $this->selectDatabase($this->_dbindex);
    }

    /**
     * Освобождает занятые ресурсы
     */
    public function __destruct() {
        if ($this->_redis) {
            $this->disconnect();
        }
    }

    /**
     * Создает подключение к серверу Redis
     *
     * @return void
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public function connect(): void {
        try {
            $this->_redis->connect($this->_host, $this->_port);
            if ($this->_auth) {
                $this->_redis->auth($this->_auth);
            }
            $this->selectDatabase($this->_dbindex);
        } catch (Throwable $reason) {
            throw  RedisException::connectionError($this->_name, $reason);
        }
    }

    /**
     * Выбор базы данных
     *
     * @param int $dbindex Индекс базы данных
     *
     * @return void
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public function selectDatabase(int $dbindex = 0): void {
        try {
            $this->_redis->select($dbindex);
        } catch (Throwable $reason) {
            throw RedisException::selectDatabaseError($this->_name, $dbindex, $reason);
        }
    }

    /**
     * Получает связанное с ключом значение
     *
     * @param string      $key          Ключ
     * @param string|null $defaultValue Значение по умолчанию
     *
     * @return string|null
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public function get(string $key, string $defaultValue = null): ?string {
        try {
            $data = $this->_redis->get($key);
            return $data === false ? $defaultValue : $data;
        } catch (Throwable $reason) {
            throw RedisException::dataReadingError($this->_name, $reason);
        }
    }

    /**
     * Сохраняет связанное с ключом значение
     *
     * @param string      $key   Ключ
     * @param string|null $value Значение
     * @param int         $ttl   Время жизни в секундах
     *
     * @return void
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public function put(string $key, ?string $value = '', int $ttl = 0): void {
        try {
            if ($ttl == 0) {
                $this->_redis->setex($key, $this->_ttl, $value);
            } else {
                $this->_redis->setex($key, $ttl, $value);
            }
        } catch (Throwable $reason) {
            throw RedisException::dataSavingError($this->_name, $reason);
        }
    }

    /**
     * Удаляет связанное с ключом значение
     *
     * @param string $key Ключ
     *
     * @return void
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public function delete(string $key): void {
        try {
            $this->_redis->delete($key);
        } catch (Throwable $reason) {
            throw RedisException::dataSavingError($this->_name, $reason);
        }
    }

    /**
     * Возвращает признак существования значения
     *
     * @param string $key Ключ
     *
     * @return bool
     */
    public function exists(string $key): bool {
        return $this->_redis->exists($key);
    }

    /**
     * Закрывает подключение к серверу Redis
     *
     * @return void
     */
    public function disconnect(): void {
        $this->_redis->close();
    }
}
