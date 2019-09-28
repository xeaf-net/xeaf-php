<?php

/**
 * Application.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\App;

use XEAF\API\App\Configuration;
use XEAF\API\Core\ActionArgs;
use XEAF\API\Models\Results\ErrorResult;
use XEAF\API\Utils\Cookie;
use XEAF\API\Utils\HttpStatusCodes;
use XEAF\API\Utils\Serializer;
use XEAF\API\Utils\Session;
use XEAF\UI\Core\ActionResult;
use XEAF\UI\Core\Template;
use XEAF\UI\Models\Results\FormResult;
use XEAF\UI\Models\Results\HtmlResult;
use XEAF\UI\Models\Results\PageResult;
use XEAF\UI\Modules\Home\HomeModule;
use XEAF\UI\Modules\Tools\ResourcesModule;
use XEAF\UI\Modules\Tools\SessionModule;
use XEAF\UI\Plugins\HeaderFavIcon\HeaderFavIconPlugin;
use XEAF\UI\Plugins\HeaderMeta\HeaderMetaPlugin;
use XEAF\UI\Plugins\HeaderTitle\HeaderTitlePlugin;
use XEAF\UI\Plugins\ResourceLink\ResourceLinkPlugin;
use XEAF\UI\Templates\Portal\PortalTemplate;
use XEAF\UI\Utils\TemplateEngine;

/**
 * Класс приложения проекта интерфейса пользователя
 *
 * @package  XEAF\UI\App
 */
class Application extends \XEAF\API\App\Application {

    /**
     * Конструктор класса
     *
     * @param \XEAF\API\App\Configuration|null $configuration Параметры конфигурации
     * @param \XEAF\API\Core\ActionArgs|null   $parameters    Параметры вызова
     *
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public function __construct(Configuration $configuration = null, ActionArgs $parameters = null) {
        $configObject = $configuration ? $configuration : new Configuration();
        parent::__construct($configObject, $parameters);
    }

    /**
     * Объявляет используемые модули
     *
     * @return array
     */
    protected function declareModules(): array {
        $result                                      = parent::declareModules();
        $result[Router::HOME_MODULE]                 = HomeModule::class;
        $result[SessionModule::SESSION_MODULE]       = SessionModule::class;
        $result[ResourcesModule::MODULE_NAME]        = ResourcesModule::class;
        $result[ResourcesModule::NPM_RESOURCES]      = ResourcesModule::class;
        $result[ResourcesModule::PUBLIC_RESOURCES]   = ResourcesModule::class;
        $result[ResourcesModule::MODULE_RESOURCES]   = ResourcesModule::class;
        $result[ResourcesModule::VENDOR_RESOURCES]   = ResourcesModule::class;
        $result[ResourcesModule::TEMPLATE_RESOURCES] = ResourcesModule::class;
        return $result;
    }

    /**
     * Объявляет используемые плагины
     *
     * @return array
     */
    protected function declarePlugins(): array {
        return [
            HeaderFavIconPlugin::PLUGIN_NAME => HeaderFavIconPlugin::class,
            ResourceLinkPlugin::PLUGIN_NAME  => ResourceLinkPlugin::class,
            HeaderTitlePlugin::PLUGIN_NAME   => HeaderTitlePlugin::class,
            HeaderMetaPlugin::PLUGIN_NAME    => HeaderMetaPlugin::class
        ];
    }

    /**
     * Объявляет используемые шаблоны разметки страниц
     *
     * @return array
     */
    protected function declareTemplates(): array {
        return [
            Router::PORTAL_TEMPLATE_NAME => PortalTemplate::class
        ];
    }

    /**
     * Определяет набор расширений приложения
     *
     * @return void
     */
    protected function defineExtensions(): void {
        parent::defineExtensions();
        Router::registerPlugins($this->declarePlugins());
        Router::registerTemplates($this->declareTemplates());
    }

    /**
     * Возвращает объект разметки страницы
     *
     * @param \XEAF\UI\Models\Results\PageResult $actionResult Результат исполнения действия
     *
     * @return \XEAF\UI\Core\Template
     */
    protected function createTemplate(PageResult $actionResult): ?Template {
        $result    = null;
        $className = Router::templateClassName($actionResult->templateName);
        if ($className) {
            $result = new $className($actionResult);
        }
        return $result;
    }

    /**
     * Метод обработки результата исполнения действия
     *
     * @param \XEAF\API\Core\ActionResult $actionResult Результат исполнения действия
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     * @throws \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    protected function processResult(\XEAF\API\Core\ActionResult $actionResult): void {
        if ($actionResult instanceof ActionResult || $actionResult instanceof FormResult) {
            if (!Session::isNative()) {
                $this->processWrongMode();
            } else {
                switch ($actionResult->resultType) {
                    case ActionResult::PAGE:
                        $this->adjustLayoutFile($actionResult);
                        $this->processPageResult($actionResult);
                        break;
                    case ActionResult::HTML:
                        $this->adjustLayoutFile($actionResult);
                        $this->processHtmlResult($actionResult);
                        break;
                    case ActionResult::FORM:
                        $this->processFormResult($actionResult);
                        break;
                }
            }
        } else {
            parent::processResult($actionResult);
        }
    }

    /**
     * Уточнает информацию об имени файла шаблона
     *
     * @param \XEAF\UI\Core\ActionResult $actionResult Результат исполнения действия
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    protected function adjustLayoutFile(ActionResult $actionResult): void {
        if ($actionResult instanceof HtmlResult && !$actionResult->layoutFile) {
            $className                = $this->moduleClassName();
            $actionResult->layoutFile = TemplateEngine::defaultLayoutFile($className);
        }
    }

    /**
     * Обрабатывает действие отправки страницы
     *
     * @param \XEAF\UI\Core\ActionResult $actionResult Результат исполнения действия
     *
     * @return void
     * @throws \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    protected function processPageResult(ActionResult $actionResult): void {
        assert($actionResult instanceof PageResult);
        Cookie::setSecurityToken();
        $template    = $this->createTemplate($actionResult);
        $pageContent = TemplateEngine::parseModule($actionResult);
        print TemplateEngine::parseTemplate($template, $pageContent);
    }

    /**
     * Обрабатывает действие отправки HTML фрагмента страницы
     *
     * @param \XEAF\UI\Core\ActionResult $actionResult Результат исполнения действия
     *
     * @return void
     * @throws \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    protected function processHtmlResult(ActionResult $actionResult): void {
        assert($actionResult instanceof HtmlResult);
        Cookie::setSecurityToken();
        print TemplateEngine::parseModule($actionResult);
    }

    /**
     * Обрабатывает действие валидации формы
     *
     * @param \XEAF\UI\Models\Results\FormResult $actionResult Результат исполнения действия
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    protected function processFormResult(FormResult $actionResult): void {
        header("Content-type: application/json; charset=utf-8");
        $data = [
            'status'       => $actionResult->statusCode,
            'alert'        => $actionResult->alertName,
            'title'        => $actionResult->alertTitle,
            'message'      => $actionResult->message,
            'objectErrors' => $actionResult->objectErrors
        ];
        print Serializer::jsonArrayEncode($data);
    }

    /**
     * Обрабатывает отказ отправки результата действия
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    protected function processWrongMode(): void {
        $error = new ErrorResult(HttpStatusCodes::NOT_FOUND);
        http_response_code($error->statusCode);
        $this->processErrorResult($error);
    }
}
