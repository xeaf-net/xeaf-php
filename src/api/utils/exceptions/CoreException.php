<?php

/**
 * CoreException.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
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
 * Исключения ядра библиотеки классов
 *
 * @package XEAF\API\Utils\Exceptions
 */
class CoreException extends Exception {

    /**
     * Обращение к неопределенному методу
     */
    public const UNKNOWN_METHOD = 'COR-001';

    /**
     * Попытка чтения значения неопределенного свойства
     */
    public const UNKNOWN_READABLE_PROPERTY = 'COR-002';

    /**
     * Попытка задания значения неопределенного свойства
     */
    public const UNKNOWN_WRITABLE_PROPERTY = 'COR-003';

    /**
     * Внутренняя ошибка отражения
     */
    public const REFLECTION_ERROR = 'COR-004';

    /**
     * Обращение к неопределенному методу
     *
     * @param string $className Имя класса
     * @param string $method    Имя метода
     *
     * @return \XEAF\API\Utils\Exceptions\CoreException
     */
    public static function unknownMethod(string $className, string $method): self {
        return new self(self::UNKNOWN_METHOD, 'Call to unknown method [%s::%s()].', [$className, $method]);
    }

    /**
     * Попытка чтения значения неопределенного свойства
     *
     * @param string $className Имя класса
     * @param string $property  Имя свойства
     *
     * @return \XEAF\API\Utils\Exceptions\CoreException
     */
    public static function unknownReadableProperty(string $className, string $property): self {
        return new self(self::UNKNOWN_READABLE_PROPERTY, 'Property [%s::%s] is undefined or write only.', [$className, $property]);
    }

    /**
     * Попытка заданич значения неопределенного свойства
     *
     * @param string $className Имя класса
     * @param string $property  Имя свойства
     *
     * @return \XEAF\API\Utils\Exceptions\CoreException
     */
    public static function unknownWritableProperty(string $className, string $property): self {
        return new self(self::UNKNOWN_WRITABLE_PROPERTY, 'Property [%s::%s] is undefined or read only.', [$className, $property]);
    }

    /**
     * Внутренняя ошибка отражения
     *
     * @param \ReflectionException $previous Причина возикновения ошибки
     *
     * @return \XEAF\API\Utils\Exceptions\CoreException
     */
    public static function reflectionError(ReflectionException $previous): self {
        return new self(self::REFLECTION_ERROR, 'Internal reflection error.', [], $previous);
    }
}
