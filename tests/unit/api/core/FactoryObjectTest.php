<?php

/**
 * FactoryObjectTest.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace Tests\XEAF\API\Core;

use Codeception\Test\Unit;

/**
 * @covers  \XEAF\API\Core\FactoryObject
 *
 * @package Tests\XEAF\API\Core
 */
class FactoryObjectTest extends Unit {

    /**
     * @covers \XEAF\API\Core\FactoryObject::__construct
     *
     * @return void
     */
    public function test__construct(): void {

    }

    /**
     * @covers \XEAF\API\Core\FactoryObject::getName
     *
     * @return void
     */
    public function testGetName(): void {
        $obj = new FactoryObjectStub('Z');
        $this->assertSame('Z', $obj->getName());
    }
}
