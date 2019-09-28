<?php

/**
 * index.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\WWW;

use XEAF\API\App\Application;

define('__XEAF_CONFIG_DIR__', __DIR__ . '/../etc');
define('__XEAF_VENDOR_DIR__', __DIR__ . '/../app/vendor');
define('__XEAF_DEBUG_MODE__', true);

/** @noinspection PhpIncludeInspection */
require_once __XEAF_VENDOR_DIR__ . '/autoload.php';

($app = new Application())->run();
