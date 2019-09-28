<?php

/**
 * Template.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Core;

use XEAF\API\Utils\Language;
use XEAF\API\Utils\Locales;
use XEAF\UI\Models\Results\PageResult;

/**
 * Реализует методы шаблона резметки страницы
 *
 * @property-read \XEAF\UI\Models\Results\PageResult $actionResult Результат исполнения действия
 * @property-read string                             $pageTitle    Заголовок страницы
 * @property-read array                              $pageMeta     Метаданные страницы
 *
 * @package  XEAF\UI\Core
 */
class Template extends LayoutExtension {

    /**
     * Идентификатор мета URL портала
     */
    public const PORTAL_URL_META = 'x-portal-url';

    /**
     * Идентификатор мета локали портала
     */
    public const PORTAL_LOCALE_META = 'x-portal-locale';

    /**
     * Идентификатор мета языка портала
     */
    public const PORTAL_LANGUAGE_META = 'x-portal-language';

    /**
     * Заголок страницы проекта XEAF-PHP
     */
    protected const XEAF_PAGE_TITLE = 'XEAF-PHP';

    /**
     * Конструктор класса
     *
     * @param \XEAF\UI\Models\Results\PageResult $actionResult Результат исполнения действия
     */
    public function __construct(PageResult $actionResult) {
        parent::__construct($actionResult);
    }

    /**
     * Возвращает заголовок страницы
     *
     * @return string
     */
    public function getPageTitle(): string {
        $result = $this->actionResult->pageTitle;
        return !$result ? $this->getDefaultPageTitle() : $this->getDefaultPageTitle() . ' | ' . $result;
    }

    /**
     * Возвращает метаданные страницы
     *
     * @return array
     */
    public function getPageMeta(): array {
        $result     = $this->getDefaultPageMeta();
        $actionMeta = $this->actionResult->pageMeta;
        foreach ($actionMeta as $name => $value) {
            $result[$name] = $value;
        }
        return $result;
    }

    /**
     * Возвращает заголовок страницы по умолчанию
     *
     * @return string
     */
    public function getDefaultPageTitle(): string {
        return self::XEAF_PAGE_TITLE;
    }

    /**
     * Возвращает метаданные страницы по умолчанию
     *
     * @return array
     */
    public function getDefaultPageMeta(): array {
        $locale = Language::getLanguage();
        return [
            self::PORTAL_URL_META      => $this->cfg->portal->url,
            self::PORTAL_LOCALE_META   => Locales::localeToMeta($locale),
            self::PORTAL_LANGUAGE_META => Locales::localeToLanguage($locale)
        ];
    }
}
