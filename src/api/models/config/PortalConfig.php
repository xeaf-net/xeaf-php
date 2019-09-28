<?php

/**
 * PortalConfig.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Models\Config;

use XEAF\API\Core\DataModel;
use XEAF\API\Utils\Language;
use XEAF\API\Utils\Timezones;

/**
 * Содержит параметры конфигурации портала
 *
 * @property-read string $url      URL портала
 * @property-read string $origin   URL источника запросов
 * @property-read string $language Язык
 * @property-read string $timezone Временная зона
 * @property-read string $session  Параметры открытия сессии
 * @property-read string $tmp      Директория временных файлов
 *
 * @package  XEAF\API\Models\Config
 */
class PortalConfig extends DataModel {

    /**
     * URL портала
     * @var string
     */
    private $_url = 'http://localhost';

    /**
     * URL источника запросов
     * @var string
     */
    private $_origin = '*';

    /**
     * Язык портала
     * @var string
     */
    private $_language = Language::DEFAULT_LANGUAGE;

    /**
     * Временная зона сервера
     * @var string
     */
    private $_timezone = Timezones::UTC;

    /**
     * Провайдер сессии
     * @var string
     */
    private $_session = '';

    /**
     * Директория временных файлов
     * @var string
     */
    private $_tmp = '/tmp';

    /**
     * Конструктор класса
     *
     * @param object $data Неразобранные параметры конфигурации
     */
    public function __construct(object $data) {
        parent::__construct();
        $this->assignVarIfNotNull($this->_url, $data->{'url'} ?? null);
        $this->assignVarIfNotNull($this->_origin, $data->{'origin'} ?? null);
        $this->assignVarIfNotNull($this->_language, $data->{'language'} ?? null);
        $this->assignVarIfNotNull($this->_timezone, $data->{'timezone'} ?? null);
        $this->assignVarIfNotNull($this->_session, $data->{'session'} ?? null);
        $this->assignVarIfNotNull($this->_tmp, $data->{'tmp'} ?? null);
        if ($this->_url) {
            $this->_url = rtrim($this->_url, '/');
        }
    }

    /**
     * Возвращает URL портала
     *
     * @return string
     */
    public function getUrl(): string {
        return $this->_url;
    }

    /**
     * Возвращает URL источника запросов
     *
     * @return string
     */
    public function getOrigin(): string {
        return $this->_origin;
    }

    /**
     * Возвращает язык
     *
     * @return string
     */
    public function getLanguage(): string {
        if (!$this->_language) {
            $this->_language = Language::DEFAULT_LANGUAGE;
        }
        return $this->_language;
    }

    /**
     * Возвращает временную зону
     *
     * @return string
     */
    public function getTimezone(): string {
        return $this->_timezone;
    }

    /**
     * Возвращает параметры открытия сессии
     * @return string
     */
    public function getSession(): ?string {
        return $this->_session;
    }

    /**
     * Возвращает директорию временных файлов
     *
     * @return string
     */
    public function getTmp(): string {
        return $this->_tmp;
    }
}
