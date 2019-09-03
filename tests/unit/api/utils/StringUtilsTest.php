<?php

/**
 * StringUtilsTest.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace Tests\XEAF\API\Utils;

use Codeception\Test\Unit;
use XEAF\API\App\Factory;
use XEAF\API\Utils\Crypto;
use XEAF\API\Utils\StringUtils;

/**
 * @covers   \XEAF\API\Utils\StringUtils
 *
 * @package  Tests\XEAF\API\Utils
 */
class StringUtilsTest extends Unit {

    /**
     * Тестируемый объект
     * @var \XEAF\API\Utils\StringUtils
     */
    private $str = null;

    /**
     * Подготовка теста
     *
     * @return void
     */
    public function _before(): void {
        parent::_before();
        Factory::clear();
        $this->str = StringUtils::getInstance();
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
     * @covers \XEAF\API\Utils\StringUtils::emptyString
     *
     * @return void
     */
    public function testEmptyString(): void {
        $this->assertSame('', $this->str->emptyString());
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::isEmpty
     *
     * @return void
     */
    public function testIsEmpty(): void {
        $this->assertSame(true, $this->str->isEmpty(''));
        $this->assertSame(true, $this->str->isEmpty(null));
        $this->assertSame(false, $this->str->isEmpty(' '));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::emptyToNull
     *
     * @return void
     */
    public function testEmptyToNull(): void {
        $this->assertSame(null, $this->str->emptyToNull(''));
        $this->assertSame(null, $this->str->emptyToNull(null));
        $this->assertNotSame(null, $this->str->emptyToNull(' '));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::stringToInteger
     *
     * @return void
     */
    public function testStringToInteger(): void {
        $this->assertEquals(0, $this->str->stringToInteger(''));
        $this->assertEquals(0, $this->str->stringToInteger('0'));
        $this->assertEquals(0, $this->str->stringToInteger('foo'));
        $this->assertEquals(1, $this->str->stringToInteger('foo', 1));
        $this->assertEquals(-1, $this->str->stringToInteger('-1'));
        $this->assertEquals(1354, $this->str->stringToInteger('1354'));
        $this->assertEquals(0, $this->str->stringToInteger('1354.17'));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::stringToFloat
     *
     * @return void
     */
    public function testStringToFloat(): void {
        $this->assertEquals(0, $this->str->stringToFloat(''));
        $this->assertEquals(0, $this->str->stringToFloat('0'));
        $this->assertEquals(0, $this->str->stringToFloat('foo'));
        $this->assertEquals(1, $this->str->stringToFloat('foo', 1));
        $this->assertEquals(-1.1, $this->str->stringToFloat('-1.1'));
        $this->assertEquals(1354, $this->str->stringToFloat('1354'));
        $this->assertEquals(1354.17, $this->str->stringToFloat('1354.17'));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::stringToDateTime
     *
     * @return void
     */
    public function testStringToDateTime(): void {
        $date = strtotime('2019-01-01');
        $time = strtotime('2019-01-01 13:54:17');
        $this->assertEquals(null, $this->str->stringToDateTime(''));
        $this->assertEquals(null, $this->str->stringToDateTime('0'));
        $this->assertEquals(null, $this->str->stringToDateTime('foo'));
        $this->assertEquals(1, $this->str->stringToDateTime('foo', 1));
        $this->assertEquals($date, $this->str->stringToDateTime('2019-01-01'));
        $this->assertEquals($time, $this->str->stringToDateTime('2019-01-01 13:54:17'));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::isInteger
     *
     * @return void
     */
    public function testIsInteger(): void {
        $this->assertSame(false, $this->str->isInteger(''));
        $this->assertSame(true, $this->str->isInteger('0'));
        $this->assertSame(false, $this->str->isInteger('foo'));
        $this->assertSame(true, $this->str->isInteger('-1'));
        $this->assertSame(true, $this->str->isInteger('1354'));
        $this->assertSame(false, $this->str->isInteger('1354.17'));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::isFloat
     *
     * @return void
     */
    public function testIsFloat(): void {
        $this->assertEquals(0, $this->str->stringToFloat(''));
        $this->assertEquals(0, $this->str->stringToFloat('0'));
        $this->assertEquals(0, $this->str->stringToFloat('foo'));
        $this->assertEquals(1, $this->str->stringToFloat('foo', 1));
        $this->assertEquals(-1.1, $this->str->stringToFloat('-1.1'));
        $this->assertEquals(1354, $this->str->stringToFloat('1354'));
        $this->assertEquals(1354.17, $this->str->stringToFloat('1354.17'));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::isDateTime
     *
     * @return void
     */
    public function testIsDateTime(): void {
        $this->assertSame(false, $this->str->isDateTime(''));
        $this->assertSame(false, $this->str->isDateTime('0'));
        $this->assertSame(false, $this->str->isDateTime('foo'));
        $this->assertSame(true, $this->str->isDateTime('2019-01-01'));
        $this->assertSame(true, $this->str->isDateTime('2019-01-01 13:54:17'));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::isUUID
     *
     * @return void
     */
    public function isUUID(): void {
        $this->assertSame(false, $this->str->isUUID(''));
        $this->assertSame(false, $this->str->isUUID('0'));
        $this->assertSame(false, $this->str->isUUID('foo'));
        $this->assertSame(false, $this->str->isUUID('00000000-0000-0000-0000-000000000000'));
        $this->assertSame(true, $this->str->isUUID('e5edb4cd-711c-473c-93bc-8e65bdbc2061'));
        $this->assertSame(true, $this->str->isUUID(Crypto::getInstance()->generateUUIDv4()));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::isEmail
     *
     * @return void
     */
    public function testIsEmail(): void {
        $this->assertSame(false, $this->str->isEmail(''));
        $this->assertSame(false, $this->str->isEmail('0'));
        $this->assertSame(false, $this->str->isEmail('foo'));
        $this->assertSame(false, $this->str->isEmail('foo@example'));
        $this->assertSame(true, $this->str->isEmail('demo@example.com'));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::isObjectId
     *
     * @return void
     */
    public function testIsObjectId(): void {
        $this->assertSame(false, $this->str->isObjectId(''));
        $this->assertSame(true, $this->str->isObjectId('0'));
        $this->assertSame(true, $this->str->isObjectId(0));
        $this->assertSame(true, $this->str->isObjectId(1354));
        $this->assertSame(false, $this->str->isObjectId('foo'));
        $this->assertSame(false, $this->str->isObjectId('00000000-0000-0000-0000-000000000000'));
        $this->assertSame(true, $this->str->isObjectId('e5edb4cd-711c-473c-93bc-8e65bdbc2061'));
        $this->assertSame(true, $this->str->isObjectId(Crypto::getInstance()->generateUUIDv4()));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::startsWith
     *
     * @return void
     */
    public function testStartsWith(): void {
        $this->assertSame(true, $this->str->startsWith('', ''));
        $this->assertSame(true, $this->str->startsWith('000', '0'));
        $this->assertSame(true, $this->str->startsWith('Abb', 'Ab'));
        $this->assertSame(true, $this->str->startsWith('Abc', 'Abc'));
        $this->assertSame(false, $this->str->startsWith('Abc', 'AbC'));
        $this->assertSame(true, $this->str->startsWith('Abc', ''));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::endsWith
     *
     * @return void
     */
    public function testEndsWith(): void {
        $this->assertSame(true, $this->str->endsWith('', ''));
        $this->assertSame(true, $this->str->endsWith('000', '0'));
        $this->assertSame(true, $this->str->endsWith('Abb', 'bb'));
        $this->assertSame(true, $this->str->endsWith('Abc', 'Abc'));
        $this->assertSame(false, $this->str->endsWith('Abc', 'AbC'));
        $this->assertSame(true, $this->str->endsWith('Abc', ''));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::upperCaseFirst
     *
     * @return void
     */
    public function testUpperCaseFirst(): void {
        $this->assertSame('', $this->str->upperCaseFirst(''));
        $this->assertSame('000', $this->str->upperCaseFirst('000'));
        $this->assertSame('Abb', $this->str->upperCaseFirst('abb'));
        $this->assertSame('Abb', $this->str->upperCaseFirst('Abb'));
        $this->assertSame('Abb', $this->str->upperCaseFirst('ABB'));
        $this->assertSame('Абб', $this->str->upperCaseFirst('абб'));
        $this->assertSame('Абб', $this->str->upperCaseFirst('АББ'));
    }

    /**
     * @covers \XEAF\API\Utils\StringUtils::getInstance
     *
     * @return void
     */
    public function testGetInstance(): void {
        $str = StringUtils::getInstance();
        $this->assertSame($this->str, $str);
    }
}
