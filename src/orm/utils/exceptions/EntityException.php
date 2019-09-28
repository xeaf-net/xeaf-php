<?php

/**
 * EntityException.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-ORM
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\ORM\Utils\Exceptions;

use Throwable;
use XEAF\API\Core\Exception;

/**
 * Исключение ORM
 *
 * @package  XEAF\ORM\Utils\Exceptions
 */
class EntityException extends Exception {

    /**
     * Синтаксическая ошибка
     *
     * @param int $position Позиция в строке
     *
     * @return \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function syntaxError(int $position): self {
        return new self('Syntax error at position [%d].', [$position]);
    }

    /**
     * Незакрытая одинарная кавычка
     *
     * @param int $position Позиция в строке
     *
     * @return \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function unclosedSingleQuote(int $position): self {
        return new self('Unclosed single quote at position [%d].', [$position]);
    }

    /**
     * Непарная закрывающая скобка
     *
     * @param int $position Позиция в строке
     *
     * @return \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function unpairedClosingBracket(int $position): self {
        return new self('Unpaired closing bracket at position [%d].', [$position]);
    }

    /**
     * Недопустимая лекема
     *
     * @param string $token    Лексема
     * @param int    $position Позиция в строке
     *
     * @return \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function invalidToken(string $token, int $position): self {
        return new self('Invalid token [%s] at position [%d].', [$token, $position]);
    }

    /**
     * Неизвестная сущность
     *
     * @param string $name     Имя сущности
     * @param int    $position Позиция в строке
     *
     * @return \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function unknownEntity(string $name, int $position = 0): self {
        if ($position > 0) {
            return new self('Unknown entity [%s] at position [%d].', [$name, $position]);
        } else {
            return new self('Unknown entity [%s].', [$name]);
        }
    }

    /**
     * Неизвестная сущность
     *
     * @param string $className Имя класса
     *
     * @return \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function unknownEntityClass(string $className): self {
        return new self('Unknown entity with class [%s].', [$className]);
    }

    /**
     * Неизвестное свойство сущности
     *
     * @param string $name     Имя сущности или псевдоним
     * @param string $property Свойство
     * @param int    $position Позиция в строке
     *
     * @return \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function unknownEntityProperty(string $name, string $property, int $position): self {
        return new self('Unknown entity property [%s.%s] at position [%d].', [$name, $property, $position]);
    }

    /**
     * Неизвестный псевдоним сущности
     *
     * @param string $alias    Псевдоним сущности
     * @param int    $position Позиция в строке
     *
     * @return \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function unknownEntityAlias(string $alias, int $position = 0): self {
        if ($position > 0) {
            return new self('Unknown entity alias [%s] as position [%d].', [$alias, $position]);
        } else {
            return new self('Unknown entity alias [%s].', [$alias]);
        }
    }

    /**
     * Пропущена конструкция FROM
     *
     * @return \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function missedFromClause(): self {
        return new self('Missed from clause.');
    }

    /**
     * Не задано ни одной сущности
     *
     * @return \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function noEntitySpecified(): self {
        return new self('No entity is specified.');
    }

    /**
     * Нельзя отслеживать объект с пустым первичным ключом
     *
     * @return \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function watchNullObject(): self {
        return new self('Could not watch an object with null primary key.');
    }

    /**
     * Внутренняя ошибка ORM
     *
     * @param \Throwable $reason Причина возникновения ошибки
     *
     * @return \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function internalError(Throwable $reason): self {
        return new self('Internal ORM error.', [], $reason);
    }
}
