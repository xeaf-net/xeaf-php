<?php

/**
 * RedisConfig.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-REDIS
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\Redis\Models\Config;

use XEAF\API\Core\DataModel;
use XEAF\API\Utils\DateTime;
use XEAF\API\Utils\Strings;

/**
 * Содержит параметры конфигурации подключения к серверу Redis
 *
 * @property string $host    Хост
 * @property int    $port    Порт
 * @property string $auth    Данные для авторизации
 * @property int    $dbindex Индекс базы данных
 * @property int    $ttl     Максимальное время жизни в секундах
 *
 * @package  XEAF\Redis\Models\Config
 */
class RedisConfig extends DataModel {

    /**
     * Порт Redis по умолчанию
     */
    public const DEFAULT_PORT = 6379;

    /**
     * Индекс базы данных по умолчанию
     */
    public const DEFAULT_DBINDEX = 0;

    /**
     * Максимальное время жизни в секундах по умолчанию
     */
    public const DEFAULT_TTL = DateTime::SECONDS_PER_DAY;

    /**
     * Хост
     * @var string
     */
    private $_host = 'localhost';

    /**
     * Порт
     * @var int
     */
    private $_port = self::DEFAULT_PORT;

    /**
     * Данные авторизации
     * @var string
     */
    private $_auth = '';

    /**
     * Индекс базы данных
     * @var int
     */
    private $_dbindex = self::DEFAULT_DBINDEX;

    /**
     * Максимальное время жизни в секундах
     * @var int
     */
    private $_ttl = self::DEFAULT_TTL;

    /**
     * Конструктор класса
     *
     * @param object $data Неразобранные параметры конфигурации
     */
    public function __construct(object $data) {
        parent::__construct();
        $port    = Strings::stringToInteger($data->{'port'} ?? null, self::DEFAULT_PORT);
        $dbindex = Strings::stringToInteger($data->{'dbindex'} ?? null, self::DEFAULT_DBINDEX);
        $ttl     = Strings::stringToInteger($data->{'ttl'} ?? null, self::DEFAULT_TTL);
        $this->assignVarIfNotNull($this->_host, $data->{'host'} ?? null);
        $this->assignVarIfNotNull($this->_auth, $data->{'auth'} ?? null);
        $this->assignVarIfNotNull($this->_port, $port);
        $this->assignVarIfNotNull($this->_dbindex, $dbindex);
        $this->assignVarIfNotNull($this->_ttl, $ttl);
    }

    /**
     * Возвращает хост
     *
     * @return string
     */
    public function getHost(): string {
        return $this->_host;
    }

    /**
     * Возвращает порт
     *
     * @return int
     */
    public function getPort(): int {
        return $this->_port;
    }

    /**
     * Возвращает данные авторизации
     *
     * @return string
     */
    public function getAuth(): string {
        return $this->_auth;
    }

    /**
     * Возвращает индекс базы данных
     *
     * @return int
     */
    public function getDbindex(): int {
        return $this->_dbindex;
    }

    /**
     * Возвращает максимальное время жизни в секундах
     * @return int
     */
    public function getTtl(): int {
        return $this->_ttl;
    }
}
