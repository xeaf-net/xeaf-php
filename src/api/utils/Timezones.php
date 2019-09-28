<?php

/**
 * Timezones.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

use DateTimeZone;
use Throwable;

/**
 * Реализует методы работы с временными зонами
 *
 * @package  XEAF\API\Utils
 */
class Timezones {

    /**
     * Среднее время по Гринвичу
     */
    public const GMT = 'GMT';

    /**
     * Всемирное координированное время
     */
    public const UTC = 'UTC';

    /**
     * Список временных зон
     * @var array
     */
    private static $_timezones = [];

    /**
     * Возвращает массив временных зон
     *
     * @return array
     */
    public static function timezoneList(): array {
        self::buildTimezoneList();
        return self::$_timezones;
    }

    /**
     * Возвращает абревиатуру для временной зоны
     *
     * @param string $timezone Временная зона
     *
     * @return string
     */
    public static function timezoneAbbr(string $timezone): string {
        try {
            $dtz    = new \DateTime('now', new DateTimeZone($timezone));
            $result = $dtz->format('T');
        } catch (Throwable $e) {
            $result = '';
        }
        return $result;
    }

    /**
     * Строит список временных зон
     *
     * @return void
     */
    protected static function buildTimezoneList(): void {
        if (!self::$_timezones) {
            try {
                $offsets = [];
                $now     = new \DateTime('now', new DateTimeZone(self::UTC));
                $list    = DateTimeZone::listIdentifiers();
                foreach ($list as $timezone) {
                    $now->setTimezone(new DateTimeZone($timezone));
                    $offsets[]                   = $offset = $now->getOffset();
                    self::$_timezones[$timezone] = '(' . self::formatOffset($offset) . ') ' . self::formatTimezone($timezone);
                }
                array_multisort($offsets, self::$_timezones);
            } catch (Throwable $e) {
                self::$_timezones = [];
            }
        }
    }

    /**
     * Форматирует представление смещения
     *
     * @param string $offset Смещение
     *
     * @return string
     */
    protected static function formatOffset(string $offset): string {
        $num     = Strings::stringToInteger($offset);
        $hours   = $num / 3600;
        $minutes = abs($num % 3600 / 60);
        return self::GMT . sprintf('%+03d:%02d', $hours, $minutes);
    }

    /**
     * Форматирует представление временной зоны
     *
     * @param string $timeZone Временная зона
     *
     * @return string
     */
    protected static function formatTimezone(string $timeZone): string {
        return str_replace(['/', '_', 'St '], [', ', ' ', 'St. '], $timeZone);
    }
}
