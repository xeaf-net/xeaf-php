<?php

/**
 * HomeModule.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Modules;

use XEAF\API\Core\ActionResult;
use XEAF\API\Core\Module;
use XEAF\API\Models\Results\DataResult;

/**
 * Контроллер модуля домашней страницы
 *
 * @package  XEAF\API\Modules
 */
class HomeModule extends Module {

    /**
     * Метод обработки действия для метода GET по умолчанию
     *
     * @return \XEAF\API\Core\ActionResult|null
     */
    protected function processGetDefault(): ?ActionResult {
        return DataResult::fromArray([
            'title'   => 'XEAF-PHP-API',
            'version' => __XEAF_PHP_VERSION__
        ]);
    }
}
