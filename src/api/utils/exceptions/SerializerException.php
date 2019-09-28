<?php

/**
 * SerializerException.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils\Exceptions;

use Throwable;
use XEAF\API\Core\Exception;

/**
 * Исключения сериализации и восстановления объектов
 *
 * @package  XEAF\API\Utils\Exceptions
 */
class SerializerException extends Exception {
    /**
     * Некорректный формат данных JSON
     *
     * @param \Throwable $reason Причина возникновения ошибки
     *
     * @return \XEAF\API\Utils\Exceptions\SerializerException
     */
    public static function invalidJsonFormat(Throwable $reason): self {
        return new self('Invalid JSON format.', [], $reason);
    }

    /**
     * Ошибка сериализации данных
     *
     * @param \Throwable $reason Причина возникновения ошибки
     *
     * @return \XEAF\API\Utils\Exceptions\SerializerException
     */
    public static function serializationError(Throwable $reason): self {
        return new self('Data serialization error.', [], $reason);
    }

    /**
     * Ошибка восстановления данных
     *
     * @param \Throwable $reason Причина возникновения ошибки
     *
     * @return \XEAF\API\Utils\Exceptions\SerializerException
     */
    public static function unserializationError(Throwable $reason): self {
        return new self('Data unserialization error.', [], $reason);
    }

    /**
     * Ошибка проверки хеша данных
     *
     * @return \XEAF\API\Utils\Exceptions\SerializerException
     */
    public static function dataHashValidationError(): self {
        return new self('Data hash validation error.');
    }
}
