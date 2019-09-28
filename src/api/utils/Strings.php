<?php

/**
 * Strings.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

use XEAF\API\Core\FactoryObject;

/**
 * Реализует методы работы со строками
 *
 * @package  XEAF\API\Utils
 */
class Strings {
    /**
     * Пустая строка
     */
    public const EMPTY = '';

    /**
     * Шаблон уникального идентификатора
     */
    public const UUID_PATTERN = '/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i';

    /**
     * Шаблон адреса электронной почты
     */
    public const EMAIL_PATTERN = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';

    /**
     * Разделитель конфигурации провайдера
     */
    public const PROVIDER_SEPARATOR = '://';

    /**
     * Возвращает признак пустой строки
     *
     * @param string|null $buf Строка символов
     *
     * @return bool
     */
    public static function isEmpty(?string $buf): bool {
        return is_null($buf) || $buf === self::EMPTY;
    }

    /**
     * Возвращает NULL для пустой строки
     *
     * @param string|null $buf Строка символов
     *
     * @return string|null
     */
    public static function emptyToNull(?string $buf): ?string {
        return self::isEmpty($buf) ? null : $buf;
    }

    /**
     * Преобразует строку в целое число
     *
     * @param string|null $buf     Строка символов
     * @param int         $onError Результат при ошибке
     *
     * @return int
     */
    public static function stringToInteger(?string $buf, int $onError = 0): int {
        return self::isInteger($buf) ? intval($buf) : $onError;
    }

    /**
     * Преобразует строку в действительное число
     *
     * @param string|null $buf     Строка символов
     * @param float       $onError Результат при ошибке
     *
     * @return float
     */
    public static function stringToFloat(?string $buf, float $onError = 0): float {
        return self::isFloat($buf) ? floatval($buf) : $onError;
    }

    /**
     * Преобразует строку в дату и время
     *
     * @param string|null $buf     Строка символов
     * @param int         $onError Результат при ошибке
     *
     * @return int
     */
    public static function stringToDateTime(?string $buf, int $onError = 0): int {
        return self::isDateTime($buf) ? strtotime($buf) : $onError;
    }

    /**
     * Проверяет содержит ли переданная строка целое число
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    public static function isInteger(?string $buf): bool {
        $result = false;
        if (!is_null($buf) && is_numeric($buf)) {
            $val    = intval($buf);
            $result = $val == $buf;
        }
        return $result;
    }

    /**
     * Проверяет содержит ли переданная строка действительное число
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    public static function isFloat(?string $buf): bool {
        $result = false;
        if (!is_null($buf) && is_numeric($buf)) {
            $val    = floatval($buf);
            $result = $val == $buf;
        }
        return $result;
    }

    /**
     * Проверяет содержит ли переданная строка дату и время
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    public static function isDateTime(?string $buf): bool {
        return !is_null($buf) && strtotime($buf) !== false;
    }

    /**
     * Проверяет является ли преданная строка UUID
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    public static function isUUID(?string $buf): bool {
        return preg_match(self::UUID_PATTERN, $buf);
    }

    /**
     * Провряет является ли переданная строка адресом электронной почты
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    public static function isEmail(?string $buf): bool {
        return filter_var($buf, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Проверяет является ли преданная строка идентификатором объекта
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    public static function isObjectId(?string $buf): bool {
        return self::isUUID($buf) || self::isInteger($buf);
    }

    /**
     * Возвращает признак начала строки символов с заданной подкстроки
     *
     * @param string $haystack   Строка символов
     * @param string $needle     Подстрока
     * @param bool   $ignoreCase Игнорировать регистр
     *
     * @return bool
     */
    public static function startsWith(string $haystack, string $needle, bool $ignoreCase = false): bool {
        $length = mb_strlen($needle);
        return $ignoreCase ? (mb_substr(mb_strtoupper($haystack), 0, $length) === mb_strtoupper($needle))
            : (mb_substr($haystack, 0, $length) === $needle);
    }

    /**
     * Возвращает признак завершения строки символов заданной подстрокой
     *
     * @param string $haystack   Строка символов
     * @param string $needle     Подстрока
     * @param bool   $ignoreCase Игнорировать регистр
     *
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle, bool $ignoreCase = false): bool {
        $length = mb_strlen($needle);
        if ($length == 0) {
            return true;
        }
        return $ignoreCase ? (mb_substr(mb_strtoupper($haystack), -$length) === mb_strtoupper($needle))
            : (mb_substr($haystack, -$length) === $needle);
    }

    /**
     * Возвращает строку с приведенным к верхенму регистру первым символом
     *
     * @param string $buf Строка символов
     *
     * @return string
     */
    public static function upperCaseFirst(string $buf): string {
        return self::isEmpty($buf) ? self::EMPTY
            : mb_strtoupper(mb_substr($buf, 0, 1)) . mb_strtolower(mb_substr($buf, 1));
    }

    /**
     * Разбирает параметр конфигурации провайдера
     *
     * @param string $provider Провайдер
     *
     * @return array
     */
    public static function parseProviderConfig(string $provider): array {
        $result = explode(self::PROVIDER_SEPARATOR, $provider);
        if (count($result) == 1 || Strings::isEmpty($result[1])) {
            $result[1] = FactoryObject::DEFAULT_NAME;
        }
        return $result;
    }
}
