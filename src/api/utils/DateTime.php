<?php

/**
 * DateTime.php
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
 * Реализует методы работы с датой и временем
 *
 * @package  XEAF\API\Utils
 */
class DateTime {

    /**
     * SQL формат представления даты
     */
    public const SQL_DATE_FORMAT = 'Y-m-d';

    /**
     * SQL формат представления даты и времени
     */
    public const SQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Количество секунд в часе
     */
    public const SECONDS_PER_HOUR = 60 * 60;

    /**
     * Количество секунд в сутках
     */
    public const SECONDS_PER_DAY = 24 * 60 * 60;

    /**
     * Пустое значение даты и времени
     */
    public const NULL_SQL_DATETIME = '1970-01-01 00:00:00';

    /**
     * Возвращает текущие дату и время
     *
     * @return int
     */
    public static function now(): int {
        return time();
    }

    /**
     * Возвращает текущую дату
     *
     * @return int
     */
    public static function today(): int {
        return self::dateTimeToDate(self::now());
    }

    /**
     * Возвращает дату, предшествующую заданной
     *
     * @param int|null $date Заданная дата
     *
     * @return int
     */
    public static function yesterday(?int $date = null): int {
        return is_null($date) ? self::today() - self::SECONDS_PER_DAY
            : self::dateTimeToDate($date) - self::SECONDS_PER_DAY;
    }

    /**
     * Возвращает дату, следующую за заданной
     *
     * @param int|null $date Заданная дата
     *
     * @return int
     */
    public static function tomorrow(?int $date = null): int {
        return is_null($date) ? self::today() + self::SECONDS_PER_DAY
            : self::dateTimeToDate($date) + self::SECONDS_PER_DAY;
    }

    /**
     * Возвращает первый день месяца заданной даты
     *
     * @param int $date Заданная дата
     *
     * @return int
     */
    public static function firstDayOfMonth(int $date): int {
        return Strings::stringToDateTime(date('Y-m-01', $date));
    }

    /**
     * Возвращает последний день месяца заданной даты
     *
     * @param int $date Заданная дата
     *
     * @return int
     */
    public static function lastDayOfMonth(int $date): int {
        return Strings::stringToDateTime(date('Y-m-t', $date));
    }

    /**
     * Возвращает первый день года заданной даты
     *
     * @param int $date Заданная дата
     *
     * @return int
     */
    public static function firstDayOfYear(int $date): int {
        return Strings::stringToDateTime(date('Y-01-01', $date));
    }

    /**
     * Возвращает последний день года заданной даты
     *
     * @param int $date Заданная дата
     *
     * @return int
     */
    public static function lastDayOfYear(int $date): int {
        return Strings::stringToDateTime(date('Y-12-31', $date));
    }

    /**
     * Возвращает день месяца
     *
     * @param int $date Дата
     *
     * @return int
     */
    public static function getDay(int $date): int {
        $tmp = date_parse_from_format(self::SQL_DATE_FORMAT, self::dateToSQL($date));
        return $tmp['day'];
    }

    /**
     * Возвращает месяц
     *
     * @param int $date Дата
     *
     * @return int
     */
    public static function getMonth(int $date): int {
        $tmp = date_parse_from_format(self::SQL_DATE_FORMAT, self::dateToSQL($date));
        return $tmp['month'];
    }

    /**
     * Возвращает год
     *
     * @param int $date Дата
     *
     * @return int
     */
    public static function getYear(int $date): int {
        $tmp = date_parse_from_format(self::SQL_DATE_FORMAT, self::dateToSQL($date));
        return $tmp['year'];
    }

    /**
     * Возвращает часы
     *
     * @param int $date Дата
     *
     * @return int
     */
    public static function getHours(int $date): int {
        $tmp = date_parse_from_format(self::SQL_DATE_FORMAT, self::dateToSQL($date));
        return $tmp['hour'];
    }

    /**
     * Возвращает минуты
     *
     * @param int $date Дата
     *
     * @return int
     */
    public static function getMinutes(int $date): int {
        $tmp = date_parse_from_format(self::SQL_DATE_FORMAT, self::dateToSQL($date));
        return $tmp['minute'];
    }

    /**
     * Возвращает секунды
     *
     * @param int $date Дата
     *
     * @return int
     */
    public static function getSeconds(int $date): int {
        $tmp = date_parse_from_format(self::SQL_DATE_FORMAT, self::dateToSQL($date));
        return $tmp['second'];
    }

    /**
     * Обрезает значение времени
     *
     * @param int $dateTime Дата и время
     *
     * @return int
     */
    public static function dateTimeToDate(int $dateTime): int {
        return strtotime(date(self::SQL_DATE_FORMAT, $dateTime));
    }

    /**
     * Возвращает представление даты и времени в формате кеша
     *
     * @param int $dateTime Дата и время
     *
     * @return string
     */
    public static function dateTimeToCache(int $dateTime): string {
        return gmdate("D, d M Y H:i:s", $dateTime) . " GMT";
    }

    /**
     * Преобразует дату в SQL формат
     *
     * @param int $date Дата
     *
     * @return string
     */
    public static function dateToSQL(int $date): string {
        return date(self::SQL_DATE_FORMAT, $date);
    }

    /**
     * Преобразует дату и время в SQL формат
     *
     * @param int $dateTime Дата и время
     *
     * @return string
     */
    public static function dateTimeToSQL(int $dateTime): string {
        return date(self::SQL_DATETIME_FORMAT, $dateTime);
    }

    /**
     * Преобразует SQL представление в дату
     *
     * @param string|null $date SQL представление даты
     *
     * @return int
     */
    public static function dateFromSQL(?string $date): int {
        $dateTime = !$date ? self::NULL_SQL_DATETIME : $date;
        return self::dateTimeToDate(Strings::stringToDateTime($dateTime));
    }

    /**
     * Преобразует SQL представление в дату и время
     *
     * @param string|null $dateTime SQL представление даты и времени
     *
     * @return int
     */
    public static function dateTimeFromSQL(?string $dateTime): int {
        return !$dateTime ? Strings::stringToDateTime(self::NULL_SQL_DATETIME) : Strings::stringToDateTime($dateTime);
    }
}
