<?php

/**
 * Storage.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Core;

/**
 * Реализует базовые методы хранилищ ключ-значение
 *
 * @package  XEAF\API\Core
 */
abstract class Storage extends FactoryObject {

    /**
     * Хранилище значений
     * @var array
     */
    private $_values = [];

    /**
     * Удаляет все сохраненные значения
     *
     * @return void
     */
    public function clear(): void {
        $this->_values = [];
    }

    /**
     * Возвращает ранее сохраненное значение
     *
     * @param string     $key          Ключ
     * @param mixed|null $defaultValue Значение по умолчанию
     *
     * @return mixed
     */
    public function get(string $key, $defaultValue = null) {
        return $this->_values[$key] ?? $defaultValue;
    }

    /**
     * Сохраняет значение
     *
     * @param string     $key   Ключ
     * @param mixed|null $value Значение
     *
     * @return void
     */
    public function put(string $key, $value = null): void {
        $this->_values[$key] = $value;
    }

    /**
     * Удаляет ранее установленное значение
     *
     * @param string $key Ключ
     *
     * @return void
     */
    public function delete(string $key): void {
        unset($this->_values[$key]);
    }

    /**
     * Возвращает признак существования значения
     *
     * @param string $key Ключ
     *
     * @return bool
     */
    public function exists(string $key): bool {
        return isset($this->_values[$key]);
    }

    /**
     * Возвращает массив установленных значений
     *
     * @return array
     */
    protected function storedValues(): array {
        return $this->_values;
    }
}
