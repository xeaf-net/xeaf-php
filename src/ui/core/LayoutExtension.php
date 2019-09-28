<?php

/**
 * LayoutExtension.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Core;

use XEAF\API\Core\DataObject;
use XEAF\API\Core\Extension;
use XEAF\UI\App\Router;
use XEAF\UI\Models\Results\HtmlResult;
use XEAF\UI\Utils\TemplateEngine;

/**
 * Реализует методы расширения шаблонизатора
 *
 * @property-read \XEAF\UI\Models\Results\HtmlResult $actionResult Результат исполнения действия
 * @property-read \XEAF\API\Core\DataObject|null     $dataObject   Объект данных
 * @property-read string                             $layoutFile   Файл шаблона построения
 *
 * @package  XEAF\UI\Core
 */
abstract class LayoutExtension extends Extension {

    /**
     * Результат исполнения действия
     * @var \XEAF\UI\Models\Results\HtmlResult
     */
    private $_actionResult = null;

    /**
     * Конструктор класса
     *
     * @param \XEAF\UI\Models\Results\HtmlResult $actionResult Результат исполнения действия
     */
    public function __construct(HtmlResult $actionResult) {
        parent::__construct();
        $this->_actionResult = $actionResult;
        $this->registerPlugins();
    }

    /**
     * Регистрирует используемые плагины
     *
     * @return void
     */
    private function registerPlugins(): void {
        Router::registerPlugins($this->declarePlugins());
    }

    /**
     * Объявляет список используемых плагинов
     *
     * @return array
     */
    protected function declarePlugins(): array {
        return [];
    }

    /**
     * Возвращает результат исполнения действия
     *
     * @return \XEAF\UI\Models\Results\HtmlResult
     */
    public function getActionResult(): HtmlResult {
        return $this->_actionResult;
    }

    /**
     * Возвращает объект данных
     *
     * @return \XEAF\API\Core\DataObject|null
     */
    public function getDataObject(): ?DataObject {
        return null;
    }

    /**
     * Возвращает имя файла шаблона построения
     *
     * @return string
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    public function getLayoutFile(): string {
        return TemplateEngine::defaultLayoutFile($this->className);
    }
}
