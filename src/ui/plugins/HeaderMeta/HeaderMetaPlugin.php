<?php

/**
 * HeaderMetaPlugin.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Plugins\HeaderMeta;

use XEAF\API\Core\DataObject;
use XEAF\UI\Core\Plugin;

/**
 * Контроллер плагина вывода метаданных страницы
 *
 * @package  XEAF\UI\Plugins\HeaderMeta
 */
class HeaderMetaPlugin extends Plugin {

    /**
     * Имя тега плагина
     */
    public const PLUGIN_NAME = 'tagHeaderMeta';

    /**
     * Возвращает объект данных плагина
     *
     * @param array $params Параметры вызова плагина
     *
     * @return \XEAF\API\Core\DataObject|null
     */
    public function getDataObject(array $params = []): ?DataObject {
        return new DataObject([
            'pageMeta' => $this->_template->pageMeta
        ]);
    }
}
