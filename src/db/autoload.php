<?php

/**
 * autoload.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-DB
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\DB;

use XEAF\API\Utils\Session;
use XEAF\DB\Utils\Sessions\DatabaseSession;

// -- Регистрация компонентов --
Session::registerProvider(DatabaseSession::PROVIDER_NAME, DatabaseSession::class);

