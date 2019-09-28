<?php

/**
 * Queue.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

use XEAF\API\Core\Collection;
use XEAF\API\Utils\Exceptions\CollectionException;

/**
 * Реализует методы работы с очередью элементов
 *
 * @package  XEAF\API\Utils
 */
class Queue extends Collection {

    /**
     * Текущая позиция итерации
     * @var int|null
     */
    private $_position = null;

    /**
     * Извлекает объект из коллекции
     *
     * @return mixed
     * @throws \XEAF\API\Utils\Exceptions\CollectionException
     */
    public function pop() {
        if (!$this->isEmpty()) {
            $this->rewind();
            return array_shift($this->_data);
        }
        throw CollectionException::queueIsEmpty();
    }

    /**
     * Помещает объект в коллекцию
     *
     * @param mixed $item Элемент коллекции
     *
     * @return void
     */
    public function push($item): void {
        if ($this->_duplicates || !$this->exists($item)) {
            $this->_data[] = $item;
            $this->rewind();
        }
    }

    /**
     * Return the current element
     * @link  https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current() {
        return $this->_position === null ? null : $this->_data[$this->_position];
    }

    /**
     * Move forward to next element
     * @link  https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next() {
        if ($this->_position !== null) {
            $this->_position++;
        }
    }

    /**
     * Return the key of the current element
     * @link  https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key() {
        return $this->_position;
    }

    /**
     * Checks if current position is valid
     * @link  https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid() {
        return $this->_position !== null && $this->_position < $this->count();
    }

    /**
     * Rewind the Iterator to the first element
     * @link  https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind() {
        $this->_position = $this->count() > 0 ? 0 : null;
    }

    /**
     * Возвращает первый элемент коллекции
     *
     * @return mixed
     * @throws \XEAF\API\Utils\Exceptions\CollectionException
     */
    public function first() {
        if (!$this->isEmpty()) {
            return $this->_data[0];
        }
        throw CollectionException::queueIsEmpty();
    }

    /**
     * Возвращает последний элемент коллекции
     *
     * @return mixed
     * @throws \XEAF\API\Utils\Exceptions\CollectionException
     */
    public function last() {
        $n = $this->count();
        if ($n > 0) {
            return $this->_data[$n - 1];
        }
        throw CollectionException::queueIsEmpty();
    }
}
