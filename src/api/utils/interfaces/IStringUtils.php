<?php

/**
 * IStringUtils.php
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
 * Описывает методы работы со строковыми данными
 *
 * @package XEAF\API\Utils\Interfaces
 */
interface IStringUtils extends IFactoryObject {

    /**
     * Возвращает пустую строку
     *
     * @return string
     */
    function emptyString(): string;

    /**
     * Возвращает признак пустой строки
     *
     * @param string|null $buf Строка символов
     *
     * @return bool
     */
    function isEmpty(?string $buf): bool;

    /**
     * Возвращает NULL для пустой строки
     *
     * @param string|null $buf Строка символов
     *
     * @return string|null
     */
    function emptyToNull(?string $buf): ?string;

    /**
     * Преобразует строку в целое число
     *
     * @param string|null $buf     Строка символов
     * @param int         $onError Результат при ошибке
     *
     * @return int
     */
    function stringToInteger(?string $buf, int $onError = 0): int;

    /**
     * Преобразует строку в действительное число
     *
     * @param string|null $buf     Строка символов
     * @param float       $onError Результат при ошибке
     *
     * @return float
     */
    function stringToFloat(?string $buf, float $onError = 0): float;

    /**
     * Преобразует строку в дату и время
     *
     * @param string|null $buf     Строка символов
     * @param int|null    $onError Результат при ошибке
     *
     * @return int|null
     */
    function stringToDateTime(?string $buf, int $onError = null): ?int;

    /**
     * Проверяет содержит ли переданная строка целое число
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    function isInteger(?string $buf): bool;

    /**
     * Проверяет содержит ли переданная строка действительное число
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    function isFloat(?string $buf): bool;

    /**
     * Проверяет содержит ли переданная строка дату и время
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    function isDateTime(?string $buf): bool;

    /**
     * Проверяет является ли преданная строка UUID
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    function isUUID(?string $buf): bool;

    /**
     * Провряет является ли переданная строка адресом электронной почты
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    function isEmail(?string $buf): bool;

    /**
     * Проверяет является ли преданная строка идентификатором объекта
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    function isObjectId(?string $buf): bool;

    /**
     * Возвращает признак начала строки символов с заданной подкстроки
     *
     * @param string $haystack   Строка символов
     * @param string $needle     Подстрока
     * @param bool   $ignoreCase Игнорировать регистр
     *
     * @return bool
     */
    function startsWith(string $haystack, string $needle, bool $ignoreCase = false): bool;

    /**
     * Возвращает признак завершения строки символов заданной подстрокой
     *
     * @param string $haystack   Строка символов
     * @param string $needle     Подстрока
     * @param bool   $ignoreCase Игнорировать регистр
     *
     * @return bool
     */
    function endsWith(string $haystack, string $needle, bool $ignoreCase = false): bool;

    /**
     * Возвращает строку с приведенным к верхенму регистру первым символом
     *
     * @param string $buf Строка символов
     *
     * @return string
     */
    function upperCaseFirst(string $buf): string;
}
