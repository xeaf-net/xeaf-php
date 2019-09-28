<?php

/**
 * SessionProvider.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Core;

use XEAF\API\App\Factory;
use XEAF\API\Utils\Crypto;
use XEAF\API\Utils\Strings;

/**
 * Реализует методы провайдера сессий
 *
 * @package  XEAF\API\Core
 */
abstract class SessionProvider extends Storage {

    /**
     * Идентификатор сессии
     * @var string
     */
    private $_sessionId = '';

    /**
     * Возвращает идентификатор сессии
     *
     * @return string
     */
    public function getSessionId(): string {
        if (!$this->_sessionId) {
            $this->_sessionId = Factory::getParameters()->sessionId;
            if (Strings::isEmpty($this->_sessionId)) {
                $this->_sessionId = Crypto::generateUUIDv4();
            }
        }
        return $this->_sessionId;
    }

    /**
     * Загружает данные сессии
     *
     * @return void
     */
    abstract public function loadSessionData(): void;

    /**
     * Сохраняет данные сессии
     *
     * @return void
     */
    abstract public function saveSessionData(): void;

    /**
     * Удаляет данные сессии
     *
     * @return void
     */
    public function deleteSessionData(): void {
        $this->clear();
    }
}
