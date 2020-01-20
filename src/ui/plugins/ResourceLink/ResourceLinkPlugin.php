<?php

/**
 * ResourceLinkPlugin.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Plugins\ResourceLink;

use XEAF\API\Core\DataObject;
use XEAF\API\Utils\FileSystem;
use XEAF\API\Utils\Parameters;
use XEAF\API\Utils\Reflection;
use XEAF\API\Utils\Strings;
use XEAF\UI\App\Router;
use XEAF\UI\Core\Plugin;

/**
 * Контроллер плагина вывода ссылок на ресурсы модуля
 *
 * @package  XEAF\UI\Plugins\ResourceLink
 */
class ResourceLinkPlugin extends Plugin {

    /**
     * Имя тега плагина
     */
    public const PLUGIN_NAME = 'tagResourceLink';

    /**
     * Список загружаемых ссылок
     * @var array
     */
    protected $_data = [];

    /**
     * Возвращает объект данных плагина
     *
     * @param array $params Параметры вызова плагина
     *
     * @return \XEAF\API\Core\DataObject|null
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    public function getDataObject(array $params = []): ?DataObject {
        $type = $params['type'] ?? null;
        if ($type == 'css' || $type == 'js') {
            $actionName = $this->prm->actionName;
            $className  = Router::moduleClassName($actionName);
            if ($className) {
                $this->checkActionNameLink($className, $type);
                $this->checkActionModeLink($type);
            }
        }
        return new DataObject(['type' => $type, 'data' => $this->_data]);
    }

    /**
     * Добавляет ссылку на ресурс модуля
     *
     * @param string $className Идентификатор класса
     * @param string $type      Тип ссылки
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    protected function checkActionNameLink(string $className, string $type) {
        $actionName = $this->prm->actionName;
        $fileName   = FileSystem::changeFileNameExt(Reflection::classFileName($className), $type);
        if (FileSystem::fileExists($fileName)) {
            $this->_data[] = $this->cfg->portal->url . '/module/' . $actionName . '.' . $type;
        }
    }

    /**
     * Добавляет ссылку на ресурс режима исполнения действия модуля
     *
     * @param string $type Тип ссылки
     *
     * @return void
     */
    protected function checkActionModeLink(string $type) {
        $layoutFile = $this->actionResult->layoutFile;
        if ($layoutFile) {
            $actionName = $this->prm->actionName;
            $actionMode = $this->prm->actionMode;
            if (Strings::isEmpty($actionMode)) {
                $actionMode = Parameters::DEFAULT_ACTION_MODE;
            }
            $fileName = FileSystem::removeFileNameExt($layoutFile) . '-' . ucfirst($actionMode) . '.' . $type;
            if (FileSystem::fileExists($fileName)) {
                $this->_data[] = $this->cfg->portal->url . '/module/' . $actionName . '.' . $actionMode . '.' . $type;
            }
        }
    }
}
