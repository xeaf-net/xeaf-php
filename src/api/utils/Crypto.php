<?php

/**
 * Crypto.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

/**
 * Реализует методы шифрования данных и работы со случайными значениями
 *
 * @package  XEAF\API\Utils
 */
class Crypto {

    /**
     * Идентификатор алгоритма хеширования
     */
    public const HASH_ALGO = 'sha256';

    /**
     * Идентификатор алгоритма хеширования паролей
     */
    public const PASSWORD_ALGO = PASSWORD_DEFAULT;

    /**
     * Представление нулевого UUID
     */
    public const ZERO_UUID = '00000000-0000-0000-0000-000000000000';

    /**
     * Генерирует хеш на основе пароля и строковых данных
     *
     * @param string $data     Данные для хеша
     * @param string $password Пароль
     *
     * @return string
     */
    public static function hash(string $data, $password = ''): string {
        return hash_hmac(self::HASH_ALGO, $data, $password);
    }

    /**
     * Метод сравнение хешей
     *
     * @param string $hash1 Известный хеш
     * @param string $hash2 Сравниваемый хеш
     *
     * @return bool
     */
    public static function hashEquals(string $hash1, string $hash2): bool {
        return hash_equals($hash1, $hash2);
    }

    /**
     * Возвращает случайно сгенерированную последовательность байтов
     *
     * @param int $length Длина строки
     *
     * @return string
     */
    public static function randomBytes(int $length): string {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= chr(mt_rand(0, 255));
        }
        return $result;
    }

    /**
     * Генерирует UUID версии 4
     *
     * @return string
     */
    public static function generateUUIDv4(): string {
        $data    = self::randomBytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Установка версии в 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Установка 6-7 битов в to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Возвращает хеш пароля
     *
     * @param string $password Пароль
     *
     * @return string
     */
    public static function passwordHash(string $password): string {
        return password_hash($password, self::PASSWORD_ALGO);
    }

    /**
     * Проверяет соответствие пароля хешу
     *
     * @param string $password Пароль
     * @param string $hash     Хеш пароля
     *
     * @return bool
     */
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    /**
     * Генерирует значение токена безопасности
     *
     * @return string
     */
    public static function securityToken(): string {
        $uuid = Crypto::generateUUIDv4();
        return base64_encode($uuid);
    }
}
