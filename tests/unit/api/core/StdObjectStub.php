<?php

/**
 * StdObjectStub.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace Tests\XEAF\API\Core;

use XEAF\API\Core\StdObject;

/**
 * Подстановочный класс для тестрирования XEAF\API\Core\StdObject
 *
 * @property-read string $demoData Демонстрационное свойство
 *
 * @package Tests\XEAF\API\Core
 */
class StdObjectStub extends StdObject {

    /**
     * Демонстрационный метод
     *
     * @return string
     */
    public function getDemoData(): string {
        return "Demo data";
    }

    /**
     * Возвращает имя метода геттера для заданного свойства
     *
     * @param string $name Имя свойства
     *
     * @return string
     */
    public function getterName(string $name): string {
        return parent::getterName($name);
    }
}
