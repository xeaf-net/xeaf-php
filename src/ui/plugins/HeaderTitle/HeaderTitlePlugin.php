<?php

/**
 * HeaderTitlePlugin.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Plugins\HeaderTitle;

use XEAF\API\Core\DataObject;
use XEAF\UI\Core\Plugin;

/**
 * Контроллер плагина вывода заголовка страницы
 *
 * @package  XEAF\UI\Plugins\HeaderTitle
 */
class HeaderTitlePlugin extends Plugin {

    /**
     * Тия тега плагина
     */
    public const PLUGIN_NAME = 'tagHeaderTitle';

    /**
     * Возвращает объект данных плагина
     *
     * @param array $params Параметры вызова плагина
     *
     * @return \XEAF\API\Core\DataObject|null
     */
    public function getDataObject(array $params = []): ?DataObject {
        return new DataObject([
            'pageTitle' => $this->_template->pageTitle
        ]);
    }
}
