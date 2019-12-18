<?php

/**
 * Collection.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Core;

use Iterator;

/**
 * Реализует базовые методы коллекций объектов
 *
 * @package  XEAF\API\Core
 */
abstract class Collection implements Iterator {

    /**
     * Хранилище объектов коллекции
     * @var array
     */
    protected $_data = [];

    /**
     * Признак возможности сохранять дубликаты
     * @var bool
     */
    protected $_duplicates = true;

    /**
     * Конструктор класса
     *
     * @param bool $duplicates Признак возможности сохранять дубликаты
     */
    public function __construct(bool $duplicates = true) {
        $this->_duplicates = $duplicates;
    }

    /**
     * Очищает коллекцию объектов
     *
     * @return void
     */
    public function clear(): void {
        $this->_data = [];
        $this->rewind();
    }

    /**
     * Возвращает признак пустой коллекции
     *
     * @return bool
     */
    public function isEmpty(): bool {
        return count($this->_data) == 0;
    }

    /**
     * Возвращает количество элементов в коллекции
     *
     * @return int
     */
    public function count(): int {
        return count($this->_data);
    }

    /**
     * Проверяет существование элемента в коллекции
     *
     * @param mixed $item Проверяемый элемент
     *
     * @return bool
     */
    public function exists($item): bool {
        return in_array($item, $this->_data);
    }

    /**
     * Возвращает массив элементов
     *
     * @return array
     */
    public function toArray(): array {
        return $this->_data;
    }

    /**
     * Изменяет порядок сортировки объектов
     *
     * @param callable $compare Функция сравнения объекто коллекции
     *
     * @return void
     */
    public function reorder(callable $compare): void {
        usort($this->_data, $compare);
        $this->rewind();
    }

    /**
     * Извлекает объект из коллекции
     *
     * @return mixed
     */
    abstract public function pop();

    /**
     * Помещает объект в коллекцию
     *
     * @param mixed $item Элемент коллекции
     *
     * @return void
     */
    abstract public function push($item): void;

    /**
     * Возвращает первый элемент коллекции
     *
     * @return mixed
     */
    abstract public function first();

    /**
     * Возвращает последний элемент коллекции
     *
     * @return mixed
     */
    abstract public function last();
}
