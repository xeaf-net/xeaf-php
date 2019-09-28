<?php

/**
 * Formatter.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

/**
 * Реализует методы форматирования данных
 *
 * @package  XEAF\API\Utils\Formatter
 */
class Formatter {

    /**
     * Раздел интернационализации
     */
    protected const I18N = 'i18n';

    /**
     * Идентификатор десятичной точки для числовых значений
     */
    protected const NUMERIC_DECIMAL_POINT = self::I18N . '.numericDecimalPoint';

    /**
     * Идентификатор разделителя разрядов для числовых значений
     */
    protected const NUMERIC_THOUSANDS_SEPARATOR = self::I18N . '.numericThousandsSeparator';

    /**
     * Идентификатор десятичной точки для денежных значений
     */
    protected const MONEY_DECIMAL_POINT = self::I18N . '.moneyDecimalPoint';

    /**
     * Идентификатор количества десятичных цифр для денежных значений
     */
    protected const MONEY_DECIMAL_DIGITS = self::I18N . '.moneyDecimalDigits';

    /**
     * Идентификатор разделителя разрядов для денежных значений
     */
    protected const MONEY_THOUSANDS_SEPARATOR = self::I18N . '.moneyThousandsSeparator';

    /**
     * Идентификатор формата представления даты
     */
    protected const DATE_FORMAT = self::I18N . '.dateFormat';

    /**
     * Идентификатор формата представления времени
     */
    protected const TIME_FORMAT = self::I18N . '.timeFormat';

    /**
     * Идентификатор формата представления даты и времени
     */
    protected const DATETIME_FORMAT = self::I18N . '.dateTimeFormat';

    /**
     * Форматирует логические значение
     *
     * @param bool $flag Логическое значение
     *
     * @return string
     */
    public static function formatBool(bool $flag): string {
        return $flag ? '1' : '0';
    }

    /**
     * Форматирует целое число
     *
     * @param int $number Форматируемое значение
     *
     * @return string
     */
    public static function formatInteger(int $number): string {
        $dp = Language::getLanguageVar(self::NUMERIC_DECIMAL_POINT);
        $ts = Language::getLanguageVar(self::NUMERIC_THOUSANDS_SEPARATOR);
        return number_format($number, 0, $dp, $ts);
    }

    /**
     * Форматирует действительное число
     *
     * @param float $number Форматируемое значение
     * @param int   $dec    Количество десятичных знаков
     *
     * @return string
     */
    public static function formatNumeric(float $number, int $dec = 2): string {
        $dp = Language::getLanguageVar(self::NUMERIC_DECIMAL_POINT);
        $ts = Language::getLanguageVar(self::NUMERIC_THOUSANDS_SEPARATOR);
        return number_format($number, $dec, $dp, $ts);
    }

    /**
     * Форматирует денежное значение
     *
     * @param float $number Форматируемое значение
     *
     * @return string
     */
    public static function formatMoney(float $number): string {
        $dp = Language::getLanguageVar(self::MONEY_DECIMAL_POINT);
        $dd = Language::getLanguageVar(self::MONEY_DECIMAL_DIGITS);
        $ts = Language::getLanguageVar(self::MONEY_THOUSANDS_SEPARATOR);
        return number_format($number, $dd, $dp, $ts);
    }

    /**
     * Форматирует дату
     *
     * @param int $date Дата
     *
     * @return string
     */
    public static function formatDate(int $date): string {
        $fmt = Language::getLanguageVar(self::DATE_FORMAT);
        return date($fmt, $date);
    }

    /**
     * Форматирует время
     *
     * @param int $time Время
     *
     * @return string
     */
    public static function formatTime(int $time): string {
        $fmt = Language::getLanguageVar(self::TIME_FORMAT);
        return date($fmt, $time);
    }

    /**
     * Форматирует дату и время
     *
     * @param int $dateTime Дата и время
     *
     * @return string
     */
    public static function formatDateTime(int $dateTime): string {
        $fmt = Language::getLanguageVar(self::DATETIME_FORMAT);
        return date($fmt, $dateTime);
    }
}
