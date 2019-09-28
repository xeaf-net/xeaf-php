<?php

/**
 * DatabaseSession.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-DB
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\DB\Utils\Sessions;

use XEAF\API\Core\SessionProvider;
use XEAF\API\Utils\Serializer;
use XEAF\DB\Utils\Database;

/**
 * Провайдер сессий базы данных
 *
 * @package  XEAF\DB\Utils\Sessions
 */
class DatabaseSession extends SessionProvider {

    /**
     * Имя провайдера
     */
    public const PROVIDER_NAME = 'database';

    /**
     * Подключение к базе данных
     * @var \XEAF\DB\Utils\Database
     */
    private $_database = null;

    /**
     * Ключ переменной сессии
     * @var string
     */
    private $_sessionKey = '';

    /**
     * Конструктор класса
     *
     * @param string $name Имя объекта
     *
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function __construct(string $name) {
        parent::__construct($name);
        $this->_database   = Database::getInstance($this->name);
        $this->_sessionKey = $this->getSessionId();
    }

    /**
     * Загружает данные сессии
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function loadSessionData(): void {
        $record = $this->_database->selectFirst($this->selectSQL(), ['id' => $this->_sessionKey]);
        if ($record) {
            $data = Serializer::unserialize($record['session_storage_data']);
            foreach ($data as $key => $value) {
                $this->put($key, $value);
            }
        }
    }

    /**
     * Сохраняет данные сессии
     *
     * @return void
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public function saveSessionData(): void {
        $data = Serializer::serialize($this->storedValues());
        $this->_database->executeInTransaction(function () use ($data) {
            $prm = ['id' => $this->_sessionKey, 'dv' => $data];
            $cnt = $this->_database->execute($this->updateSQL(), $prm);
            if (!$cnt) {
                $this->_database->execute($this->insertSQL(), $prm);
            }
        });
    }

    /**
     * Удаляет данные сессии
     *
     * @return void
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function deleteSessionData(): void {
        parent::deleteSessionData();
        $this->_database->executeInTransaction(function () {
            $this->_database->execute($this->deleteSQL(), ['id' => $this->_sessionKey]);
        });
    }

    /**
     * Возвращает текст SQL запроса выбора данных
     *
     * @return string
     */
    protected function selectSQL(): string {
        return '
            # noinspection SqlResolve
            select session_storage_data 
                from 
                    session_storage 
                where 
                    session_storage_id = :id';
    }

    /**
     * Возвращает текст SQL запроса добавления данных
     *
     * @return string
     */
    protected function insertSQL(): string {
        return '
            # noinspection SqlResolve
            insert into session_storage (
                session_storage_id, 
                session_storage_data
            ) values (
                :id, 
                :dv
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
            update session_storage
                set 
                    session_storage_data = :dv, 
                    session_storage_time = now() 
                where 
                    session_storage_id   = :id';
    }

    /**
     * Возвращает текст SQL запроса удаления данных
     *
     * @return string
     */
    protected function deleteSQL(): string {
        return '
            # noinspection SqlResolve
            delete from session_storage
                where 
                    session_storage_id = :id';
    }
}
