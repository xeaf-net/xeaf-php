<?php

/**
 * DataModelTest.php
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
 * @covers  \XEAF\API\Core\DataModel
 *
 * @package Tests\XEAF\API\Core
 */
class DataModelTest extends Unit {

    /**
     * @covers \XEAF\API\Core\DataModel::__set
     *
     * @return void
     */
    public function test__set(): void {
        $obj = new DataModelStub(['propC' => 'New Value C']);
        $this->assertSame('New Value C', $obj->propC);
    }

    /**
     * @covers \XEAF\API\Core\DataModel::__set
     *
     * @return void
     */
    public function test__setException(): void {
        $this->expectException(CoreException::class);
        $this->expectExceptionCode(CoreException::UNKNOWN_WRITABLE_PROPERTY);
        new DataModelStub(['propZ' => 'New Value Z']);
    }

}
