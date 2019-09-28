<?php

/**
 * DataModel.php
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
 * Реализует базовые свойства моделей данных
 *
 * @package  XEAF\API\Core
 */
abstract class DataModel extends DataObject {

    /**
     * Задает значение неопределенного свойства
     *
     * @param string $name  Свойство
     * @param mixed  $value Значение
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    protected function undefinedSetter(string $name, $value): void {
        throw CoreException::undefinedProperty($this->className, $name);
    }
}
