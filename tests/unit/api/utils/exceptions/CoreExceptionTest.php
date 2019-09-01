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
namespace XEAF;

use Codeception\Test\Unit;
use ReflectionException;
use XEAF\API\Utils\Exceptions\CoreException;

/**
 * @covers  \XEAF\API\Utils\Exceptions\CoreException
 *
 * @package XEAF
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
     * @covers \XEAF\API\Utils\Exceptions\CoreException::unknownProperty
     *
     * @return void
     */
    public function testUnknownProperty() {
        $e = CoreException::unknownProperty('Demo', 'foo');
        $this->assertSame('COR-002', $e->getCode());
        $this->assertSame('Unknown property [Demo::foo].', $e->getMessage());
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
        $this->assertSame('COR-003', $e->getCode());
        $this->assertSame('Internal reflection error.', $e->getMessage());
        $this->assertSame($r, $e->getPrevious());
    }
}
