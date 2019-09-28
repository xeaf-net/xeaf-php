<?php

/**
 * PgSqlProvider.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-DB-PGSQL
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\DB\PgSQL\Utils\Providers;

use XEAF\API\Utils\Language;
use XEAF\DB\Core\DatabaseProvider;

/**
 * Провайдер подключения к PostgreSQL
 *
 * @package  XEAF\DB\PgSQL\Utils\Providers
 */
class PgSqlProvider extends DatabaseProvider {

    /**
     * Идентификатор провайдера
     */
    public const PROVIDER_NAME = 'pgsql';

    /**
     * Возвращает SQL выражение преобразования к верхнему регистру
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    public function toLowerCase(string $expression): string {
        return 'lower(' . $expression . ')';
    }

    /**
     * Возвращает SQL выражение преобразования к верхнему регистру
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    public function toUpperCase(string $expression): string {
        return 'upper(' . $expression . ')';
    }

    /**
     * Возвращает SQL выражение форматирования даты
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    public function formatDate(string $expression): string {
        $format = Language::getLanguageVar('pgsql.dateFormat');
        return "to_char($expression, '$format')";
    }

    /**
     * Возвращает SQL выражение форматирования времени
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    public function formatTime(string $expression): string {
        $format = Language::getLanguageVar('pgsql.timeFormat');
        return "to_char($expression, '$format')";
    }

    /**
     * Возвращает SQL выражение форматирования даты и времени
     *
     * @param string $expression Исходное выражение
     *
     * @return string
     */
    public function formatDateTime(string $expression): string {
        $format = Language::getLanguageVar('pgsql.dateTimeFormat');
        return "to_char($expression, '$format')";
    }
}
