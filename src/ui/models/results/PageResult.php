<?php

/**
 * PageResult.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Models\Results;

use XEAF\API\Core\DataObject;
use XEAF\UI\App\Router;
use XEAF\UI\Core\ActionResult;

/**
 * Содержит данные для отправки страницы
 *
 * @property string      $pageTitle    Заголовок страницы
 * @property array       $pageMeta     Метаданные страницы
 * @property string|null $templateName Идентификатор шаблона разметки страницы
 *
 * @package  XEAF\UI\Models\Results
 */
class PageResult extends HtmlResult {

    /**
     * Идентификатор мета тега ключевых слов страницы
     */
    public const META_KEYWORDS = 'keywords';

    /**
     * Идентификатор мета тега описания страницы
     */
    public const META_DESCRIPTION = 'description';

    /**
     * Заголовок страницы
     * @var string
     */
    private $_pageTitle = '';

    /**
     * Метаданные страницы
     * @var array
     */
    private $_pageMeta = [];

    /**
     * Идентификатор шаблона разметки страницы
     * @var string|null
     */
    private $_templateName = null;

    /**
     * Конструктор класса
     *
     * @param \XEAF\API\Core\DataObject $dataObject   Объект данных
     * @param string                    $pageTitle    Заголовок страницы
     * @param array                     $pageMeta     Метаданные страницы
     * @param string|null               $templateName Идентификатор шаблона
     * @param string|null               $layoutTpl    Файл шаблона построения
     */
    public function __construct(DataObject $dataObject = null, string $pageTitle = '', array $pageMeta = [], string $templateName = null, string $layoutTpl = null) {
        parent::__construct($dataObject, $layoutTpl);
        $this->resultType    = ActionResult::PAGE;
        $this->_pageTitle    = $pageTitle;
        $this->_pageMeta     = $pageMeta;
        $this->_templateName = $templateName;
    }

    /**
     * Возвращает заголовок страницы
     *
     * @return string
     */
    public function getPageTitle(): string {
        return $this->_pageTitle;
    }

    /**
     * Задает заголовок страницы
     *
     * @param string $value Заголовок страницы
     *
     * @return void
     */
    public function setPageTitle(string $value): void {
        $this->_pageTitle = $value;
    }

    /**
     * Возвращает метаданные страницы
     *
     * @return array
     */
    public function getPageMeta(): array {
        return $this->_pageMeta;
    }

    /**
     * Задает метаданные страницы
     *
     * @param array $pageMeta Метаданные страницы
     *
     * @return void
     */
    public function setPageMeta(array $pageMeta): void {
        $this->_pageMeta = $pageMeta;
    }

    /**
     * Возвращает шаблон разметки страницы
     *
     * @return string
     */
    public function getTemplateName(): ?string {
        if (!$this->_templateName) {
            $this->_templateName = Router::PORTAL_TEMPLATE_NAME;
        }
        return $this->_templateName;
    }

    /**
     * Задает шаблон разметки страницы
     *
     * @param string $templateName Шаблон разметки страницы
     *
     * @return void
     */
    public function setTemplateName(string $templateName): void {
        $this->_templateName = $templateName;
    }
}
