<?php

/**
 * StdObject.php
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
 * Базовый класс для всех классов объектов проекта
 *
 * @property-read string $className Полное имя класса
 *
 * @package  XEAF\API\Core
 */
abstract class StdObject {

    /**
     * Префикс метода получения значения свойства класса
     */
    protected const GETTER_PREFIX = 'get';

    /**
     * Префикс метода задания значения свойства класса
     */
    protected const SETTER_PREFIX = 'set';

    /**
     * Возвращает полное имя класса объекта
     *
     * @return string
     */
    protected function getClassName(): string {
        return get_class($this);
    }

    /**
     * Возвращает значение свойства объекта класса
     *
     * @param string $name Свойство
     *
     * @return mixed
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    public function __get(string $name) {
        $method = $this->propertyMethod($name, StdObject::GETTER_PREFIX);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return $this->undefinedGetter($name);
    }

    /**
     * Возвращает значение неопределенного свойства
     *
     * @param string $name Свойство
     *
     * @return mixed
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    protected function undefinedGetter(string $name) {
        throw CoreException::undefinedProperty($this->className, $name);
    }

    /**
     * Обрабатывает обращение к неизвестному методу
     *
     * @param string $name      Метод
     * @param array  $arguments Аргументы вызова метода
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    public function __call(string $name, array $arguments) {
        throw CoreException::undefinedMethod($this->className, $name);
    }

    /**
     * Возвращает имя метода обращеия к свойству класса
     *
     * @param string $name   Свойство
     * @param string $prefix Префикс метода
     *
     * @return string
     */
    protected function propertyMethod(string $name, string $prefix): string {
        return $prefix . ucfirst($name);
    }

    /**
     * Задает значение свойства только при условнии непустого значения
     *
     * @param string     $name  Свойство
     * @param mixed|null $value Значение свойства
     *
     * @return void
     */
    protected function assignIfNotNull(string $name, $value): void {
        if ($value !== null) {
            $this->$name = $value;
        }
    }

    /**
     * Задает значение переменной только при условнии непустого значения
     *
     * @param mixed      $var   Переменная
     * @param mixed|null $value Значение свойства
     *
     * @return void
     */
    protected function assignVarIfNotNull(&$var, $value): void {
        if ($value !== null) {
            $var = $value;
        }
    }
}
