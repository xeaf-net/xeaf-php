<?php

/**
 * StringUtils.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

use XEAF\API\App\Factory;
use XEAF\API\Core\FactoryObject;
use XEAF\API\Utils\Interfaces\IStringUtils;

/**
 * Реализует методы работы со строками
 *
 * @package  XEAF\API\Utils
 */
class StringUtils extends FactoryObject implements IStringUtils {

    /**
     * Пустая строка
     */
    private const EMPTY_STRING = '';

    /**
     * Шаблон уникального идентификатора
     */
    private const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-(8|9|a|b)[0-9a-f]{3}-[0-9a-f]{12}$/i';

    /**
     * Возвращает пустую строку
     *
     * @return string
     */
    public function emptyString(): string {
        return self::EMPTY_STRING;
    }

    /**
     * Возвращает признак пустой строки
     *
     * @param string|null $buf Строка символов
     *
     * @return bool
     */
    public function isEmpty(?string $buf): bool {
        return is_null($buf) || mb_strlen($buf) == 0;
    }

    /**
     * Возвращает NULL для пустой строки
     *
     * @param string|null $buf Строка символов
     *
     * @return string|null
     */
    public function emptyToNull(?string $buf): ?string {
        return $this->isEmpty($buf) ? null : $buf;
    }

    /**
     * Преобразует строку в целое число
     *
     * @param string|null $buf     Строка символов
     * @param int         $onError Результат при ошибке
     *
     * @return int
     */
    public function stringToInteger(?string $buf, int $onError = 0): int {
        return $this->isInteger($buf) ? intval($buf) : $onError;
    }

    /**
     * Преобразует строку в действительное число
     *
     * @param string|null $buf     Строка символов
     * @param float       $onError Результат при ошибке
     *
     * @return float
     */
    public function stringToFloat(?string $buf, float $onError = 0): float {
        return $this->isFloat($buf) ? floatval($buf) : $onError;
    }

    /**
     * Преобразует строку в дату и время
     *
     * @param string|null $buf     Строка символов
     * @param int|null    $onError Результат при ошибке
     *
     * @return int|null
     */
    public function stringToDateTime(?string $buf, int $onError = null): ?int {
        return $this->isDateTime($buf) ? strtotime($buf) : $onError;
    }

    /**
     * Проверяет содержит ли переданная строка целое число
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    public function isInteger(?string $buf): bool {
        $result = false;
        if (!$this->isEmpty($buf) && is_numeric($buf)) {
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
    public function isFloat(?string $buf): bool {
        $result = false;
        if (!$this->isEmpty($buf) && is_numeric($buf)) {
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
    public function isDateTime(?string $buf): bool {
        return !$this->isEmpty($buf) && strtotime($buf) !== false;
    }

    /**
     * Проверяет является ли преданная строка UUID
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    public function isUUID(?string $buf): bool {
        return preg_match(self::UUID_PATTERN, $buf);
    }

    /**
     * Провряет является ли переданная строка адресом электронной почты
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    public function isEmail(?string $buf): bool {
        $filter = filter_var($buf, FILTER_VALIDATE_EMAIL);
        return $filter !== null && $filter !== false;
    }

    /**
     * Проверяет является ли преданная строка идентификатором объекта
     *
     * @param string|null $buf Проверяемая строка
     *
     * @return bool
     */
    public function isObjectId(?string $buf): bool {
        return $this->isUUID($buf) || $this->isInteger($buf);
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
    public function startsWith(string $haystack, string $needle, bool $ignoreCase = false): bool {
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
    public function endsWith(string $haystack, string $needle, bool $ignoreCase = false): bool {
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
    public function upperCaseFirst(string $buf): string {
        return $this->isEmpty($buf) ? $this->emptyString()
            : mb_strtoupper(mb_substr($buf, 0, 1)) . mb_strtolower(mb_substr($buf, 1));
    }

    /**
     * Возвращает единичный экемпляр объекта
     *
     * @return \XEAF\API\Utils\Interfaces\IStringUtils
     */
    public static function getInstance(): IStringUtils {
        $result = Factory::getFactoryObject(self::class, FactoryObject::DEFAULT_NAME);
        assert($result instanceof IStringUtils);
        return $result;
    }
}
