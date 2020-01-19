<?php

/**
 * Module.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Core;

use XEAF\UI\App\Router;

class Module extends \XEAF\API\Core\Module {

    /**
     * Конструктор класса
     */
    public function __construct() {
        parent::__construct();
        if (!$this->apiMode()) {
            $this->registerPlugins();
        }
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
}
