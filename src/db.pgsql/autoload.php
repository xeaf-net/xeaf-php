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
namespace XEAF\DB\PgSQL;

use XEAF\DB\Utils\Database;
use XEAF\DB\PgSQL\Utils\Providers\PgSqlProvider;

// -- Регистрация компонентов --
Database::registerProvider(PgSqlProvider::PROVIDER_NAME, PgSqlProvider::class);

