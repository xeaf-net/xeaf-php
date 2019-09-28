<?php

/**
 * FactoryObject.php
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
 * Реализует свойства хранения имени объекта
 *
 * @property string $name Имя объекта
 *
 * @package  XEAF\API\Core
 */
abstract class FactoryObject extends StdObject {

    /**
     * Имя объекта по умолчанию
     */
    public const DEFAULT_NAME = 'default';

    /**
     * Имя объекта
     * @var string
     */
    private $_name = self::DEFAULT_NAME;

    /**
     * Конструктор класса
     *
     * @param string $name Имя объекта
     */
    public function __construct(string $name = self::DEFAULT_NAME) {
        $this->_name = $name;
    }

    /**
     * Возвращает имя объекта
     *
     * @return string
     */
    public function getName(): string {
        return $this->_name;
    }

    /**
     * Задает имя объекта
     *
     * @param string $name Имя объекта
     *
     * @return void
     */
    public function setName(string $name): void {
        $this->_name = $name;
    }
}
