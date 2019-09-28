<?php

/**
 * CoreException.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils\Exceptions;

use ReflectionException;
use XEAF\API\Core\Exception;

/**
 * Исключения классов ядра проекта
 *
 * @package  XEAF\API\Utils\Exceptions
 */
class CoreException extends Exception {

    /**
     * Попытка вызова неизвестного метода класса
     *
     * @param string $className Имя класса
     * @param string $name      Имя метода
     *
     * @return \XEAF\API\Utils\Exceptions\CoreException
     */
    public static function undefinedMethod(string $className, string $name): self {
        return new self('Call to undefined method [%s::%s].', [$className, $name]);
    }

    /**
     * Обращение к неизвестному свойству класса
     *
     * @param string $className Имя класса
     * @param string $name      Имя свойства
     *
     * @return \XEAF\API\Utils\Exceptions\CoreException
     */
    public static function undefinedProperty(string $className, string $name): self {
        return new self('Undefined property [%s::%s].', [$className, $name]);
    }

    /**
     * Внутренняя ошибка при работе с отражениями
     *
     * @param \ReflectionException $previous Причина исключения
     *
     * @return \XEAF\API\Utils\Exceptions\CoreException
     */
    public static function internalReflectionError(ReflectionException $previous): self {
        return new self('Internal reflection error.', [], $previous);
    }
}

