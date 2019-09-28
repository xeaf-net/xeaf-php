<?php

/**
 * RedisException.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-REDIS
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\Redis\Utils\Exceptions;

use Throwable;
use XEAF\API\Core\Exception;

/**
 * Исключения при работе с сервером Redis
 *
 * @package  XEAF\Redis\Utils\Exceptions
 */
class RedisException extends Exception {

    /**
     * Ошибка подключения к серверу Redis
     *
     * @param string     $name   Имя сервера
     * @param \Throwable $reason Причина возникновения ошибки
     *
     * @return \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public static function connectionError(string $name, Throwable $reason): self {
        return new self('Could not connect to Redis server [%s].', [$name], $reason);
    }

    /**
     * Ошибка выбора базы данных
     *
     * @param string     $name    Имя сервера
     * @param int        $dbindex Индекс базы данных
     * @param \Throwable $reason  Причина возникновения ошибки
     *
     * @return \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public static function selectDatabaseError(string $name, int $dbindex, Throwable $reason): self {
        return new self('Could not select Redis database [$%s:%d].', [$name, $dbindex], $reason);
    }

    /**
     * Ошибка получения данных с сервера
     *
     * @param string     $name   Имя базы данных
     * @param \Throwable $reason Причина возникновения ошибки
     *
     * @return \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public static function dataReadingError(string $name, Throwable $reason): self {
        return new self('Error while getting data from Redis server [%s].', [$name], $reason);
    }

    /**
     * Ошибка созранения данных на сервере
     *
     * @param string     $name   Имя сервера
     * @param \Throwable $reason Причина возникновения ошибки
     *
     * @return \XEAF\Redis\Utils\Exceptions\RedisException
     */
    public static function dataSavingError(string $name, Throwable $reason): self {
        return new self('Error while putting data to Redis server [%s].', [$name], $reason);
    }
}
