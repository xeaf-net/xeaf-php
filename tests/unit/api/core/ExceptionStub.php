<?php

/**
 * ExceptionStub.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace Tests\XEAF\API\Core;

use Throwable;
use XEAF\API\Core\Exception;

/**
 * Подстановочный класс для тестирования XEAF\API\Core\Exception
 *
 * @package Tests\XEAF\API\Core
 */
class ExceptionStub extends Exception {

    /**
     * Конструктор класса
     *
     * @param string          $code     Числовой код ошибки
     * @param string          $format   Формат текста сообщения
     * @param array           $args     Аргументы текста сообщения
     * @param \Throwable|null $previous Причина возникновения исключения
     */
    public function __construct(string $code, string $format, array $args = [], Throwable $previous = null) {
        parent::__construct($code, $format, $args, $previous);
    }
}
