<?php

/**
 * DataObject.php
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
 * Реализует базовые свойства объектов данных
 *
 * @property-read array $readableProperties Свойства для чтения
 * @property      array $writableProperties Свойства для записи
 *
 * @package XEAF\API\Core
 */
class DataObject extends StdObject {

    /**
     * Префикс метода задания значения свойства
     */
    private const SETTER_PREFIX = 'set';

    /**
     * Признак режима инициализации
     * @var bool
     */
    private static $_initializing = false;

    /**
     * Конструктор класса
     *
     * @param array $data Значения свойств объекта
     */
    public function __construct(array $data = []) {
        self::$_initializing = true;
        $this->setWritableProperties($data);
        self::$_initializing = false;
    }

    /**
     * Возвращает признак возможности задания значения свойтства
     *
     * @param string $name Имя свойтства
     *
     * @return bool
     */
    public function propertyWritable(string $name): bool {
        $result = property_exists($this, $name);
        if (!$result) {
            $method = $this->setterName($name);
            $result = $this->methodExists($method);
        }
        return $result;
    }

    /**
     * Задает значение свойства объекта
     *
     * @param string $name  Имя свойства
     * @param mixed  $value Значение
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    public function __set(string $name, $value): void {
        $method = $this->setterName($name);
        if ($this->methodExists($method)) {
            $this->$method($value);
        } else {
            if (self::$_initializing) {
                $this->$name = $value;
            } else {
                throw CoreException::unknownWritableProperty($this->getClassName(), $name);
            }
        }
    }

    /**
     * Возвращает массив идентификаторов доступных для чтения свойств
     *
     * @return array
     */
    public function getReadableProperties(): array {
        $result = [];
        foreach ($this as $name => $_) {
            if (substr($name, 0, 1) == '_') {
                $property = substr($name, 1);
                $method   = $this->getterName($property);
                if ($this->methodExists($method)) {
                    $result[] = $property;
                }
            } else {
                $result[] = $name;
            }
        }
        return $result;
    }

    /**
     * Возвращает массив идентификаторов доступных для записи свойств
     *
     * @return array
     */
    public function getWritableProperties(): array {
        $result = [];
        foreach ($this as $name => $_) {
            if (substr($name, 0, 1) == '_') {
                $property = substr($name, 1);
                $method   = $this->setterName($property);
                if ($this->methodExists($method)) {
                    $result[] = $property;
                }
            } else {
                $result[] = $name;
            }
        }
        return $result;
    }

    /**
     * Задает значения свойств из массива данных
     *
     * @param array $values Массив значений свойств
     *
     * @return void
     */
    public function setWritableProperties(array $values): void {
        foreach ($values as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Возвращает имя метода задания значения свойства
     *
     * @param string $name Имя свойства
     *
     * @return string
     */
    protected function setterName(string $name): string {
        return self::SETTER_PREFIX . ucfirst($name);
    }

    /**
     * Создает объект данных по определениями из массива
     *
     * @param array $values Массив значений свойств
     *
     * @return \XEAF\API\Core\DataObject
     */
    public static function fromArray(array $values): self {
        return new self($values);
    }
}
