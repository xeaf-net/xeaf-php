<?php

/**
 * DatabaseException.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-DB
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\DB\Utils\Exceptions;

use Throwable;
use XEAF\API\Core\Exception;

/**
 * Исключения при работе с базами данных
 *
 * @package  XEAF\DB\Utils\Exceptions
 */
class DatabaseException extends Exception {

    /**
     * Ошибка конфигурации базы данных
     *
     * @param string     $name   Идентификатор базы данных
     * @param \Throwable $reason Причина ошибки
     *
     * @return \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public static function configurationError(string $name, Throwable $reason): self {
        return new self('Database [%s] configuration error.', [$name], $reason);
    }

    /**
     * Ошибка подключения к базе данных
     *
     * @param string     $name   Идентификатор базы данных
     * @param \Throwable $reason Причина ошибки
     *
     * @return \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public static function connectionError(string $name, Throwable $reason): self {
        return new self('Database [%s] connection error.', [$name], $reason);
    }

    /**
     * Ошибка работы с транзакциями
     *
     * @param string     $name   Идентификатор базы данных
     * @param \Throwable $reason Причина ошибки
     *
     * @return \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public static function transactionError(string $name, Throwable $reason): self {
        return new self('Database [%s] transaction error.', [$name], $reason);
    }

    /**
     * Ошибка исполнения SQL команды
     *
     * @param string     $name   Идентификатор базы данных
     * @param \Throwable $reason Причина ошибки
     *
     * @return \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public static function sqlCommandError(string $name, Throwable $reason): self {
        return new self('SQL command error on database [%s].', [$name], $reason);
    }

    /**
     * Ошибка исполнения SQL запроса
     *
     * @param string     $name   Идентификатор базы данных
     * @param \Throwable $reason Причина ошибки
     *
     * @return \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public static function sqlQueryError(string $name, Throwable $reason): self {
        return new self('SQL query error on database [%s].', [$name], $reason);
    }

    /**
     * Неизвестный провайдер подкючения к базе данных
     *
     * @param string $name Идентификатор базы данных
     *
     * @return \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public static function unknownDatabaseProvider(string $name): self {
        return new self('Unknown provider for database [%s].', [$name]);
    }

    /**
     * Нет открытого соединения
     *
     * @param string $name Идентификатор базы данных
     *
     * @return \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public static function noOpenConnection(string $name): self {
        return new self('There is no open connection for database [%s].', [$name]);
    }

    /**
     * Нет активной транзакции
     *
     * @param string $name Идентификатор базы данных
     *
     * @return \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public static function noActiveTransaction(string $name): self {
        return new self('There is no active transaction for database [%s].', [$name]);
    }

    /**
     * Соединение сбазой данных уже установлено
     *
     * @param string $name Идентификатор базы данных
     *
     * @return \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public static function connectionAlreadyOpened(string $name): self {
        return new self('Connection already opened for database [%s].', [$name]);
    }

    /**
     * Транзакация уже открыта
     *
     * @param string $name Идентификатор базы данных
     *
     * @return \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public static function transactionAlreadyStarted(string $name): self {
        return new self('Transaction already started for database [%s].', [$name]);
    }
}
