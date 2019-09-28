<?php

/**
 * Reflection.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use XEAF\API\Utils\Exceptions\CoreException;

/**
 * Реализует методы работы с отражениями
 *
 * @package  XEAF\API\Utils
 */
class Reflection {

    /**
     * Возвращает имя файла реализации класса
     *
     * @param string $className Имя класса
     *
     * @return string
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    public static function classFileName(string $className): string {
        try {
            $ref = new ReflectionClass($className);
            return $ref->getFileName();
        } catch (ReflectionException $reason) {
            throw CoreException::internalReflectionError($reason);
        }
    }

    /**
     * Возвращает массив публичных свойств класса
     *
     * @param string $className Имя класса
     *
     * @return array
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    public static function classPublicProperties(string $className): array {
        try {
            $ref = new ReflectionClass($className);
            return $ref->getProperties(ReflectionProperty::IS_PUBLIC | !ReflectionProperty::IS_STATIC);
        } catch (ReflectionException $reason) {
            throw CoreException::internalReflectionError($reason);
        }
    }
}
