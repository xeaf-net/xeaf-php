<?php

/**
 * autoload.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-DB-PGSQL
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\DB\MySQL;

use XEAF\DB\Utils\Database;
use XEAF\DB\MySQL\Utils\Providers\MySqlProvider;

// -- Регистрация компонентов --
Database::registerProvider(MySqlProvider::PROVIDER_NAME, MySqlProvider::class);

