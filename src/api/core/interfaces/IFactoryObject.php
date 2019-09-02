<?php

/**
 * IFactoryObject.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Core\Interfaces;

/**
 * Интерфейс объектов фабрики
 *
 * @package XEAF\API\Core\Interfaces
 */
interface IFactoryObject {

    /**
     * Идентификатор объекта по умолчанию
     */
    const DEFAULT_NAME = 'default';

    /**
     * Конструктор класса
     *
     * @param string $name Идентификатор объекта
     */
    function __construct(string $name);

    /**
     * Возвращает идентификатор объекта
     *
     * @return string
     */
    function getName(): string;
}
