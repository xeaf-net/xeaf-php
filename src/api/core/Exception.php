<?php

/**
 * Exception.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Core;

use Throwable;

/**
 * Реализует базовые методы исключений проекта
 *
 * @package XEAF\API\Core
 */
abstract class Exception extends \Exception {

    /**
     * Конструктор класса
     *
     * @param string          $code     Числовой код ошибки
     * @param string          $format   Формат текста сообщения
     * @param array           $args     Аргументы текста сообщения
     * @param \Throwable|null $previous Причина возникновения исключения
     */
    protected function __construct(string $code, string $format, array $args = [], Throwable $previous = null) {
        $message = vsprintf($format, $args);
        parent::__construct($message, 0, $previous);
        $this->code = $code;
    }

    /**
     * Возвращает строковое представление объекта
     *
     * @return string|void
     */
    public function __toString(): string {
        return vsprintf("[%s] %s",[$this->getCode(),parent::getMessage()]);
    }
}
