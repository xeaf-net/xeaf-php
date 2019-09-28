<?php

/**
 * Router.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\App;

/**
 * Реализует методы работы с маршрутами
 *
 * @package  XEAF\UI\App
 */
class Router extends \XEAF\API\App\Router {

    /**
     * Идентификатор шалона по умолчанию
     */
    public const PORTAL_TEMPLATE_NAME = 'portal';

    /**
     * Список зарегистрированных плагинов
     * @var array
     */
    private static $_plugins = [];

    /**
     * Список зарегистрированных шаблонов
     * @var array
     */
    private static $_templates = [];

    /**
     * Удаляет информацию обо всех зарегистрированных плагинах
     *
     * @return void
     */
    public static function clearRegisteredPlugins(): void {
        self::$_plugins = [];
    }

    /**
     * Регистрирует новый плагин
     *
     * @param string $name      Имя плагина
     * @param string $className Имя класса плагина
     */
    public static function registerPlugin(string $name, string $className): void {
        self::$_plugins[$name] = $className;
    }

    /**
     * Регистрирует плагины по определениям из массива
     *
     * @param array $plugins Массив определений плагинов
     *
     * @return void
     */
    public static function registerPlugins(array $plugins): void {
        foreach ($plugins as $name => $className) {
            self::registerPlugin($name, $className);
        }
    }

    /**
     * Возвращает имя класса плагина
     *
     * @param string $name Идентификатор плагина
     *
     * @return string|null
     */
    public static function pluginClassName(string $name): ?string {
        return self::$_plugins[$name] ?? null;
    }

    /**
     * Удаляет информацию обо всех зарегистрированных шаблонах
     *
     * @return void
     */
    public static function clearRegisteredTemplates(): void {
        self::$_templates = [];
    }

    /**
     * Регистрирует новый шаблон
     *
     * @param string $name      Имя шаблона
     * @param string $className Имя класса шаблона
     */
    public static function registerTemplate(string $name, string $className): void {
        self::$_templates[$name] = $className;
    }

    /**
     * Регистрирует шаблоны по определениям из массива
     *
     * @param array $templates Массив определений шаблонов
     *
     * @return void
     */
    public static function registerTemplates(array $templates): void {
        foreach ($templates as $name => $className) {
            self::registerTemplate($name, $className);
        }
    }

    /**
     * Возвращает имя класса шаблона
     *
     * @param string $name Идентификатор шаблона
     *
     * @return string|null
     */
    public static function templateClassName(string $name): ?string {
        return self::$_templates[$name] ?? null;
    }
}
