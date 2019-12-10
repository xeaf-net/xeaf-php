<?php

/**
 * NotificatorConfig.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Models\Config;

use XEAF\API\Core\DataModel;
use XEAF\API\Utils\Crypto;

/**
 * Параметры конфигурации отправки нотификационных сообщений
 *
 * @property-read string $url    URL сервера доставки сообщений
 * @property-read string $key    Ключ отправителя
 * @property-read bool   $enable Признак разрешения отправки сообщений
 *
 * @package  XEAF\API\Models\Config
 */
class NotificatorConfig extends DataModel {

    /**
     * URL сервера
     * @var string
     */
    private $_url = 'http://localhost:8181';

    /**
     * Ключ подключения к серверу
     * @var string
     */
    private $_key = Crypto::ZERO_UUID;

    /**
     * Признак разрешения отправки сообщений
     * @var bool
     */
    private $_enabled = false;

    /**
     * Конструктор класса
     *
     * @param object $data Неразобранные параметры конфигурации
     */
    public function __construct(?object $data) {
        parent::__construct();
        if ($data) {
            $this->assignVarIfNotNull($this->_url, $data->{'url'} ?? null);
            $this->assignVarIfNotNull($this->_key, $data->{'key'} ?? null);
            $this->assignVarIfNotNull($this->_enabled, $data->{'enable'} ?? false);
        }
    }

    /**
     * Возвращает URL сервера доставки сообщений
     *
     * @return string
     */
    public function getUrl(): string {
        return $this->_url;
    }

    /**
     * Возвращает ключ поключения к севреру
     *
     * @return string
     */
    public function getKey(): string {
        return $this->_key;
    }

    /**
     * Возвращает признак разрешения отправки сообщений
     * @return bool
     */
    public function getEnabled(): bool {
        return $this->_enabled;
    }
}
