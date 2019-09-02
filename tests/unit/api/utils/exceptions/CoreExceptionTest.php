<?php

/**
 * CoreExceptionTest.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace Tests\XEAF\API\Utils\Exceptions;

use Codeception\Test\Unit;
use ReflectionException;
use XEAF\API\Utils\Exceptions\CoreException;

/**
 * @covers  \XEAF\API\Utils\Exceptions\CoreException
 *
 * @package Tests\XEAF\API\Utils\Exceptions
 */
class CoreExceptionTest extends Unit {

    /**
     * @covers \XEAF\API\Utils\Exceptions\CoreException::unknownMethod
     *
     * @return void
     */
    public function testUnknownMethod() {
        $e = CoreException::unknownMethod('Demo', 'foo');
        $this->assertSame('COR-001', $e->getCode());
        $this->assertSame('Call to unknown method [Demo::foo()].', $e->getMessage());
        $this->assertNull($e->getPrevious());
    }

    /**
     * @covers \XEAF\API\Utils\Exceptions\CoreException::unknownReadableProperty
     *
     * @return void
     */
    public function testUnknownReadableProperty() {
        $e = CoreException::unknownReadableProperty('Demo', 'foo');
        $this->assertSame('COR-002', $e->getCode());
        $this->assertSame('Property [Demo::foo] is undefined or write only.', $e->getMessage());
        $this->assertNull($e->getPrevious());
    }

    /**
     * @covers \XEAF\API\Utils\Exceptions\CoreException::unknownWritableProperty
     *
     * @return void
     */
    public function testUnknownWritableProperty() {
        $e = CoreException::unknownWritableProperty('Demo', 'foo');
        $this->assertSame('COR-003', $e->getCode());
        $this->assertSame('Property [Demo::foo] is undefined or read only.', $e->getMessage());
        $this->assertNull($e->getPrevious());
    }

    /**
     * @covers \XEAF\API\Utils\Exceptions\CoreException::reflectionError
     *
     * @return void
     */
    public function testReflectionError() {
        $r = new ReflectionException('Ref');
        $e = CoreException::reflectionError($r);
        $this->assertSame('COR-004', $e->getCode());
        $this->assertSame('Internal reflection error.', $e->getMessage());
        $this->assertSame($r, $e->getPrevious());
    }
}
