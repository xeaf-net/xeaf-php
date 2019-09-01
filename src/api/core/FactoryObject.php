<?php

/**
 * FactoryObject.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Core;

use XEAF\API\Core\Interfaces\IFactoryObject;

/**
 * Реализует базовые методы объекта фабрики
 *
 * @property string $name Идентификатор объекта
 *
 * @package XEAF\API\Core
 */
abstract class FactoryObject extends StdObject implements IFactoryObject {

    /**
     * Идентификатор объекта
     * @var string
     */
    private $_name = self::DEFAULT_NAME;

    /**
     * Конструктор класса
     *
     * @param string $name Идентификатор объекта
     */
    public function __construct(string $name) {
        $this->_name = $name;
    }

    /**
     * Возвращает идентификатор объекта
     *
     * @return string
     */
    public function getName(): string {
        return $this->_name;
    }
}
