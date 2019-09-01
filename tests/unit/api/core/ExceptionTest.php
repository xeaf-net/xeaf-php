<?php

/**
 * ExceptionTest.php
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
use Exception;

/**
 * @covers  \XEAF\API\Core\Exception
 *
 * @package Tests\XEAF\API\Core
 */
class ExceptionTest extends Unit {

    /**
     * @covers \XEAF\API\Core\Exception::__construct
     *
     * @return void
     */
    public function test__construct(): void {
        $r = new Exception('Demo');
        $e = new ExceptionStub('TST-054', '%s::%s', ['A', 'B'], $r);
        $this->assertSame('TST-054', $e->getCode());
        $this->assertSame('A::B', $e->getMessage());
        $this->assertSame($r, $e->getPrevious());
    }

    /**
     * @covers \XEAF\API\Core\Exception::__toString
     *
     * @return void
     */
    public function test__toString(): void {
        $e = new ExceptionStub('TST-054', '%s::%s', ['A', 'B']);
        $this->assertSame('[TST-054] A::B', $e->__toString());
    }
}
