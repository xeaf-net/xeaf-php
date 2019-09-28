<?php

/**
 * RedisSession.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-REDIS
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\Redis\Utils\Sessions;

use XEAF\API\Core\SessionProvider;
use XEAF\Redis\Utils\Storage\RedisStorage;

/**
 * Провайдер сессий на основе сервера Redis
 *
 * @package  XEAF\Redis\Utils\Sessions
 */
class RedisSession extends SessionProvider {

    /**
     * Идентификатор провайдера
     */
    public const PROVIDER_NAME = 'redis';

    /**
     * Префикс переменной сессии
     */
    protected const PREFIX = 'session';

    /**
     * Хранилище Redis
     * @var \XEAF\Redis\Utils\Storage\RedisStorage
     */
    private $_redis = null;

    /**
     * Ключ переменной сессии
     * @var string
     */
    private $_sessionKey = '';

    /**
     * Конструктор класса
     *
     * @param string $name Имя объекта
     */
    public function __construct(string $name) {
        parent::__construct($name);
        $this->_redis      = RedisStorage::getInstance($this->name);
        $this->_sessionKey = self::PREFIX . '-' . $this->getSessionId();
    }

    /**
     * Загружает данные сессии
     *
     * @return void
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public function loadSessionData(): void {
        $data = $this->_redis->get($this->_sessionKey, []);
        foreach ($data as $key => $value) {
            $this->put($key, $value);
        }
    }

    /**
     * Сохраняет данные сессии
     *
     * @return void
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public function saveSessionData(): void {
        $this->_redis->put($this->_sessionKey, $this->storedValues());
    }

    /**
     * Удаляет данные сессии
     *
     * @return void
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public function deleteSessionData(): void {
        parent::deleteSessionData();
        $this->_redis->delete($this->_sessionKey);
    }
}
