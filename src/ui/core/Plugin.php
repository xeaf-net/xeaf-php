<?php

/**
 * Plugin.php
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
use XEAF\UI\Models\Results\HtmlResult;

/**
 * Реализует базовые методы плагинов
 *
 * @package  XEAF\UI\Core
 */
class Plugin extends LayoutExtension {

    /**
     * Объект шаблона разметки страницы
     * @var \XEAF\UI\Core\Template
     */
    protected $_template = null;

    /**
     * Конструктор класса
     *
     * @param \XEAF\UI\Models\Results\HtmlResult $actionResult Результат исполнения действия
     * @param \XEAF\UI\Core\Template|null        $template     Объект шаблона разметки страницы
     */
    public function __construct(HtmlResult $actionResult, Template $template = null) {
        parent::__construct($actionResult);
        $this->_template = $template;
    }

    /**
     * Возвращает объект данных
     *
     * @param array $params Параметры вызова плагина
     *
     * @return \XEAF\API\Core\DataObject|null
     */
    public function getDataObject(/** @noinspection PhpUnusedParameterInspection */ array $params = []): ?DataObject {
        return null;
    }
}
