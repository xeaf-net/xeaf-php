<?php

/**
 * Factory.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\App;

use XEAF\API\Core\Interfaces\IFactoryObject;

/**
 * Реализует методы фабрики объектов
 *
 * @package XEAF\API\App
 */
class Factory {

    /**
     * Список экземпляров объектов фабрики
     * @var array
     */
    private static $_instances = [];

    /**
     * Удаляет все ссылки на объекты сущностей
     *
     * @return void
     */
    public static function clear(): void {
        self::$_instances = [];
    }

    /**
     * Создает и возвращает объекты фабрики классов
     *
     * @param string $className Имя класса
     * @param string $name      Имя объекта
     *
     * @return \XEAF\API\Core\Interfaces\IFactoryObject
     */
    public static function getFactoryObject(string $className, string $name = IFactoryObject::DEFAULT_NAME): IFactoryObject {
        $id = self::objectId($className, $name);
        if (!self::objectExists($id)) {
            self::$_instances[$id] = new $className($name);
        }
        return self::$_instances[$id];
    }

    /**
     * Сохраняет объект фабрики классов
     *
     * @param string                                   $className     Имя класса
     * @param string                                   $name          Имя объекта
     * @param \XEAF\API\Core\Interfaces\IFactoryObject $factoryObject Объект фабрики классов
     *
     * @return void
     */
    public static function setFactoryObject(string $className, string $name, IFactoryObject $factoryObject): void {
        $id                    = self::objectId($className, $name);
        self::$_instances[$id] = $factoryObject;
    }

    /**
     * Возвращает идентификатор объекта фабрики
     *
     * @param string $className Имя класса
     * @param string $name      Имя объекта
     *
     * @return string
     */
    protected static function objectId(string $className, string $name): string {
        return "$className-$name";
    }

    /**
     * Возвращает признак существования объекта
     *
     * @param string $objectId Идентификатор объекта
     *
     * @return bool
     */
    protected static function objectExists(string $objectId): bool {
        return isset(self::$_instances[$objectId]);
    }
}
