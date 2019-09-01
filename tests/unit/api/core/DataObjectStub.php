<?php

/**
 * DataObjectStub.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace Tests\XEAF\API\Core;

use XEAF\API\Core\DataObject;

/**
 * Подстановочный класс для тестирования XEAF\API\Core\DataObject
 *
 * @package Tests\XEAF\API\Core
 */
class DataObjectStub extends DataObject {

    /**
     * Свойство A только для чтения
     * @var string
     */
    protected $_propA = 'A';

    /**
     * Свойство B только для чтения
     * @var string
     */
    protected $_propB = 'B';

    /**
     * Свойство С для чтения и записи
     * @var string
     */
    protected $_propC = 'C';

    /**
     * Общедоступное свойство M
     * @var string
     */
    public $propM = 'M';

    /**
     * Общедоступное свойство N
     * @var string
     */
    public $propN = 'N';

    /**
     * Свойство Z только для записи
     * @var string
     */
    protected $_propZ = 'Z';

    /**
     * Возвращает значение свойства A
     *
     * @return string
     */
    public function getPropA(): string {
        return $this->_propA;
    }

    /**
     * Возвращает значение свойства B
     *
     * @return string
     */
    public function getPropB(): string {
        return $this->_propB;
    }

    /**
     * Возвращает значение свойства C
     *
     * @return string
     */
    public function getPropC(): string {
        return $this->_propC;
    }

    /**
     * Задает значение свойства C
     *
     * @param string $value Значение свойства
     *
     * @return void
     */
    public function setPropC(string $value): void {
        $this->_propC = $value;
    }

    /**
     * Задает значение свойства Z
     *
     * @param string $value Значение свойства
     *
     * @return void
     */
    public function setPropZ(string $value): void {
        $this->_propZ = $value;
    }

    /**
     * Возвращает имя метода задания значения свойства
     *
     * @param string $name Имя свойства
     *
     * @return string
     */
    public function setterName(string $name): string {
        return parent::setterName($name);
    }
}
