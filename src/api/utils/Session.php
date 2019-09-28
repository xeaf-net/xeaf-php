<?php

/**
 * Session.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

use XEAF\API\App\Factory;
use XEAF\API\Core\SessionProvider;
use XEAF\API\Utils\Exceptions\SessionException;
use XEAF\API\Utils\Sessions\NativeSession;

/**
 * Реализует методы работы с сессиями
 *
 * @property      string|null $sessionId    Идентификатор сессии
 * @property      string|null $userId       Идентификатор пользователя
 * @property-read bool        $userLoggedIn Признак авторизованного пользователя
 *
 * @package  XEAF\API\Utils
 */
class Session {

    /**
     * Идентификатор переменной пользователя
     */
    protected const USER_ID = 'X-User';

    /**
     * Идентификатор переменной языка сессии
     */
    protected const LANGUAGE_ID = 'X-Language';

    /**
     * Список зарегистрированных провайдеров
     * @var array
     */
    protected static $_providers = [];

    /**
     * Хранилище перемнных сессии
     * @var \XEAF\API\Core\SessionProvider
     */
    protected static $_provider = null;

    /**
     * Возвращает значение переменной сессии
     *
     * @param string $name         Переменная
     * @param null   $defaultValue Значение по умолчанию
     *
     * @return mixed
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function get(string $name, $defaultValue = null) {
        if (self::$_provider == null) {
            throw SessionException::sessionNotOpened();
        }
        return self::$_provider->get($name, $defaultValue);
    }

    /**
     * Задает заначение переменной сессии
     *
     * @param string $name  Переменная
     * @param mixed  $value Значение
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function put(string $name, $value): void {
        if (self::$_provider == null) {
            throw SessionException::sessionNotOpened();
        }
        self::$_provider->put($name, $value);
    }

    /**
     * Возвращает идентификатор сессии
     *
     * @return string
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function getSessionId(): string {
        if (self::$_provider == null) {
            throw SessionException::sessionNotOpened();
        }
        return self::$_provider->getSessionId();
    }

    /**
     * Возвращает идентификатор пользователя
     *
     * @return string
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function getUserId(): string {
        return self::get(self::USER_ID, '');
    }

    /**
     * Задает идентификатор пользователя
     *
     * @param string $userId Идентификатор пользователя
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function setUserId(string $userId): void {
        self::put(self::USER_ID, $userId);
    }

    /**
     * Возвращает язык сессии
     *
     * @return string
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function getLanguage(): string {
        $result = self::get(self::LANGUAGE_ID);
        if (!$result) {
            $config = Factory::getConfiguration()->portal;
            $result = $config->language;
            self::setLanguage($result);
        }
        return $result;
    }

    /**
     * Задает язык сессии
     *
     * @param string $language Язык
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function setLanguage(string $language): void {
        self::put(self::LANGUAGE_ID, $language);
    }

    /**
     * Возвращает признак авторизованной сессии
     *
     * @return bool
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function authorized(): bool {
        return !Strings::isEmpty(self::getUserId());
    }

    /**
     * Возвращает признак нативной сессии
     *
     * @return bool
     */
    public static function isNative(): bool {
        return self::$_provider instanceof NativeSession;
    }

    /**
     * Открывает сессию
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function openSession(): void {
        if (self::$_provider == null) {
            self::$_provider = self::createProvider();
            self::$_provider->loadSessionData();
        }
    }

    /**
     * Закрывает сессию
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function closeSession(): void {
        if (self::$_provider == null) {
            throw SessionException::sessionNotOpened();
        }
        self::$_provider->saveSessionData();
    }

    /**
     * Очищает данные сессии
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public static function clearSession(): void {
        if (self::$_provider == null) {
            throw SessionException::sessionNotOpened();
        }
        self::$_provider->deleteSessionData();
    }

    /**
     * Регистрирует класс провайдера сессии
     *
     * @param string $name      Имя провайдера
     * @param string $className Имя класса
     *
     * @return void
     */
    public static function registerProvider(string $name, string $className): void {
        self::$_providers[$name] = $className;
    }

    /**
     * Отменяет регистрацию класса провайдера
     *
     * @param string $name Имя провайдера
     *
     * @return void
     */
    public static function unregisterProvider(string $name): void {
        unset(self::$_providers[$name]);
    }

    /**
     * Создает провайдер сессии
     *
     * @return \XEAF\API\Core\SessionProvider
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    protected static function createProvider(): SessionProvider {
        $result = null;
        list($type, $name) = self::parseProviderConfig();
        $className = self::$_providers[$type] ?? null;
        if ($className) {
            $result = new $className($name);
            assert($result instanceof SessionProvider);
        } else {
            throw SessionException::unknownSessionProvider($type);
        }
        return $result;
    }

    /**
     * Разбирает параметр конфигурации провайдера
     *
     * @return array
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    protected static function parseProviderConfig(): array {
        $config = Factory::getConfiguration()->portal->session;
        if (!$config) {
            throw SessionException::invalidSessionConfiguration();
        }
        return Strings::parseProviderConfig($config);
    }
}
