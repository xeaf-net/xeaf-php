<?php

/**
 * DataObjectTest.php
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
use XEAF\API\Core\DataObject;
use XEAF\API\Utils\Exceptions\CoreException;

/**
 * @covers  \XEAF\API\Core\DataObject
 *
 * @package Tests\XEAF\API\Core
 */
class DataObjectTest extends Unit {

    /**
     * @covers \XEAF\API\Core\DataObject::__construct()
     *
     * @return void
     */
    public function test__construct(): void {
        $obj = new DataObject(['propA' => 'A', 'propB' => 'B']);
        $this->assertSame('A', $obj->{'propA'});
        $this->assertSame('B', $obj->{'propB'});
    }

    /**
     * @covers \XEAF\API\Core\DataObject::propertyReadable()
     *
     * @return void
     */
    public function testPropertyReadable(): void {
        $obj = new DataObjectStub();
        $this->assertTrue($obj->propertyReadable('propA'));
        $this->assertTrue($obj->propertyReadable('propB'));
        $this->assertTrue($obj->propertyReadable('propC'));
        $this->assertTrue($obj->propertyReadable('propM'));
        $this->assertTrue($obj->propertyReadable('propN'));
        $this->assertFalse($obj->propertyReadable('propZ'));
    }

    /**
     * @covers \XEAF\API\Core\DataObject::propertyWritable()
     *
     * @return void
     */
    public function testPropertyWritable(): void {
        $obj = new DataObjectStub();
        $this->assertFalse($obj->propertyWritable('propA'));
        $this->assertFalse($obj->propertyWritable('propB'));
        $this->assertTrue($obj->propertyWritable('propC'));
        $this->assertTrue($obj->propertyWritable('propM'));
        $this->assertTrue($obj->propertyWritable('propN'));
        $this->assertTrue($obj->propertyWritable('propZ'));
    }

    /**
     * @covers \XEAF\API\Core\DataObject::__set()
     *
     * @return void
     */
    public function test__set(): void {
        $obj            = new DataObjectStub();
        $obj->{'propC'} = 'New Value C';
        $obj->{'propN'} = 'New Value N';
        $this->assertSame('New Value C', $obj->{'propC'});
        $this->assertSame('New Value N', $obj->{'propN'});
    }

    /**
     * @covers \XEAF\API\Core\DataObject::__set()
     *
     * @return void
     */
    public function test__setException(): void {
        $this->expectException(CoreException::class);
        $this->expectExceptionCode(CoreException::UNKNOWN_WRITABLE_PROPERTY);
        $obj          = new DataObjectStub();
        $obj->{'foo'} = 'Value';
    }

    /**
     * @covers \XEAF\API\Core\DataObject::getReadableProperties
     *
     * @return void
     */
    public function testGetReadableProperties(): void {
        $obj   = new DataObjectStub();
        $props = $obj->getReadableProperties();
        $this->assertSame(5, count($props));
        $this->assertSame('propA', $props[0]);
        $this->assertSame('propB', $props[1]);
        $this->assertSame('propC', $props[2]);
        $this->assertSame('propM', $props[3]);
        $this->assertSame('propN', $props[4]);
    }

    /**
     * @covers \XEAF\API\Core\DataObject::getWritableProperties
     *
     * @return void
     */
    public function testGetWritableProperties(): void {
        $obj   = new DataObjectStub();
        $props = $obj->getWritableProperties();
        $this->assertSame(4, count($props));
        $this->assertSame('propC', $props[0]);
        $this->assertSame('propM', $props[1]);
        $this->assertSame('propN', $props[2]);
        $this->assertSame('propZ', $props[3]);
    }

    /**
     * @covers \XEAF\API\Core\DataObject::setWritableProperties
     *
     * @return void
     */
    public function testSetWritableProperties(): void {
        $obj = new DataObjectStub();
        $obj->setWritableProperties([
            'propC' => 'New Prop C Value',
            'propM' => 'New Prop M Value',
            'propN' => 'New Prop N Value',
            'propZ' => 'New Prop Z Value',
        ]);
        $this->assertSame('New Prop C Value', $obj->{'propC'});
        $this->assertSame('New Prop M Value', $obj->{'propM'});
        $this->assertSame('New Prop N Value', $obj->{'propN'});
    }

    /**
     * @covers \XEAF\API\Core\DataObject::setterName
     *
     * @return void
     */
    public function testSetterName(): void {
        $obj = new DataObjectStub();
        $this->assertSame('setFoo', $obj->setterName('foo'));
    }

    /**
     * @covers \XEAF\API\Core\DataObject::fromArray
     *
     * @return void
     */
    public function testFromArray(): void {
        $obj = DataObject::fromArray([
            'propA' => 'Value A',
            'propB' => 'Value B'
        ]);
        $this->assertSame(2, count($obj->readableProperties));
        $this->assertSame(2, count($obj->writableProperties));
        $this->assertSame('Value A', $obj->{'propA'});
        $this->assertSame('Value B', $obj->{'propB'});
    }
}


