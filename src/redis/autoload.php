<?php

/**
 * autoload.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-REDIS
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\Redis;

use XEAF\API\Utils\Session;
use XEAF\Redis\Utils\Sessions\RedisSession;

// -- Регистрация компонентов --
Session::registerProvider(RedisSession::PROVIDER_NAME, RedisSession::class);
