<?php

/**
 * Logger.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

use Throwable;
use XEAF\API\App\Factory;
use XEAF\API\Models\Config\LoggerConfig;

/**
 * Реализует методы ведения журнала операций
 *
 * @package  XEAF\API\Utils
 */
class Logger {

    /**
     * Фатальная невосстановимая ошибка
     */
    public const FATAL = 0;

    /**
     * Ошибки
     */
    public const ERROR = 1;

    /**
     * Предупреждения
     */
    public const WARNING = 2;

    /**
     * Информация
     */
    public const INFO = 3;

    /**
     * Отладка
     */
    public const DEBUG = 4;

    /**
     * Строковые идентификаторы уровня
     */
    public const LEVEL_NAMES = [
        self::FATAL   => 'fatal',
        self::ERROR   => 'error',
        self::WARNING => 'warning',
        self::INFO    => 'info',
        self::DEBUG   => 'debug'
    ];

    /**
     * Префиксы сообщений
     */
    private const LEVEL_PREFIX = [
        self::FATAL   => 'FTL',
        self::ERROR   => 'ERR',
        self::WARNING => 'WNG',
        self::INFO    => 'INF',
        self::DEBUG   => 'DBG'
    ];

    /**
     * Префикс имени файла журнала по умолчанию
     */
    protected const FILE_NAME = 'xeaf';

    /**
     * Расширение имен файла журнала
     */
    protected const FILE_NAME_EXT = 'log';

    /**
     * Имя раздела файла конфигурации
     */
    protected const CONFIG_SECTION = 'logger';

    /**
     * Уровень записей журнала
     * @var int
     */
    private static $_level = self::ERROR;

    /**
     * Префикс имени файла журнала
     * @var string
     */
    private static $_prefix = self::FILE_NAME;

    /**
     * Директория файлов журналов
     * @var string
     */
    private static $_path = '/var/log';

    /**
     * Имя файла журнала
     * @var string|null
     */
    private static $_fileName = null;

    /**
     * Признак инициализации
     * @var bool
     */
    private static $_initialized = false;

    /**
     * Возвращает уровень записей журнала
     *
     * @return int
     */
    public static function getLevel(): int {
        if (!self::$_initialized) {
            self::initialize();
        }
        $result = self::$_level;
        if ($result == self::DEBUG && !__XEAF_DEBUG_MODE__) {
            $result = self::INFO;
        }
        return $result;
    }

    /**
     * Задает уровень записей журнала
     *
     * @param int $level Уровень записей журнала
     *
     * @return void
     */
    public static function setLevel(int $level): void {
        if ($level >= self::FATAL && $level <= self::DEBUG) {
            self::$_level = $level;
        }
    }

    /**
     * Выводит в журнал сообщение о фатальной невостановимой ошибке
     *
     * @param string          $errorMsg Текст сообщения
     * @param \Throwable|null $reason   Причина возникновения ошибки
     *
     * @return void
     */
    public static function fatalError(string $errorMsg, Throwable $reason = null): void {
        if (ob_get_level()) {
            ob_end_clean();
        }
        $text = self::LEVEL_PREFIX[self::FATAL] . ': ' . $errorMsg;
        if (__XEAF_DEBUG_MODE__ && $reason != null) {
            print $text;
            print_r($reason);
            die();
        } else {
            die($text);
        }
    }

    /**
     * Выводит в журнал сообщение об ошибке
     *
     * @param string     $message Текст сообщения об ошибке
     * @param mixed|null $data    Дополнительная информация
     *
     * @return void
     */
    public static function error(string $message, $data = null): void {
        self::writeLog(self::ERROR, $message, $data);
    }

    /**
     * Выводит в журнал предупреждение
     *
     * @param string     $message Текст сообщения об ошибке
     * @param mixed|null $data    Дополнительная информация
     *
     * @return void
     */
    public static function warning(string $message, $data = null): void {
        self::writeLog(self::WARNING, $message, $data);
    }

    /**
     * Выводит в журнал информационное сообщение
     *
     * @param string     $message Текст сообщения
     * @param mixed|null $data    Дополнительная информация
     *
     * @return void
     */
    public static function info(string $message, $data = null): void {
        self::writeLog(self::INFO, $message, $data);
    }

    /**
     * Выводит в журнал отладочную информацию
     *
     * @param string     $message Текст сообщения
     * @param mixed|null $data    Дополнительная информация
     *
     * @return void
     */
    public static function debug(string $message, $data = null): void {
        self::writeLog(self::DEBUG, $message, $data);
    }

    /**
     * Возвращает имя файла журнала
     *
     * @return string
     */
    protected static function logFileName(): string {
        if (!self::$_fileName) {
            $name            = self::$_prefix . '-' . DateTime::dateToSQL(time()) . '.' . self::FILE_NAME_EXT;
            self::$_fileName = self::$_path . '/' . $name;
        }
        return self::$_fileName;
    }

    /**
     * Возвращает подготовленный текст для записи в журнал
     *
     * @param int        $level   Уровень записи
     * @param string     $message Текст сообщения
     * @param mixed|null $data    Дополнительная информация
     *
     * @return string
     */
    protected static function logText(int $level, string $message, $data = null): string {
        $prefix = '[' . self::LEVEL_PREFIX[$level] . '] ';
        $time   = DateTime::dateTimeToSQL(time());
        $debug  = __XEAF_DEBUG_MODE__ && $data != null ? "\n" . print_r($data, true) : '';
        $lines  = explode("\n", $time . ' ' . $message . $debug);
        return $prefix . implode("\n" . $prefix, $lines) . "\n";
    }

    /**
     * Записывает информациюв файл журнала
     *
     * @param int        $level   Уроверь сообщения
     * @param string     $message Текст сообщения
     * @param mixed|null $data    Дополнительная информация
     *
     * @return void
     */
    protected static function writeLog(int $level, string $message, $data = null): void {
        if ($level <= self::getLevel()) {
            $text = self::logText($level, $message, $data);
            $file = fopen(self::logFileName(), 'a+') or self::fatalError('Could not open log file.');
            fputs($file, $text);
            fclose($file);
        }
    }

    /**
     * Инициализирует параметры журнала
     *
     * @return void
     */
    protected static function initialize() {
        $config             = self::loadConfig();
        self::$_level       = $config->level;
        self::$_prefix      = $config->prefix;
        self::$_path        = $config->path;
        self::$_initialized = true;
    }

    /**
     * Загружает параметры конфигурации
     *
     * @return \XEAF\API\Models\Config\LoggerConfig
     */
    protected static function loadConfig(): LoggerConfig {
        $data = null;
        try {
            $config = Factory::getConfiguration();
            $data   = $config->getSection(self::CONFIG_SECTION);
        } catch (Throwable $reason) {
            self::fatalError($reason->getMessage(), $reason);
        }
        return new LoggerConfig($data);
    }
}
