<?php

/**
 * DataModelStub.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace Tests\XEAF\API\Core;

use XEAF\API\Core\DataModel;

/**
 * Подстановочный класс для тестирования XEAF\API\Core\DataModel
 *
 * @property string $propC Свойство C
 *
 * @package Tests\XEAF\API\Core
 */
class DataModelStub extends DataModel {

    /**
     * Свойство C
     * @var string
     */
    protected $_propC = 'C';

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

}

