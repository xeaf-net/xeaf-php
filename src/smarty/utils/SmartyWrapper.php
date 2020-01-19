<?php

/**
 * SmartyWrapper.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-SMARTY
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\Smarty\Utils;

use Smarty;
use Throwable;
use XEAF\API\App\Factory;
use XEAF\API\Core\DataObject;
use XEAF\API\Utils\Formatter;
use XEAF\API\Utils\Language;
use XEAF\API\Utils\Strings;
use XEAF\Smarty\Models\Config\SmartyConfig;
use XEAF\Smarty\Utils\Exceptions\TemplateException;

/**
 * Класс - обертка вокруг шаблонизатора Smarty
 *
 * @package  XEAF\Smarty\Utils
 */
class SmartyWrapper {

    /**
     * Расширение имени файла шаблона построения
     */
    public const FILE_NAME_EXT = 'tpl';

    /**
     * Идентификатор переменной URL портала
     */
    private const PORTAL_URL = 'portalURL';

    /**
     * Идентификатор переменной текущего URL
     */
    protected const ACTUAL_URL = 'actualURL';

    /**
     * Идентификатор переменной действия
     */
    protected const ACTION_NAME = 'actionName';

    /**
     * Идентификатор переменной режима действия
     */
    protected const ACTION_MODE = 'actionMode';

    /**
     * Идентификатор модели данных
     */
    protected const DATA_MODEL = 'dataModel';

    /**
     * Идентификатор переменной признака режима отладки
     */
    protected const DEBUG_MODE = 'debugMode';

    /**
     * Идентификатор раздела файла конфигурации
     */
    protected const CONFIG_SECTION = 'smarty';

    /**
     * Возвращает параметры конфигурации шаблонизатора
     *
     * @return \XEAF\Smarty\Models\Config\SmartyConfig
     * @throws \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    protected static function getSmartyConfig(): SmartyConfig {
        try {
            $config = Factory::getConfiguration();
            $data   = $config->getSection(self::CONFIG_SECTION, false);
            return new SmartyConfig($data);
        } catch (Throwable $reason) {
            throw TemplateException::errorInitializingSmarty($reason);
        }
    }

    /**
     * Создает и инициализирует объект Smarty
     *
     * @return \Smarty
     * @throws \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    protected static function createSmarty(): Smarty {
        $config = static::getSmartyConfig();
        $result = new Smarty();
        $result->setCacheDir($config->cacheDir);
        $result->setCompileDir($config->compileDir);
        $result->caching       = $config->enableCaching;
        $result->force_compile = $config->forceCompile;
        self::initPlugins($result);
        return $result;
    }

    /**
     * Инициализирует плагины Smarty
     *
     * @param \Smarty $smarty Объект шаблонизатора Smarty
     *
     * @return void
     * @throws \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    protected static function initPlugins(Smarty $smarty): void {
        try {
            // -- Модификаторы --
            $smarty->registerPlugin("modifier", "lang", self::class . "::printLangModifier");
            $smarty->registerPlugin("modifier", "int", self::class . "::printIntModifier");
            $smarty->registerPlugin("modifier", "money", self::class . "::printMoneyModifier");
            $smarty->registerPlugin("modifier", "number", self::class . "::printNumberModifier");
            $smarty->registerPlugin("modifier", "date", self::class . "::printDateModifier");
            $smarty->registerPlugin("modifier", "time", self::class . "::printTimeModifier");
            $smarty->registerPlugin("modifier", "dt", self::class . "::printDateTimeModifier");

            // -- Переменные ----
            $prm = Factory::getParameters();
            $url = Factory::getConfiguration()->portal->url;
            $smarty->assign(self::PORTAL_URL, $url);
            $smarty->assign(self::ACTUAL_URL, $url . $_SERVER['REQUEST_URI']);
            $smarty->assign(self::ACTION_NAME, $prm->actionName);
            $smarty->assign(self::ACTION_MODE, $prm->actionMode);
            $smarty->assign(self::DEBUG_MODE, __XEAF_DEBUG_MODE__);
        } catch (Throwable $reason) {
            throw TemplateException::errorInitializingDefaultPlugins($reason);
        }
    }

    /**
     * Разбирает файл шаблона
     *
     * @param string                         $layoutFile Имя файла шаблона
     * @param \XEAF\API\Core\DataObject|null $dataObject
     *
     * @return string
     * @throws \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    public static function parseTplFile(string $layoutFile, DataObject $dataObject = null): string {
        $smarty = self::createSmarty();
        try {
            $smarty->assignByRef(self::DATA_MODEL, $dataObject);
            return $smarty->fetch($layoutFile);
        } catch (Throwable $reason) {
            throw TemplateException::errorParsingTemplateFile($layoutFile, $reason);
        }
    }

    /**
     * Обрабатывает вызов модификатора языковой переменной
     *
     * @param mixed $name Идентификатор переменной
     *
     * @return string
     */
    public static function printLangModifier($name = null) {
        return Language::getLanguageVar($name);
    }

    /**
     * Обрабатывает вызов модификатора форматирования целых чисел
     *
     * @param null $text Форматируемый текст
     *
     * @return string
     */
    public static function printIntModifier($text = null) {
        if (Strings::isInteger($text)) {
            return Formatter::formatInteger($text);
        }
        return $text;
    }

    /**
     * Обрабатывает вызов модификатора форматирования денежных значений
     *
     * @param null $text Форматируемый текст
     *
     * @return string
     */
    public static function printMoneyModifier($text = null) {
        if (is_numeric($text)) {
            return Formatter::formatMoney($text);
        }
        return $text;
    }

    /**
     * Обрабатывает вызов модификатора форматирования чисел
     *
     * @param null $text Форматируемый текст
     * @param int  $dec  Количество десятичных цифр
     *
     * @return string
     */
    public static function printNumberModifier($text = null, $dec = 0) {
        if (is_numeric($text) && is_numeric($dec)) {
            return Formatter::formatNumeric($text, $dec);
        }
        return $text;
    }

    /**
     * Обрабатывает вызов модификатора форматирования даты
     *
     * @param null $text Форматируемый текст
     *
     * @return string
     */
    public static function printDateModifier($text = null) {
        if (Strings::isInteger($text)) {
            return Formatter::formatDate($text);
        }
        return $text;
    }

    /**
     * Обрабатывает вызов модификатора форматирования времени
     *
     * @param null $text Форматируемый текст
     *
     * @return string
     */
    public static function printTimeModifier($text = null) {
        if (Strings::isInteger($text)) {
            return Formatter::formatTime($text);
        }
        return $text;
    }

    /**
     * Обрабатывает вызов модификатора форматирования времени
     *
     * @param null $text Форматируемый текст
     *
     * @return string
     */
    public static function printDateTimeModifier($text = null) {
        if (Strings::isInteger($text)) {
            return Formatter::formatDateTime($text);
        }
        return $text;
    }
}
