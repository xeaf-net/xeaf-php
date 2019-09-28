<?php

/**
 * EntityFromModel.php
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
 * Модель данных конструкции FROM
 *
 * @property-read string $name  Сущность
 * @property-read string $alias Псевдоним
 *
 * @package  XEAF\ORM\Models
 */
class EntityFromModel extends DataModel {

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
     * Конструктор класса
     *
     * @param string $name  Сущность
     * @param string $alias Псевдоним
     */
    public function __construct(string $name, string $alias) {
        parent::__construct();
        $this->_name  = $name;
        $this->_alias = $alias;
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
}
