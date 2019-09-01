<?php

/**
 * StdObjectTest.php
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
use XEAF\API\Utils\Exceptions\CoreException;

/**
 * @covers  \XEAF\API\Core\StdObject
 *
 * @package Tests\XEAF\API\Core
 */
class StdObjectTest extends Unit {

    /**
     * @covers \XEAF\API\Core\StdObject::getClassName()
     *
     * @return void
     */
    public function testGetClassName(): void {
        $obj = new StdObjectStub();
        $this->assertSame(StdObjectStub::class, $obj->getClassName());
    }

    /**
     * @covers \XEAF\API\Core\StdObject::methodExists()
     *
     * @return void
     */
    public function testMethodExists(): void {
        $obj = new StdObjectStub();
        $this->assertTrue($obj->methodExists('methodExists'));
        $this->assertTrue($obj->methodExists('getDemoData'));
        $this->assertFalse($obj->methodExists('foo'));
    }

    /**
     * @covers \XEAF\API\Core\StdObject::propertyReadable()
     *
     * @return void
     */
    public function testPropertyReadable(): void {
        $obj = new StdObjectStub();
        $this->assertTrue($obj->propertyReadable('className'));
        $this->assertTrue($obj->propertyReadable('demoData'));
        $this->assertFalse($obj->propertyReadable('foo'));
    }

    /**
     * @covers \XEAF\API\Core\StdObject::__get()
     *
     * @return void
     */
    public function test__get(): void {
        $obj = new StdObjectStub();
        $this->assertSame($obj->getClassName(), $obj->className);
        $this->assertSame('Demo data', $obj->demoData);
    }

    /**
     * @covers \XEAF\API\Core\StdObject::__get()
     *
     * @return void
     */
    public function test__getException(): void {
        $this->expectException(CoreException::class);
        $this->expectExceptionCode(CoreException::UNKNOWN_READABLE_PROPERTY);
        $obj = new StdObjectStub();
        /** @noinspection PhpUndefinedFieldInspection */
        $this->assertSame('Foo data', $obj->foo);
    }

    /**
     * @covers \XEAF\API\Core\StdObject::__call
     *
     * @return void
     */
    public function test__call(): void {
        $this->expectException(CoreException::class);
        $this->expectExceptionCode(CoreException::UNKNOWN_METHOD);
        $obj = new StdObjectStub();
        /** @noinspection PhpUndefinedMethodInspection */
        $obj->foo();
    }

    /**
     * @covers \XEAF\API\Core\StdObject::getterName()
     *
     * @return void
     */
    public function testGetterName(): void {
        $obj = new StdObjectStub();
        $this->assertSame('getFoo', $obj->getterName('foo'));
    }
}
