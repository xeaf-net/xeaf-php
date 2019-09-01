<?php

/**
 * DataModel.php
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
 * Реализует методы моделей данных
 *
 * @package XEAF\API\Core
 */
abstract class DataModel extends DataObject {

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
            throw CoreException::unknownWritableProperty($this->getClassName(), $name);
        }
    }
}
