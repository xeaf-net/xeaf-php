<?php

/**
 * DataObject.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Core;

use XEAF\API\Utils\Exceptions\CoreException;

/**
 * Реализует базовые свойства объектов данных
 *
 * @property-read array $properties     Свойства объекта
 * @property      array $propertyValues Значения свойств
 *
 * @package  XEAF\API\Core
 */
class DataObject extends StdObject {

    /**
     * Префикс переменной свойства
     */
    private const PROPERTY_VAR_PREFIX = '_';

    /**
     * Признак инициализации
     * @var bool
     */
    private static $_initialization = false;

    /**
     * Конструктор класса
     *
     * @param array|null $data Данные инициализации
     */
    public function __construct(array $data = null) {
        if ($data) {
            self::$_initialization = true;
            $this->setPropertyValues($data);
            self::$_initialization = false;
        }
    }

    /**
     * Возвращает массив свойств объекта
     *
     * @return array
     */
    public function getProperties(): array {
        $result = [];
        foreach ($this as $key => $value) {
            $result[] = ltrim($key, self::PROPERTY_VAR_PREFIX);
        }
        return $result;
    }

    /**
     * Возвращает массив значений свойств объекта
     *
     * @return array
     */
    public function getPropertyValues(): array {
        $result = [];
        $names  = $this->getProperties();
        foreach ($names as $name) {
            $result[$name] = $this->$name;
        }
        return $result;
    }

    /**
     * Задает значения свойств объекта
     *
     * @param array $values Массив значений
     *
     * @return void
     */
    public function setPropertyValues(array $values): void {
        foreach ($values as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Задает значение свойства объекта класса
     *
     * @param string $name  Свойство
     * @param mixed  $value Значение
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    public function __set(string $name, $value): void {
        $method = $this->propertyMethod($name, self::SETTER_PREFIX);
        if (method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $this->undefinedSetter($name, $value);
        }
    }

    /**
     * Задает значение неопределенного свойства
     *
     * @param string $name   Свойство
     * @param mixed  $value  Значение
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    protected function undefinedSetter(string $name, $value): void {
        if (self::$_initialization) {
            $this->$name = $value;
        } else {
            throw CoreException::undefinedProperty($this->className, $name);
        }
    }
}
