<?php

/**
 * Factory.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\App;

use XEAF\API\Core\ActionArgs;
use XEAF\API\Core\FactoryObject;
use XEAF\API\Utils\Parameters;
use XEAF\API\Utils\Storage\FileStorage;
use XEAF\API\Utils\Storage\StaticStorage;

/**
 * Фабрика объектов
 *
 * @package  XEAF\API\App
 */
class Factory {

    /**
     * Хранилище экземпляров объектов
     * @var array
     */
    private static $_instances = [];

    /**
     * Возвращает объект параметров конфигурации
     *
     * @return \XEAF\API\App\Configuration
     */
    public static function getConfiguration(): Configuration {
        $result = self::getFactoryObject(Configuration::class);
        assert($result instanceof Configuration);
        return $result;
    }

    /**
     * Задает объект параметров конфигурации
     *
     * @param \XEAF\API\App\Configuration $configuration Объект параметров конфигурации
     *
     * @return void
     */
    public static function setConfiguration(Configuration $configuration): void {
        self::setFactoryObject(Configuration::class, FactoryObject::DEFAULT_NAME, $configuration);
    }

    /**
     * Возвращает объект параметров запуска приложения
     *
     * @return \XEAF\API\Core\ActionArgs
     */
    public static function getParameters(): ActionArgs {
        $result = self::getFactoryObject(Parameters::class);
        assert($result instanceof ActionArgs);
        return $result;
    }

    /**
     * Задает объект параметров запуска приложения
     *
     * @param \XEAF\API\Core\ActionArgs $parameters Объект параметров
     *
     * @return void
     */
    public static function setParameters(ActionArgs $parameters): void {
        self::setFactoryObject(Parameters::class, FactoryObject::DEFAULT_NAME, $parameters);
    }

    /**
     * Возвращает объект статического хранилища
     *
     * @param string $name Имя объекта
     *
     * @return \XEAF\API\Utils\Storage\StaticStorage
     */
    public static function getStaticStorage(string $name): StaticStorage {
        $result = self::getFactoryObject(StaticStorage::class, $name);
        assert($result instanceof StaticStorage);
        return $result;
    }

    /**
     * Возвращает объект файлового кеширования
     *
     * @param string $name Имя объекта
     *
     * @return \XEAF\API\Utils\Storage\FileStorage
     */
    public static function getFileStorage(string $name = FactoryObject::DEFAULT_NAME): FileStorage {
        $result = self::getFactoryObject(FileStorage::class, $name);
        assert($result instanceof FileStorage);
        return $result;
    }

    /**
     * Возвращает именованный экземпляр объекта класса
     *
     * @param string $className Имя класса
     * @param string $name      Имя объекта
     *
     * @return object
     */
    public static function getFactoryObject(string $className, string $name = FactoryObject::DEFAULT_NAME): object {
        $id = self::getObjectId($className, $name);
        if (!isset(self::$_instances[$id])) {
            self::$_instances[$id] = new $className($name);
        }
        return self::$_instances[$id];
    }

    /**
     * Задает именованый экземпляр объекта класса
     *
     * @param string $className     Имя класса
     * @param string $name          Имя объекта
     * @param object $factoryObject Экземпляр объекта
     *
     * @return void
     */
    public static function setFactoryObject(string $className, string $name, object $factoryObject): void {
        $id                    = self::getObjectId($className, $name);
        self::$_instances[$id] = $factoryObject;
    }

    /**
     * Возвращает признак существования объекта
     *
     * @param string $className Имя класса
     * @param string $name      Имя объекта
     *
     * @return bool
     */
    public static function factoryObjectExists(string $className, string $name = FactoryObject::DEFAULT_NAME): bool {
        $id = self::getObjectId($className, $name);
        return isset(self::$_instances[$id]);
    }

    /**
     * Возвращает идентификатор хранения объекта
     *
     * @param string $className Имя класса
     * @param string $name      Имя объекта
     *
     * @return string
     */
    protected static function getObjectId(string $className, string $name): string {
        return "$className-$name";
    }
}
