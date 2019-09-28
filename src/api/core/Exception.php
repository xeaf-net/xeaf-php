<?php

/**
 * Exception.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Core;

use Throwable;

/**
 * Базовый класс для всех исключений проекта
 *
 * @package  XEAF\API\Core
 */
abstract class Exception extends \Exception {

    /**
     * Код ошибки (не используется)
     */
    protected const ERROR_CODE = 0;

    /**
     * Конструктор класса
     *
     * @param string          $message  Текст сообщения
     * @param array           $args     Аргументы текста сообщения
     * @param \Throwable|null $previous Причина исключения
     */
    protected function __construct($message = '', array $args = [], Throwable $previous = null) {
        parent::__construct(vsprintf($message, $args), self::ERROR_CODE, $previous);
    }
}
