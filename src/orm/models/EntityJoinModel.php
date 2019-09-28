<?php

/**
 * EntityJoinModel.php
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
 * Модель данных объединения сущностей
 *
 * @property-read string $type          Тип объединения
 * @property-read string $name          Сущность
 * @property-read string $alias         Псевдоним
 * @property      string $leftAlias     Псевдоним сущности свойства слева
 * @property      string $leftProperty  Свойство сущности слева
 * @property      string $rightAlias    Псевдоним сущности свойства справа
 * @property      string $rightProperty Свойство сущности справа
 *
 * @package  XEAF\ORM\Models
 */
class EntityJoinModel extends DataModel {

    /**
     * Тип объединения слева
     */
    public const LEFT = 'left';

    /**
     * Тип объединения справа
     */
    public const RIGHT = 'right';

    /**
     * Тип внутреннего объединения
     */
    public const INNER = 'inner';

    /**
     * Тип внешнего объединения
     */
    public const OUTER = 'outer';

    /**
     * Тип объединения
     * @var string
     */
    private $_type = '';

    /**
     * Сущность
     * @var string
     */
    private $_name = '';

    /**
     * Псевдоним
     * @var string
     */
    private $_alias = '';

    /**
     * Псевдоним сущности свойства слева
     * @var string
     */
    private $_leftAlias = '';

    /**
     * Свойство сущности слева
     * @var string
     */
    private $_leftProperty = '';

    /**
     * Псевдоним сущности свойства справа
     * @var string
     */
    private $_rightAlias = '';

    /**
     * Свойство сущности справа
     * @var string
     */
    private $_rightProperty = '';

    /**
     * Конструктор класса
     *
     * @param string $type          Тип объединения
     * @param string $name          Сущность
     * @param string $alias         Псевдоним
     * @param string $leftAlias     Псевдоним сущности свойства слева
     * @param string $leftProperty  Свойство сущности слева
     * @param string $rightAlias    Псевдоним сущности свойства справа
     * @param string $rightProperty Свойство сущности справа
     */
    public function __construct(string $type, string $name, string $alias, string $leftAlias, string $leftProperty, string $rightAlias, string $rightProperty) {
        parent::__construct();
        $this->_type          = $type;
        $this->_name          = $name;
        $this->_alias         = $alias;
        $this->_leftAlias     = $leftAlias;
        $this->_leftProperty  = $leftProperty;
        $this->_rightAlias    = $rightAlias;
        $this->_rightProperty = $rightProperty;
    }

    /**
     * Возвращает тип объединения
     *
     * @return string
     */
    public function getType(): string {
        return $this->_type;
    }

    /**
     * Возвращает сущность
     *
     * @return string
     */
    public function getName(): string {
        return $this->_name;
    }

    /**
     * Возвращает псевдоним
     *
     * @return string
     */
    public function getAlias(): string {
        return $this->_alias;
    }

    /**
     * Возвращает псевдоним сущности свойства слева
     *
     * @return string
     */
    public function getLeftAlias(): string {
        return $this->_leftAlias;
    }

    /**
     * Задает псевдоним сущности свойства слева
     *
     * @param string $leftAlias Псевдоним сущности свойства слева
     *
     * @return void
     */
    public function setLeftAlias(string $leftAlias): void {
        $this->_leftAlias = $leftAlias;
    }

    /**
     * Возвращает свойство сущности слева
     *
     * @return string
     */
    public function getLeftProperty(): string {
        return $this->_leftProperty;
    }

    /**
     * Задает свойство сущности слева
     *
     * @param string $leftProperty Свойство сущности слева
     *
     * @return void
     */
    public function setLeftProperty(string $leftProperty): void {
        $this->_leftProperty = $leftProperty;
    }

    /**
     * Возвращает псевдоним сущности свойства справа
     *
     * @return string
     */
    public function getRightAlias(): string {
        return $this->_rightAlias;
    }

    /**
     * Задает псевдоним сущности свойства справа
     *
     * @param string $rightAlias Псевдоним сущности свойства справа
     *
     * @return void
     */
    public function setRightAlias(string $rightAlias): void {
        $this->_rightAlias = $rightAlias;
    }

    /**
     * Возвращает свойство сущности справа
     *
     * @return string
     */
    public function getRightProperty(): string {
        return $this->_rightProperty;
    }

    /**
     * Задает свойство сущности справа
     *
     * @param string $rightProperty Свойство сущности справа
     *
     * @return void
     */
    public function setRightProperty(string $rightProperty): void {
        $this->_rightProperty = $rightProperty;
    }
}
