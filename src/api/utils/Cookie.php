<?php

/**
 * Cookie.php
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

/**
 * Реализует методы работы с Cookie
 *
 * @package  XEAF\API\Utils
 */
class Cookie {

    /**
     * Cookie токена безопасности
     */
    public const SECURITY_TOKEN_NAME = 'xeaf-auth-token';

    /**
     * Возвращает ранее сохраненное значение
     *
     * @param string     $key          Ключ
     * @param mixed|null $defaultValue Значение по умолчанию
     *
     * @return mixed
     */
    public static function get(string $key, $defaultValue = null) {
        return $_COOKIE[$key] ?? $defaultValue;
    }

    /**
     * Сохраняет значение
     *
     * @param string $key   Ключ
     * @param null   $value Значение
     * @param int    $ttl   Время жизни в секундах
     *
     * @return void
     */
    public static function put(string $key, $value = null, int $ttl = 0) {
        $expire        = $ttl == 0 ? 0 : time() + $ttl;
        $config        = Factory::getConfiguration();
        $domain        = parse_url($config->portal->url, PHP_URL_HOST);
        $_COOKIE[$key] = $value;
        setcookie($key, $value, $expire, '/', $domain);
    }

    /**
     * Удаляет ранее установленное значение
     *
     * @param string $key Ключ
     *
     * @return void
     */
    public static function delete(string $key): void {
        self::put($key, '', -DateTime::SECONDS_PER_HOUR);
    }

    /**
     * Возвращает признак существования значения
     *
     * @param string $key Ключ
     *
     * @return bool
     */
    public static function exists(string $key): bool {
        return isset($_COOKIE[$key]);
    }

    /**
     * Возвращает массив установленных значений
     *
     * @return array
     */
    protected static function getValues(): array {
        return $_COOKIE;
    }

    /**
     * Проверяет тоекн безопасности
     *
     * @param string|null $token Токен безопасности
     *
     * @return bool
     */
    public static function checkSecurityToken(?string $token): bool {
        $cookie = self::get(self::SECURITY_TOKEN_NAME);
        return $cookie == $token;
    }

    /**
     * Задает токен безопасности
     *
     * @return void
     */
    public static function setSecurityToken(): void {
        $token = Crypto::securityToken();
        self::put(self::SECURITY_TOKEN_NAME, $token);
    }
}
