<?php

/**
 * autoload.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API;

use XEAF\API\Utils\Session;
use XEAF\API\Utils\Sessions\FileSession;
use XEAF\API\Utils\Sessions\NativeSession;
use XEAF\API\Utils\Sessions\StaticSession;

// -- Регистрация компонентов --
Session::registerProvider(FileSession::PROVIDER_NAME, FileSession::class);
Session::registerProvider(StaticSession::PROVIDER_NAME, StaticSession::class);
Session::registerProvider(NativeSession::PROVIDER_NAME, NativeSession::class);
