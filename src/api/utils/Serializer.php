<?php

/**
 * Serializer.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

use Throwable;
use XEAF\API\Core\DataObject;
use XEAF\API\Utils\Exceptions\SerializerException;

/**
 * Реализует методы сериализации и восстановления данных
 *
 * @package  XEAF\API\Utils
 */
class Serializer {

    /**
     * Максимальная глубина просмотра массивов и объектов
     */
    private const DEPTH = 512;

    /**
     * Поле сохранения данных
     */
    private const DATA_FIELD = 'data';

    /**
     * Поле сохранения хеша
     */
    private const HASH_FIELD = 'hash';

    /**
     * Возвращает представление массива в формате JSON
     *
     * @param array $data Массив данных
     *
     * @return string
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public static function jsonArrayEncode(array $data): string {
        try {
            return json_encode($data, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            throw SerializerException::serializationError($e);
        }
    }

    /**
     * Восстанавливает массив из данных в формате JSON
     *
     * @param string $json Данные в формате JSON
     *
     * @return array
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public static function jsonArrayDecode(string $json): array {
        try {
            return json_decode($json, true, self::DEPTH, JSON_THROW_ON_ERROR);
        } catch (Throwable $reason) {
            throw SerializerException::invalidJsonFormat($reason);
        }
    }

    /**
     * Возвращает JSON представление объекта данных
     *
     * @param \XEAF\API\Core\DataObject $dataObject Объект данных
     *
     * @return string
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public static function jsonDataObjectEncode(DataObject $dataObject): string {
        try {
            return json_encode($dataObject->getPropertyValues(), JSON_THROW_ON_ERROR);
        } catch (Throwable $reason) {
            throw SerializerException::serializationError($reason);
        }
    }

    /**
     * Создает объект данных из JSON
     *
     * @param string $json Исходные данные в формате JSON
     *
     * @return \XEAF\API\Core\DataObject
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public static function jsonDataObjectDecode(string $json): DataObject {
        $data = self::jsonArrayDecode($json);
        return new DataObject($data);
    }

    /**
     * Возвращает JSON представление списка объектов данных
     *
     * @param \XEAF\API\Utils\DataObjectList $list Список объектов данных
     *
     * @return string
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public static function jsonDataObjectListEncode(DataObjectList $list): string {
        $data = [];
        foreach ($list as $item) {
            assert($item instanceof DataObject);
            $data[] = $item->getPropertyValues();
        }
        return self::jsonArrayEncode($data);
    }

    /**
     * Создает список объектов данных из JSON
     *
     * @param string $json Исходные данные в формате JSON
     *
     * @return \XEAF\API\Utils\DataObjectList
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public static function jsonDataObjectListDecode(string $json): DataObjectList {
        $result = new DataObjectList();
        $data   = self::jsonArrayDecode($json);
        foreach ($data as $item) {
            $result->push(new DataObject($item));
        }
        return $result;
    }

    /**
     * Восстанавливает массив из файла данных в формате JSON
     *
     * @param string $fileName Имя файла
     * @param bool   $comments Признак наличия комментариев в файле
     *
     * @return array
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public static function jsonDecodeFile(string $fileName, bool $comments = false): array {
        $result = [];
        if (FileSystem::fileExists($fileName)) {
            $json = file_get_contents($fileName);
            if ($comments) {
                $json = preg_replace('!/\*.*?\*/!s', '', $json);
                $json = preg_replace('/\n\s*\n/', "\n", $json);
                /** @noinspection RegExpRedundantEscape */
                $json = preg_replace('/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/', '', $json);
            }
            $result = Serializer::jsonArrayDecode($json);
        }
        return $result;
    }

    /**
     * Сериализует данные для сохранения
     *
     * @param mixed  $data     Исходные данные
     * @param string $password Пароль для хеша
     *
     * @return string
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public static function serialize($data, string $password = ''): string {
        $result = serialize($data);
        if (!Strings::isEmpty($password)) {
            $data   = [
                self::DATA_FIELD => base64_encode($result),
                self::HASH_FIELD => Crypto::hash($result, $password)
            ];
            $result = self::jsonArrayEncode($data);
        }
        return $result;
    }

    /**
     * Восстанавливает данные из сериализованного представления
     *
     * @param string $serialized Сериализованные данные
     * @param string $password   Пароль для хеша
     *
     * @return mixed
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public static function unserialize(string $serialized, string $password = '') {
        if (Strings::isEmpty($password)) {
            $result = unserialize($serialized);
        } else {
            $arr  = self::jsonArrayDecode($serialized);
            $data = $arr[self::DATA_FIELD] ?? null;
            $hash = $arr[self::HASH_FIELD] ?? null;
            if ($data && $hash) {
                $data    = base64_decode($data);
                $newHash = Crypto::hash($data, $password);
                if (Crypto::hashEquals($newHash, $hash)) {
                    $result = unserialize($data);
                } else {
                    throw SerializerException::dataHashValidationError();
                }
            } else {
                throw SerializerException::dataHashValidationError();
            }
        }
        return $result;
    }
}
