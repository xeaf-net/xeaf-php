<?php

/**
 * SessionException.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils\Exceptions;

use XEAF\API\Core\Exception;

/**
 * Исключения при работе с сессиями
 *
 * @package  XEAF\API\Utils\Exceptions
 */
class SessionException extends Exception {

    /**
     * Некорректный параметры конфигурации сессии
     *
     * @return \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function invalidSessionConfiguration(): self {
        return new self('Invalid session configuration.');
    }

    /**
     * Неизвестный провайдер сессии
     *
     * @param string $name Идентификатор провайдера
     *
     * @return \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function unknownSessionProvider(string $name): self {
        return new self('Unknown session provider [%s].', [$name]);
    }

    /**
     * Сессия не была должным образом открыта
     *
     * @return \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function sessionNotOpened(): self {
        return new self('Session was not propertly opened.');
    }
}
