<?php

/**
 * Router.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\App;

/**
 * Реализует методы работы с маршрутами
 *
 * @package  XEAF\API\App
 */
class Router {

    /**
     * Модуль домашней страницы
     */
    public const HOME_MODULE = 'home';

    /**
     * Модуль авторизации
     */
    public const LOGIN_MODULE = 'login';

    /**
     * Модуль профиля пользователя
     */
    public const PROFILE_MODULE = 'profile';

    /**
     * Модуль сообщений пользователя
     */
    public const MESSAGES_MODULE = 'messages';

    /**
     * Модуль задач пользователя
     */
    public const TASKS_MODULE = 'tasks';

    /**
     * Модуль изменения параметров сессии
     */
    public const SESSION_MODULE = 'session';

    /**
     * Список зарегистрированных модулей
     * @var array
     */
    private static $_modules = [];

    /**
     * Удаляет информацию обо всех зарегистрированных модулях
     *
     * @return void
     */
    public static function clearRegisteredModules(): void {
        self::$_modules = [];
    }

    /**
     * Регистрирует новый модуль
     *
     * @param string $name      Имя модуля
     * @param string $className Имя класса модуля
     */
    public static function registerModule(string $name, string $className): void {
        self::$_modules[$name] = $className;
    }

    /**
     * Регистрирует модули по определениям из массива
     *
     * @param array $modules Массив определений модулей
     *
     * @return void
     */
    public static function registerModules(array $modules): void {
        foreach ($modules as $name => $className) {
            self::registerModule($name, $className);
        }
    }

    /**
     * Возвращает имя класса модуля
     *
     * @param string $name Идентификатор модуля
     *
     * @return string|null
     */
    public static function moduleClassName(string $name): ?string {
        return self::$_modules[$name] ?? null;
    }
}
