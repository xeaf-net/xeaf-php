<?php

/**
 * EntityOrderModel.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-ORM
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\ORM\Models;

use XEAF\API\Core\DataModel;

/**
 * Модель данных условия сортировки записей
 *
 * @property string $alias     Псевдоним сущности
 * @property string $property  Свойство
 * @property string $direction Направление
 *
 * @package  XEAF\ORM\Models
 */
class EntityOrderModel extends DataModel {

    /**
     * Признак сортировки по возрастанию
     */
    public const ORDER_ASCENDING = 'ascending';

    /**
     * Признак сортировки по убыванию
     */
    public const ORDER_DESCENDING = 'descending';

    /**
     * Псевдоним сущности
     * @var string
     */
    private $_alias = '';

    /**
     * Свойство
     * @var string
     */
    private $_property = '';

    /**
     * Напрвление
     * @var string
     */
    private $_direction = '';

    /**
     * Конструктор класса
     *
     * @param string $alias     Псевдоним сущности
     * @param string $property  Свойство
     * @param string $direction Направление
     */
    public function __construct(string $alias, string $property, string $direction = self::ORDER_ASCENDING) {
        parent::__construct();
        $this->_alias     = $alias;
        $this->_property  = $property;
        $this->_direction = $direction;
    }

    /**
     * Возвращает псевдоним сущности
     *
     * @return string
     */
    public function getAlias(): string {
        return $this->_alias;
    }

    /**
     * Задает псевдоним сущности
     *
     * @param string $alias Псевдоним сущности
     *
     * @return void
     */
    public function setAlias(string $alias): void {
        $this->_alias = $alias;
    }

    /**
     * Возвращает свойство
     *
     * @return string
     */
    public function getProperty(): string {
        return $this->_property;
    }

    /**
     * Задает свойство
     *
     * @param string $property Свойство
     *
     * @return void
     */
    public function setProperty(string $property): void {
        $this->_property = $property;
    }

    /**
     * Возвращает направление
     *
     * @return string
     */
    public function getDirection(): string {
        return $this->_direction;
    }

    /**
     * Задает направление
     *
     * @param string $direction Направление
     *
     * @return void
     */
    public function setDirection(string $direction): void {
        $this->_direction = $direction;
    }
}
