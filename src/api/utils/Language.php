<?php

/**
 * Language.php
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

/**
 * Реализует методы работы с языковыми переменными
 *
 * @package  XEAF\API\Utils
 */
class Language {

    /**
     * Английский язык
     */
    public const EN = 'en_GB';

    /**
     * Название английского языка
     */
    public const EN_TITLE = 'English';

    /**
     * Русский язык
     */
    public const RU = 'ru_RU';

    /**
     * Название русского языка
     */
    public const RU_TITLE = 'Русский';

    /**
     * Язык по умолчанию
     */
    public const DEFAULT_LANGUAGE = self::EN;

    /**
     * Директрия файлов языковых переменных
     */
    public const LANGUAGE_FILE_DIR = 'l10n';

    /**
     * Расширение имен файлов языковых переменных
     */
    public const LANGUAGE_FILE_EXT = 'lng';

    /**
     * Имя секции глобальных переменных
     */
    public const GLOBAL_SECTION = 'global';

    /**
     * Текущий выбранный язык
     * @var string
     */
    private static $_language = self::DEFAULT_LANGUAGE;

    /**
     * Поддерживаемые языки
     * @var array
     */
    private static $_languages = [];

    /**
     * Языковые переменные
     * @var array
     */
    private static $_languageVars = [];

    /**
     * Заггруженные файлы
     * @var array
     */
    private static $_languageFiles = [];

    /**
     * Загружает языковой файл для заданного класса
     *
     * @param string $className Имя класса
     *
     * @return void
     */
    public static function loadClassLanguageFile(string $className): void {
        $fileName = self::classLanguageFile($className);
        if ($fileName && !in_array($fileName, self::$_languageFiles)) {
            $data = parse_ini_file($fileName, true);
            foreach ($data as $section => $sectionData) {
                foreach ($sectionData as $name => $value) {
                    $name = $section != self::GLOBAL_SECTION ? $section . '.' . $name : $name;
                    self::setLanguageVar($name, $value);
                }
            }
            self::$_languageFiles[] = $fileName;
        }
    }

    /**
     * Возвращает имя языкового файла для заданного класса
     *
     * @param string $className Имя класса
     *
     * @return string|null
     */
    protected static function classLanguageFile(string $className): ?string {
        try {
            $cf   = Reflection::classFileName($className);
            $dir  = FileSystem::getFileDir($cf) . '/' . self::LANGUAGE_FILE_DIR;
            $file = FileSystem::getFileName($cf) . '.' . self::$_language . '.' . self::LANGUAGE_FILE_EXT;
            $path = $dir . '/' . $file;
            return FileSystem::fileExists($path) ? $path : null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Возвращает текущий язык
     *
     * @return string
     */
    public static function getLanguage(): string {
        return self::$_language;
    }

    /**
     * Задает текущий язык
     *
     * @param string $language Язык
     *
     * @return void
     */
    public static function setLanguage(string $language): void {
        if (self::isSupported($language)) {
            self::$_language = $language;
            self::clearLanguageData();
        }
    }

    /**
     * Возвращает значение языковой переменной
     *
     * @param string $name Идентификатор
     * @param array  $args Аргументы
     *
     * @return string
     */
    public static function getLanguageVar(string $name, array $args = []): string {
        if (isset(self::$_languageVars[$name])) {
            return vsprintf(self::$_languageVars[$name], $args);
        }
        return $name;
    }

    /**
     * Задает значение языковой переменной
     *
     * @param string $name  Идентификатор
     * @param string $value Значение
     *
     * @return void
     */
    public static function setLanguageVar(string $name, string $value): void {
        self::$_languageVars[$name] = $value;
    }

    /**
     * Возвращает значения языковых переменных
     *
     * @return array
     */
    public static function getLanguageVars(): array {
        return self::$_languageVars;
    }

    /**
     * Регистрирует новый поддерживаемый язык
     *
     * @param string $language Язык
     * @param string $title    Название
     *
     * @return void
     */
    public static function registerLanguage(string $language, string $title): void {
        self::$_languages[$language] = $title;
    }

    /**
     * Отменяет регистрацию поддерживаемого языка
     *
     * @param string $language Язык
     *
     * @return void
     */
    public static function unregisterLanguage(string $language): void {
        unset(self::$_languages[$language]);
    }

    /**
     * Возвращает признак поддержки языка
     *
     * @param string $language Язык
     *
     * @return bool
     */
    public static function isSupported(string $language): bool {
        return array_key_exists($language, self::supportedLanguages());
    }

    /**
     * Возвращает массив поддерживаемых языков
     *
     * @return array
     */
    public static function supportedLanguages(): array {
        if (!self::$_languages) {
            self::initSupportedLanguages();
        }
        return self::$_languages;
    }

    /**
     * Очищает данные об языковых переменных и загруженных файлах
     *
     * @return void
     */
    protected static function clearLanguageData(): void {
        self::$_languageVars  = [];
        self::$_languageFiles = [];
    }

    /**
     * Инициализирует список поддерживаемых языков
     *
     * @return void
     */
    protected static function initSupportedLanguages(): void {
        self::registerLanguage(self::EN, self::EN_TITLE);
        self::registerLanguage(self::RU, self::RU_TITLE);
    }
}
