<?php

/**
 * CryptoUtils.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

use XEAF\API\App\Factory;
use XEAF\API\Core\FactoryObject;
use XEAF\API\Utils\Interfaces\ICryptoUtils;

class CryptoUtils extends FactoryObject implements ICryptoUtils {

    /**
     * Идентификатор алгоритма построения хеша данных
     */
    private const HASH_ALGO = 'sha256';

    /**
     * Идентификатор алгоритма построения хеша паролей
     */
    private const PASSWORD_ALGO = PASSWORD_DEFAULT;

    /**
     * Возвращает идентификатор алгоритма построения хеша данных
     *
     * @return string
     */
    public function hashAlgo(): string {
        return self::HASH_ALGO;
    }

    /**
     * Возвращает код алгоритма построения хеша паролей
     *
     * @return int
     */
    public function passwordAlgo(): int {
        return self::PASSWORD_ALGO;
    }

    /**
     * Генерирует хеш на основе пароля и строковых данных
     *
     * @param string $data     Данные для хеша
     * @param string $password Пароль
     *
     * @return string
     */
    public function hash(string $data, $password = ''): string {
        return hash_hmac($this->hashAlgo(), $data, $password);
    }

    /**
     * Безопасный метод сравнение хешей
     *
     * @param string $hash1 Известный хеш
     * @param string $hash2 Сравниваемый хеш
     *
     * @return bool
     */
    public function hashEquals(string $hash1, string $hash2): bool {
        return hash_equals($hash1, $hash2);
    }

    /**
     * Возвращает случайно сгенерированную последовательность байтов
     *
     * @param int $length Длина строки
     *
     * @return string
     */
    public function randomBytes(int $length): string {
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
    public function generateUUIDv4(): string {
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
    public function passwordHash(string $password): string {
        return password_hash($password, $this->passwordAlgo());
    }

    /**
     * Проверяет соответствие пароля хешу
     *
     * @param string $password Пароль
     * @param string $hash     Хеш пароля
     *
     * @return bool
     */
    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    /**
     * Генерирует значение токена безопасности
     *
     * @return string
     */
    public function securityToken(): string {
        $uuid = CryptoUtils::generateUUIDv4();
        return base64_encode($uuid);
    }

    /**
     * Возвращает единичный экемпляр объекта
     *
     * @return \XEAF\API\Utils\Interfaces\ICryptoUtils
     */
    public static function getInstance(): ICryptoUtils {
        $result = Factory::getFactoryObject(CryptoUtils::class, self::DEFAULT_NAME);
        assert($result instanceof ICryptoUtils);
        return $result;
    }
}
