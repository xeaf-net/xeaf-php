<?php

/**
 * TemplateEngine.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Utils;

use Smarty;
use Smarty_Internal_Template;
use Throwable;
use XEAF\API\App\Factory;
use XEAF\API\Utils\FileSystem;
use XEAF\API\Utils\Parameters;
use XEAF\API\Utils\Reflection;
use XEAF\Smarty\Utils\Exceptions\TemplateException;
use XEAF\Smarty\Utils\SmartyWrapper;
use XEAF\UI\App\Application;
use XEAF\UI\App\Router;
use XEAF\UI\Core\Plugin;
use XEAF\UI\Core\Template;
use XEAF\UI\Models\Results\HtmlResult;

/**
 * Реализует классы разбора шаблонов элементов
 *
 * @package  XEAF\UI\Utils
 */
class TemplateEngine extends SmartyWrapper {

    /**
     * Идентификатор переменной модели даных действия
     */
    private const ACTION_MODEL = 'actionModel';

    /**
     * Идентификатор переменной модели данных плагина
     */
    private const PLUGIN_MODEL = 'pluginModel';

    /**
     * Идентификатор переменной модели данных шаблона
     */
    private const TEMPLATE_MODEL = 'templateModel';

    /**
     * Идентификатор переменной заголовка страницы
     */
    private const PAGE_TITLE = 'pageTitle';

    /**
     * Идентификатор переменной метаданных страницы
     */
    private const PAGE_META = 'pageMeta';

    /**
     * Объект данных разметки страницы
     * @var \XEAF\UI\Core\Template|null
     */
    protected static $_template = null;

    /**
     * Результат исполнения действия
     * @var \XEAF\UI\Models\Results\HtmlResult|null
     */
    protected static $_actionResult = null;

    /**
     * Текущее содержание страницы
     * @var string
     */
    protected static $_pageContent = '';

    /**
     * Создает объект шаблонизатора
     *
     * @return \Smarty
     * @throws \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    protected static function createEngine(): Smarty {
        $result = self::createSmarty();
        self::initEnginePlugins($result);
        return $result;
    }

    /**
     * Инициализирует плагины шаблонизатора
     *
     * @param \Smarty $smarty
     *
     * @return void
     * @throws \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    protected static function initEnginePlugins(Smarty $smarty): void {
        try {
            // -- Плагины -------
            $smarty->registerPlugin("function", "content", self::class . "::printPageContent");
            $smarty->registerPlugin("function", "plugin", self::class . "::printPluginContent");
        } catch (Throwable $reason) {
            throw TemplateException::errorInitializingDefaultPlugins($reason);
        }
    }

    /**
     * Разбирает файл шаблона модуля
     *
     * @param \XEAF\UI\Models\Results\HtmlResult $actionResult Результат исполнения действия
     *
     * @return string
     * @throws \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    public static function parseModule(HtmlResult $actionResult): string {
        $smarty = self::createEngine();
        try {
            self::$_actionResult = $actionResult;
            // $smarty->assignByRef(self::ACTION_MODEL, self::$_actionResult->dataObject);
            $smarty->assign(self::ACTION_MODEL, self::$_actionResult->dataObject);
            return $smarty->fetch($actionResult->layoutFile);
        } catch (Throwable $reason) {
            throw TemplateException::errorParsingTemplateFile($actionResult->layoutFile, $reason);
        }
    }

    /**
     * Разбирает файл шаблона разметки портала
     *
     * @param \XEAF\UI\Core\Template $template    Шаблон разметки страницы
     * @param string                 $pageContent Содержимое страницы
     *
     * @return string
     * @throws \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    public static function parseTemplate(Template $template, string $pageContent = ''): string {
        $smarty = self::createEngine();
        try {
            self::$_template    = $template;
            self::$_pageContent = $pageContent;
            // $smarty->assignByRef(self::ACTION_MODEL, self::$_actionResult->dataObject);
            $smarty->assign(self::ACTION_MODEL, self::$_actionResult->dataObject);
            $smarty->assign(self::TEMPLATE_MODEL, $template->dataObject);
            $smarty->assign(self::PAGE_TITLE, $template->pageTitle);
            $smarty->assign(self::PAGE_META, $template->pageMeta);
            return $smarty->fetch($template->layoutFile) . self::templateSignature();
        } catch (Throwable $reason) {
            throw TemplateException::errorParsingTemplateFile($template->layoutFile, $reason);
        }
    }

    /**
     * Создает плагин
     *
     * @param string $pluginName Идентификатор плагина
     *
     * @return \XEAF\UI\Core\Plugin
     * @throws \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    protected static function createPlugin(string $pluginName): ?Plugin {
        $pluginClassName = Router::pluginClassName($pluginName);
        if ($pluginClassName) {
            return new $pluginClassName(self::$_actionResult, self::$_template);
        }
        throw TemplateException::errorInitializingPlugin($pluginName);
    }

    /**
     * Реализует печать содержимого страницы
     *
     * @param array   $params Параметры вызова плагина
     * @param \Smarty $smarty Объект шаблонизатора
     *
     * @return string
     */
    public static function printPageContent(/** @noinspection PhpUnusedParameterInspection */ $params, $smarty) {
        return self::$_pageContent;
    }

    /**
     * Обрабатывает вызов плагина
     *
     * @param array   $params Параметры вызова плагина
     * @param \Smarty $smarty Объект шаблонизатора
     *
     * @return string
     * @throws \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    public static function printPluginContent($params, $smarty) {
        $result = '';
        assert($smarty instanceof Smarty_Internal_Template);
        $pluginName = $params['name'] ?? null;
        if ($pluginName) {
            try {
                $smarty = self::createEngine();
                $plugin = self::createPlugin($pluginName);
                // $smarty->assignByRef(self::ACTION_MODEL, self::$_actionResult->dataObject);
                // $smarty->assignByRef(self::TEMPLATE_MODEL, self::$_template);
                $smarty->assign(self::ACTION_MODEL, self::$_actionResult->dataObject);
                $smarty->assign(self::TEMPLATE_MODEL, self::$_template);
                $smarty->assign(self::PLUGIN_MODEL, $plugin->getDataObject($params));
                $result = $smarty->fetch($plugin->getLayoutFile());
            } catch (Throwable $reason) {
                throw TemplateException::errorProcessingPlugin($pluginName, $reason);
            }
        }
        return $result;
    }

    /**
     * Возвращает имя файла шаблона построения по умолчанию
     *
     * @param string $className Имя класса
     *
     * @return string
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    public static function defaultLayoutFile(string $className): string {
        $result   = null;
        $params   = Factory::getParameters();
        $baseName = Reflection::classFileName($className);
        if ($params->actionMode != Parameters::DEFAULT_ACTION_MODE) {
            $prefix   = FileSystem::removeFileNameExt($baseName);
            $fileName = $prefix . '-' . ucfirst($params->actionMode) . '.' . self::FILE_NAME_EXT;
            if (FileSystem::fileExists($fileName)) {
                $result = $fileName;
            }
        }
        if (!$result) {
            $result = FileSystem::changeFileNameExt($baseName, self::FILE_NAME_EXT);
        }
        return $result;
    }

    /**
     * Возвращает подпись к шаблону
     *
     * @return string
     */
    protected static function templateSignature(): string {
        $time    = microtime(true) - Application::startTime();
        $version = __XEAF_PHP_VERSION__;
        return "<!-- Generated by XEAF-PHP-UI v$version in $time -->";
    }
}
