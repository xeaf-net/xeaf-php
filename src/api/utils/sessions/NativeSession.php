<?php

/**
 * NativeSession.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils\Sessions;

use XEAF\API\Core\SessionProvider;
use XEAF\API\Utils\Parameters;
use XEAF\API\Utils\Strings;

/**
 * Провайдер нативной сессии
 *
 * @package  XEAF\API\Utils\Sessions
 */
class NativeSession extends SessionProvider {

    /**
     * Идентификатор провайдера
     */
    public const PROVIDER_NAME = 'native';

    /**
     * Возвращает идентификатор сессии
     *
     * @return string
     */
    public function getSessionId(): string {
        $result = $this->get(Parameters::SESSION_ID_NAME);
        if (Strings::isEmpty($result)) {
            $result = parent::getSessionId();
            $this->put(Parameters::SESSION_ID_NAME, $result);
        }
        return $result;
    }

    /**
     * Загружает данные сессии
     *
     * @return void
     */
    public function loadSessionData(): void {
        session_start();
        foreach ($_SESSION as $key => $value) {
            $this->put($key, $value);
        }
        session_write_close();
    }

    /**
     * Сохраняет данные сессии
     *
     * @return void
     */
    public function saveSessionData(): void {
        session_start();
        $data = $this->storedValues();
        foreach ($data as $key => $value) {
            $_SESSION[$key] = $value;
        }
        $_SESSION[Parameters::SESSION_ID_NAME] = $this->getSessionId();
        session_write_close();
    }

    /**
     * Удаляет данные сессии
     *
     * @return void
     */
    public function deleteSessionData(): void {
        session_start();
        $data = $this->storedValues();
        foreach ($data as $key => $value) {
            if ($key != Parameters::SESSION_ID_NAME) {
                unset($_SESSION[$key]);
            }
        }
        session_write_close();
        parent::deleteSessionData();
    }
}
