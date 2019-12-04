<?php

/**
 * RedisStorage.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-REDIS
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\Redis\Utils\Storage;

use XEAF\API\App\Factory;
use XEAF\API\Core\Storage;
use XEAF\API\Utils\Serializer;
use XEAF\API\Utils\Strings;
use XEAF\Redis\Models\Config\RedisConfig;
use XEAF\Redis\Utils\Providers\RedisProvider;

/**
 * Реализует методы хранилища данных на сервере Redis
 *
 * @package  XEAF\Redis\Utils\Storage
 */
class RedisStorage extends Storage {

    /**
     * Идентификатор раздера параметров конфигурации
     */
    protected const CONFIG_SECTION = 'redis';

    /**
     * Провайдер подключения к серверу Redis
     * @var \XEAF\Redis\Utils\Providers\RedisProvider
     */
    private $_provider = null;

    /**
     * Конструктор класса
     *
     * @param string $name Имя хранилища
     *
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public function __construct(string $name = self::DEFAULT_NAME) {
        parent::__construct($name);
        $config          = $this->loadConfig($name);
        $this->_provider = new RedisProvider($name, $config->host, $config->port, $config->auth, $config->dbindex, $config->ttl);
    }

    /**
     * Возвращает ранее сохраненное значение
     *
     * @param string     $key          Ключ
     * @param mixed|null $defaultValue Значение по умолчанию
     *
     * @return mixed
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public function get(string $key, $defaultValue = null) {
        $result = $defaultValue;
        $data   = $this->getString($key);
        if ($data) {
            $result = Serializer::unserialize($data);
        }
        return $result;
    }

    /**
     * Возвращает ранее сохраненное строковое значение
     *
     * @param string     $key          Ключ
     * @param mixed|null $defaultValue Значение по умолчанию
     *
     * @return string|null
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public function getString(string $key, $defaultValue = null): ?string {
        $result = parent::get($key);
        if (!$result) {
            $result = $this->_provider->get($key, $defaultValue);
        }
        return $result;
    }

    /**
     * Возвращает ранее сохраненное целочисленное значение
     *
     * @param string $key          Ключ
     * @param int    $defaultValue Значение по умолчанию
     *
     * @return int
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public function getInteger(string $key, $defaultValue = 0): int {
        $buf = $this->getString($key, $defaultValue);
        return Strings::stringToInteger($buf, $defaultValue);
    }

    /**
     * Сохраняет значение
     *
     * @param string     $key   Ключ
     * @param mixed|null $value Значение
     * @param int        $ttl   Время жизни в секундах
     *
     * @return void
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public function put(string $key, $value = null, int $ttl = 0): void {
        $data = Serializer::serialize($value);
        $this->putString($key, $data, $ttl);
    }

    /**
     * Сохраняет строковое значение
     *
     * @param string      $key   Ключ
     * @param string|null $value Значение
     * @param int         $ttl   Время жизни в секундах
     *
     * @return void
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public function putString(string $key, $value = null, int $ttl = 0): void {
        parent::put($key, $value);
        $this->_provider->put($key, $value, $ttl);
    }

    /**
     * Сохраняет целочисленное значение
     *
     * @param string $key   Ключ
     * @param int    $value Значение
     * @param int    $ttl   Время жизни в секундах
     *
     * @return void
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public function putInteger(string $key, int $value = 0, int $ttl = 0): void {
        $this->putString($key, $value, $ttl);
    }

    /**
     * Удаляет ранее установленное значение
     *
     * @param string $key Ключ
     *
     * @return void
     * @throws \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public function delete(string $key): void {
        parent::delete($key);
        $this->_provider->delete($key);
    }

    /**
     * Возвращает признак существования значения
     *
     * @param string $key Ключ
     *
     * @return bool
     */
    public function exists(string $key): bool {
        $result = parent::exists($key);
        if (!$result) {
            $this->_provider->exists($key);
        }
        return $result;
    }

    /**
     * Возвращает объект параметров конфигурации
     *
     * @param string $name Имя объекта
     *
     * @return \XEAF\Redis\Models\Config\RedisConfig
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    protected function loadConfig(string $name): RedisConfig {
        $config = Factory::getConfiguration();
        $data   = $config->getNamedSection(self::CONFIG_SECTION, $name);
        return new RedisConfig($data);
    }

    /**
     * Создает объект хранилища
     *
     * @param string $name Имя объекта
     *
     * @return \XEAF\Redis\Utils\Storage\RedisStorage
     */
    public static function getInstance(string $name): RedisStorage {
        $result = Factory::getFactoryObject(self::class, $name);
        assert($result instanceof RedisStorage);
        return $result;
    }
}
