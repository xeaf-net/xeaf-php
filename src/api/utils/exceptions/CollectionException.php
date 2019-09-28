<?php

/**
 * CollectionException.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils\Exceptions;

use XEAF\API\Core\Exception;

/**
 * Исключения при работе с коллекциями
 *
 * @package  XEAF\API\Utils\Exceptions
 */
class CollectionException extends Exception {

    /**
     * Нет элементов в очереди
     *
     * @return \XEAF\API\Utils\Exceptions\CollectionException
     */
    public static function queueIsEmpty(): self {
        return new self('There are no items in the queue.');
    }

    /**
     * Нет элементов в стеке
     *
     * @return \XEAF\API\Utils\Exceptions\CollectionException
     */
    public static function queueIsStack(): self {
        return new self('There are no items in the stack.');
    }
}
