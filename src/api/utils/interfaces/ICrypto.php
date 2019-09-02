<?php

/**
 * ICrypto.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils\Interfaces;

use XEAF\API\Core\Interfaces\IFactoryObject;

/**
 * Описывает методы работы со случайными и шфрованными данными
 *
 * @package XEAF\API\Utils\Interfaces
 */
interface ICrypto extends IFactoryObject {

    /**
     * Возвращает идентификатор алгоритма построения хеша данных
     *
     * @return string
     */
    function hashAlgo(): string;

    /**
     * Возвращает код алгоритма построения хеша паролей
     *
     * @return int
     */
    function passwordAlgo(): int;

    /**
     * Генерирует хеш на основе пароля и строковых данных
     *
     * @param string $data     Данные для хеша
     * @param string $password Пароль
     *
     * @return string
     */
    function hash(string $data, $password = ''): string ;

    /**
     * Безопасный метод сравнение хешей
     *
     * @param string $hash1 Известный хеш
     * @param string $hash2 Сравниваемый хеш
     *
     * @return bool
     */
    function hashEquals(string $hash1, string $hash2): bool;

    /**
     * Возвращает случайно сгенерированную последовательность байтов
     *
     * @param int $length Длина строки
     *
     * @return string
     */
    function randomBytes(int $length): string;

    /**
     * Генерирует UUID версии 4
     *
     * @return string
     */
    function generateUUIDv4(): string;

    /**
     * Возвращает хеш пароля
     *
     * @param string $password Пароль
     *
     * @return string
     */
    function passwordHash(string $password): string;

    /**
     * Проверяет соответствие пароля хешу
     *
     * @param string $password Пароль
     * @param string $hash     Хеш пароля
     *
     * @return bool
     */
    function verifyPassword(string $password, string $hash): bool;

    /**
     * Генерирует значение токена безопасности
     *
     * @return string
     */
    function securityToken(): string;
}
