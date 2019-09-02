<?php

/**
 * CryptoTest.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace Tests\XEAF\API\Utils;

namespace Tests\XEAF\API\Utils;

use Codeception\Test\Unit;
use XEAF\API\App\Factory;
use XEAF\API\Utils\Crypto;

/**
 * @covers  \XEAF\API\Utils\Crypto
 *
 * @package Tests\XEAF\API\Utils
 */
class CryptoTest extends Unit {

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
     * @covers \XEAF\API\Utils\Crypto::hashAlgo
     *
     * @return void
     */
    public function testHashAlgo(): void {
        $crypto = Crypto::getInstance();
        $this->assertSame('sha256', $crypto->hashAlgo());
    }

    /**
     * @covers \XEAF\API\Utils\Crypto::hashAlgo
     *
     * @return void
     */
    public function testPasswordAlgo(): void {
        $crypto = Crypto::getInstance();
        $this->assertSame(PASSWORD_DEFAULT, $crypto->passwordAlgo());
    }

    /**
     * @covers \XEAF\API\Utils\Crypto::hash
     *
     * @return void
     */
    public function testHash(): void {
        $crypto   = Crypto::getInstance();
        $data     = "Test string";
        $password = "Test password";
        $hash1    = $crypto->hash($data, $password);
        $hash2    = $crypto->hash($data, $password);
        $this->assertSame($hash1, $hash2);
    }

    /**
     * @covers \XEAF\API\Utils\Crypto::hashEquals
     *
     * @return void
     */
    public function testHashEquals(): void {
        $crypto   = Crypto::getInstance();
        $data     = "Test string";
        $password = "Test password";
        $hash1    = $crypto->hash($data, $password);
        $hash2    = $crypto->hash($data, $password);
        $hash3    = $crypto->hash($data);
        $this->assertSame(true, $crypto->hashEquals($hash1, $hash2));
        $this->assertSame(false, $crypto->hashEquals($hash1, $hash3));
        $this->assertSame(false, $crypto->hashEquals($hash2, $hash3));
    }

    /**
     * @covers \XEAF\API\Utils\Crypto::randomBytes
     *
     * @return void
     */
    public function testRandomBytes(): void {
        $crypto = Crypto::getInstance();
        $data1  = $crypto->randomBytes(16);
        $data2  = $crypto->randomBytes(16);
        $data3  = $crypto->randomBytes(16);
        $this->assertNotSame($data1, $data2);
        $this->assertNotSame($data1, $data3);
        $this->assertNotSame($data2, $data3);
        $this->assertSame(16, strlen($data1));
        $this->assertSame(16, strlen($data2));
        $this->assertSame(16, strlen($data3));
    }

    /**
     * @covers \XEAF\API\Utils\Crypto::generateUUIDv4
     *
     * @return void
     */
    public function testGenerateUUIDv4(): void {
        $crypto  = Crypto::getInstance();
        $pattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i';
        $data1   = $crypto->generateUUIDv4();
        $data2   = $crypto->generateUUIDv4();
        $data3   = $crypto->generateUUIDv4();
        $this->assertNotSame($data1, $data2);
        $this->assertNotSame($data1, $data3);
        $this->assertNotSame($data2, $data3);
        $this->assertSame(36, strlen($data1));
        $this->assertSame(36, strlen($data2));
        $this->assertSame(36, strlen($data3));
        $this->assertSame(1, preg_match($pattern, $data1));
        $this->assertSame(1, preg_match($pattern, $data2));
        $this->assertSame(1, preg_match($pattern, $data3));
    }

    /**
     * @covers \XEAF\API\Utils\Crypto::passwordHash
     *
     * @return void
     */
    public function testPasswordHash(): void {
        $crypto = Crypto::getInstance();
        $hash1  = $crypto->passwordHash('manager');
        $hash2  = $crypto->passwordHash('manager');
        $this->assertSame(true, password_verify('manager', $hash1));
        $this->assertSame(true, password_verify('manager', $hash2));
    }

    /**
     * @covers \XEAF\API\Utils\Crypto::verifyPassword
     *
     * @return void
     */
    public function testVerifyPassword(): void {
        $crypto = Crypto::getInstance();
        /** @noinspection SpellCheckingInspection */
        $hash1 = '$2y$10$oinG0tx1s6CkXbtJiWUcDuUm392k0dSPdEokruPM1rV/zRe6k0..K';
        /** @noinspection SpellCheckingInspection */
        $hash2 = '$2y$10$4dh5rUCEjD80maejkISgDO7Ee4OKCEtKybEr6I7Uz0XtEBnuPBNEq';
        $this->assertSame(true, $crypto->verifyPassword('manager', $hash1));
        $this->assertSame(true, $crypto->verifyPassword('manager', $hash2));
    }

    /**
     * @covers \XEAF\API\Utils\Crypto::securityToken
     *
     * @return void
     */
    public function testSecurityToken(): void {
        $crypto = Crypto::getInstance();
        $token1 = $crypto->securityToken();
        $token2 = $crypto->securityToken();
        $this->assertNotSame($token1, $token2);
    }

    /**
     * @covers \XEAF\API\Utils\Crypto::getInstance
     *
     * @return void
     */
    public function testGetInstance(): void {
        $crypto1 = Crypto::getInstance();
        $crypto2 = Crypto::getInstance();
        $this->assertSame($crypto1, $crypto2);
    }
}
