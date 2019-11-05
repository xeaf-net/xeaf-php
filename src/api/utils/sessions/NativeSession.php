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
        if ($this->internalAllowSession()) {
            $this->internalSessionStart();
            foreach ($_SESSION as $key => $value) {
                $this->put($key, $value);
            }
            $this->internalSessionWriteClose();
        }
    }

    /**
     * Сохраняет данные сессии
     *
     * @return void
     */
    public function saveSessionData(): void {
        if ($this->internalAllowSession()) {
            $this->internalSessionStart();
            $data = $this->storedValues();
            foreach ($data as $key => $value) {
                $_SESSION[$key] = $value;
            }
            $_SESSION[Parameters::SESSION_ID_NAME] = $this->getSessionId();
            $this->internalSessionWriteClose();
        }
    }

    /**
     * Удаляет данные сессии
     *
     * @return void
     */
    public function deleteSessionData(): void {
        if ($this->internalAllowSession()) {
            $this->internalSessionStart();
            $data = $this->storedValues();
            foreach ($data as $key => $value) {
                if ($key != Parameters::SESSION_ID_NAME) {
                    unset($_SESSION[$key]);
                }
            }
            $this->internalSessionWriteClose();
        }
        parent::deleteSessionData();
    }

    /**
     * Возвращает признак разрешения работы с сессией
     *
     * @return bool
     */
    private function internalAllowSession(): bool {
        return !defined('STDIN');
    }

    /**
     * Вунтренняя функция запуска механизма сессии
     *
     * @return void
     */
    private function internalSessionStart(): void {
        if ($this->internalAllowSession()) {
            session_start();
        }
    }

    /**
     * Вунтренняя функция закрытия сессии
     *
     * @return void
     */
    private function internalSessionWriteClose(): void {
        if ($this->internalAllowSession()) {
            session_write_close();
        }
    }
}
