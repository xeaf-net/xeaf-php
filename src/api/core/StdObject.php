<?php

/**
 * StdObject.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Core;

use XEAF\API\Utils\Exceptions\CoreException;

/**
 * Реализует базовые методы свойств объектов
 *
 * @property-read string $className Идентификатор класса объекта
 *
 * @package XEAF\API\Core
 */
abstract class StdObject {

    /**
     * Префикс метда геттера
     */
    private const GETTER_PREFIX = 'get';

    /**
     * Возвращает идентификатор класса объекта
     *
     * @return string
     */
    public function getClassName(): string {
        return get_class($this);
    }

    /**
     * Возвращает признак существования метода
     *
     * @param string $name Имя метода
     *
     * @return bool
     */
    public function methodExists(string $name): bool {
        return method_exists($this, $name);
    }

    /**
     * Возвращает признак возможности чтения значения свойтства
     *
     * @param string $name Имя свойтства
     *
     * @return bool
     */
    public function propertyReadable(string $name): bool {
        $result = property_exists($this, $name);
        if (!$result) {
            $method = $this->getterName($name);
            $result = $this->methodExists($method);
        }
        return $result;
    }

    /**
     * Возвращает значение свойства класса
     *
     * @param string $name Имя свойства
     *
     * @return mixed
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    public function __get(string $name) {
        $method = $this->getterName($name);
        if ($this->methodExists($method)) {
            return $this->$method();
        }
        throw CoreException::unknownProperty($this->getClassName(), $name);
    }

    /**
     * Возвращает имя метода геттера для заданного свойства
     *
     * @param string $name Имя свойства
     *
     * @return string
     */
    protected function getterName(string $name): string {
        return self::GETTER_PREFIX . ucfirst($name);
    }
}
