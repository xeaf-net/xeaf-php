<?php

/**
 * FactoryTest.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace Tests\XEAF\API\App;

use Codeception\Test\Unit;
use Tests\XEAF\API\Core\FactoryObjectStub;
use XEAF\API\App\Factory;

/**
 * @covers  \XEAF\API\App\Factory
 *
 * @package Tests\XEAF\API\App
 */
class FactoryTest extends Unit {

    /**
     * Подготовка теста
     *
     * @return void
     */
    public function _before(): void {
        parent::_before();
        Factory::clear();
    }

    /**
     * Завершение теста
     *
     * @return void
     */
    public function _after(): void {
        Factory::clear();
        parent::_after();
    }

    /**
     * @covers \XEAF\API\App\Factory::clear
     *
     * @return void
     */
    public function testClear(): void {
        $object1 = Factory::getFactoryObject(FactoryObjectStub::class, 'name-1');
        Factory::clear();
        $object2 = Factory::getFactoryObject(FactoryObjectStub::class, 'name-1');
        $this->assertNotSame($object1, $object2);
    }

    /**
     * @covers \XEAF\API\App\Factory::getFactoryObject
     *
     * @return void
     */
    public function testGetFactoryObject(): void {
        $object1 = Factory::getFactoryObject(FactoryStub::class, 'name-1');
        $object2 = Factory::getFactoryObject(FactoryStub::class, 'name-1');
        $this->assertSame($object1, $object2);
    }

    /**
     * @covers \XEAF\API\App\Factory::setFactoryObject
     *
     * @return void
     */
    public function testSetFactoryObject(): void {
        $object1 = new FactoryStub('name-1');
        $object2 = new FactoryStub('name-2');
        Factory::setFactoryObject(FactoryStub::class, 'name-1', $object1);
        Factory::setFactoryObject(FactoryStub::class, 'name-2', $object2);
        $this->assertSame($object1, Factory::getFactoryObject(FactoryStub::class, 'name-1'));
        $this->assertSame($object2, Factory::getFactoryObject(FactoryStub::class, 'name-2'));
    }
}
