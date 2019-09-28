<?php

/**
 * EntityFilterModel.php
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
 * Модель данных условий фильтрации
 *
 * @property-read array  $filterProperties Свойства
 * @property-read string $filterValue      Значение фильтра
 *
 * @package  XEAF\ORM\Models
 */
class EntityFilterModel extends DataModel {

    /**
     * Свойства
     * @var array
     */
    private $_filterProperties = [];

    /**
     * Значение фильтра
     * @var string
     */
    private $_filterValue = '';

    /**
     * Конструктор класса
     *
     * @param array  $properties Свойства
     * @param string $value      Значение фильтра
     */
    public function __construct(array $properties, string $value) {
        parent::__construct();
        $this->_filterProperties = $properties;
        $this->_filterValue      = mb_strtoupper($value);
    }

    /**
     * Возвращает список свойств
     *
     * @return array
     */
    public function getFilterProperties(): array {
        return $this->_filterProperties;
    }

    /**
     * Возвращает значение фильтра
     *
     * @return string
     */
    public function getFilterValue(): string {
        return $this->_filterValue;
    }
}
