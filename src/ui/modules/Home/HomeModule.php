<?php

/**
 * HomeModule.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Modules\Home;

use XEAF\API\Core\ActionResult;
use XEAF\UI\Models\Results\PageResult;

/**
 * Контроллер модуля домашней страницы
 *
 * @package  XEAF\UI\Modules
 */
class HomeModule extends \XEAF\API\Modules\HomeModule {

    /**
     * Метод обработки действия для метода GET по умолчанию
     *
     * @return \XEAF\API\Core\ActionResult|null
     */
    protected function processGetDefault(): ?ActionResult {
        return $this->apiMode() ? $this->processGetDefaultAPI() : new PageResult();
    }

    /**
     * Метод обработки действия для метода GET по умолчанию
     *
     * @return \XEAF\API\Core\ActionResult|null
     */
    protected function processGetDefaultAPI(): ?ActionResult {
        return parent::processGetDefault();
    }
}
