<?php

/**
 * DatabaseStorage.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-DB
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\DB\Utils\Storage;

use XEAF\API\App\Factory;
use XEAF\API\Core\Storage;
use XEAF\API\Utils\DateTime;
use XEAF\API\Utils\Serializer;
use XEAF\DB\Utils\Database;

/**
 * Реализует методы хранилища базы данных
 *
 * @package  XEAF\DB\Utils\Storage
 */
class DatabaseStorage extends Storage {

    /**
     * Подключение к БД
     * @var \XEAF\DB\Utils\Database
     */
    private $_db = null;

    /**
     * Конструктор класса
     *
     * @param string $name Имя хранилища
     *
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public function __construct(string $name = self::DEFAULT_NAME) {
        parent::__construct($name);
        $this->_db = Database::getInstance($name);
        $this->readAllValues();
    }

    /**
     * Сохраняет значение
     *
     * @param string     $key   Ключ
     * @param mixed|null $value Значение
     * @param int        $ttl   Время жизни в секундах
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function put(string $key, $value = null, int $ttl = 0): void {
        parent::put($key, $value);
        $data   = Serializer::serialize($value);
        $valid  = $ttl > 0 ? DateTime::dateTimeToSQL(time() + $ttl) : null;
        $params = ['id' => $key, 'dv' => $data, 'dt' => $valid];
        $this->_db->executeInTransaction(function () use ($params) {
            $cnt = $this->_db->execute($this->updateSQL(), $params);
            if (!$cnt) {
                $this->_db->execute($this->insertSQL(), $params);
            }
        });
    }

    /**
     * Удаляет ранее установленное значение
     *
     * @param string $key Ключ
     *
     * @return void
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function delete(string $key): void {
        parent::delete($key);
        $this->_db->executeInTransaction(function () use ($key) {
            $this->_db->execute($this->deleteSQL(), ['id' => $key]);
        });
    }

    /**
     * Читает все сохраненные значения
     *
     * @return void
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    protected function readAllValues(): void {
        $dt      = DateTime::dateTimeToSQL(time());
        $records = $this->_db->select($this->selectSQL(), ['dt' => $dt]);
        foreach ($records as $record) {
            $value = Serializer::unserialize($record['value_storage_data']);
            parent::put($record['value_storage_name'], $value);
        }
    }

    /**
     * Возвращает текст SQL запроса выбора данных
     *
     * @return string
     */
    protected function selectSQL(): string {
        return '
            # noinspection SqlResolve
            select * from value_storage 
                where 
                    value_storage_valid is null or 
                    value_storage_valid > :dt';
    }

    /**
     * Возвращает текст SQL запроса добавления данных
     *
     * @return string
     */
    protected function insertSQL(): string {
        return '
            # noinspection SqlResolve
            insert into value_storage values (
                :id, 
                :dv,
                :dt
            )';
    }

    /**
     * Возвращает текст SQL запроса обновления данных
     *
     * @return string
     */
    protected function updateSQL(): string {
        return '
            # noinspection SqlResolve
            update value_storage 
                set 
                    value_storage_data  = :dv, 
                    value_storage_valid = :dt 
                where 
                    value_storage_name  = :id';
    }

    /**
     * Возвращает текст SQL запроса удаления данных
     *
     * @return string
     */
    protected function deleteSQL(): string {
        return '
            # noinspection SqlResolve
            delete from value_storage
                where 
                    value_storage_name = :id';
    }

    /**
     * Создает объект хранилища
     *
     * @param string $name Имя объекта
     *
     * @return \XEAF\DB\Utils\Storage\DatabaseStorage
     */
    public static function getInstance(string $name): self {
        $result = Factory::getFactoryObject(self::class, $name);
        assert($result instanceof DatabaseStorage);
        return $result;
    }
}
