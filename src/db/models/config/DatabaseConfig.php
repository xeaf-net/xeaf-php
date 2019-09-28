<?php

/**
 * DatabaseConfig.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-DB
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\DB\Models\Config;

use XEAF\API\Core\DataModel;

/**
 * Содержит параметры конфигурации подключения к базе данных
 *
 * @property-read string $dsn      Строка подключения к базе данных
 * @property-read string $user     Имя пользователя
 * @property-read string $password Пароль
 *
 * @package  XEAF\DB\Models\Config
 */
class DatabaseConfig extends DataModel {

    /**
     * Строка подключения к базе данных
     * @var string
     */
    private $_dsn = '';

    /**
     * Имя пользователя
     * @var string
     */
    private $_user = '';

    /**
     * Пароль
     * @var string
     */
    private $_password = '';

    /**
     * Конструктор класса
     *
     * @param object $data Неразобранные параметры конфигурации
     */
    public function __construct(object $data) {
        parent::__construct();
        $this->assignVarIfNotNull($this->_dsn, $data->{'dsn'});
        $this->assignVarIfNotNull($this->_user, $data->{'user'});
        $this->assignVarIfNotNull($this->_password, $data->{'password'});
    }

    /**
     * Возвращает строку подключения к базе данных
     *
     * @return string
     */
    protected function getDsn(): string {
        return $this->_dsn;
    }

    /**
     * Возвращает имя пользователя
     *
     * @return string
     */
    protected function getUser(): string {
        return $this->_user;
    }

    /**
     * Возвращает пароль
     *
     * @return string
     */
    protected function getPassword(): string {
        return $this->_password;
    }
}
