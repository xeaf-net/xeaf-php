<?php

/**
 * FileSession.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils\Sessions;

use XEAF\API\App\Factory;
use XEAF\API\Core\SessionProvider;

/**
 * Провайдер файловой сессии
 *
 * @package  XEAF\API\Utils\Sessions
 */
class FileSession extends SessionProvider {

    /**
     * Идентификатор провайдера
     */
    public const PROVIDER_NAME = 'files';

    /**
     * Префикс переменной сессии
     */
    protected const PREFIX = 'session';

    /**
     * Файловое хранилище
     * @var \XEAF\API\Utils\Storage\FileStorage
     */
    private $_fileStorage = null;

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
        $this->_fileStorage = Factory::getFileStorage($this->name);
        $this->_sessionKey  = self::PREFIX . '-' . $this->getSessionId();
    }

    /**
     * Загружает данные сессии
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public function loadSessionData(): void {
        $data = $this->_fileStorage->get($this->_sessionKey, []);
        foreach ($data as $key => $value) {
            $this->put($key, $value);
        }
    }

    /**
     * Сохраняет данные сессии
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public function saveSessionData(): void {
        $this->_fileStorage->put($this->_sessionKey, $this->storedValues());
    }

    /**
     * Удаляет данные сессии
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public function deleteSessionData(): void {
        parent::deleteSessionData();
        $this->_fileStorage->delete($this->_sessionKey);
    }
}
