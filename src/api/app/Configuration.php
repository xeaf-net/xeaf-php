<?php

/**
 * Configuration.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\App;

use stdClass;
use XEAF\API\Core\DataModel;
use XEAF\API\Models\Config\PortalConfig;
use XEAF\API\Utils\Exceptions\ConfigurationException;
use XEAF\API\Utils\FileSystem;

/**
 * Содержит свойства параметров конфигурации
 *
 * @property-read \XEAF\API\Models\Config\PortalConfig|null $portal Конфигурация портала
 *
 * @package  XEAF\API\App
 */
class Configuration extends DataModel {

    /**
     * Имя файла конфигурации
     */
    protected const FILE_NAME = 'config';

    /**
     * Расширение имени файла конфигурации
     */
    protected const FILE_NAME_EXT = 'json';

    /**
     * Неразобранные данные
     * @var stdClass
     */
    protected $_data = null;

    /**
     * Параметры конфигурации портала
     * @var \XEAF\API\Models\Config\PortalConfig
     */
    protected $_portal = null;

    /**
     * Параметры подключения к базам данных
     * @var array
     */
    protected $_databases = [];

    /**
     * Параметры подключения к серверам Redis
     * @var array
     */
    protected $_redis = [];

    /**
     * Параметры журнала операций
     * @var \XEAF\API\Models\Config\LoggerConfig
     */
    protected $_logger = null;

    /**
     * Конструктор класса
     *
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public function __construct() {
        parent::__construct();
        $this->readConfigurationFile();
    }

    /**
     * Возвращает имя файла конфигурации
     *
     * @return string
     */
    protected function configFileName(): string {
        $prefix = __XEAF_CONFIG_DIR__ . '/' . self::FILE_NAME;
        $result = $prefix . '.' . self::FILE_NAME_EXT;
        $host   = $_SERVER['SERVER_NAME'] ?? '';
        if ($host) {
            $hostConfigFile = $prefix . '-' . $host . '.' . self::FILE_NAME_EXT;
            if (FileSystem::fileExists($hostConfigFile)) {
                $result = $hostConfigFile;
            }
        }
        return $result;
    }

    /**
     * Читает файл конфигурации
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    protected function readConfigurationFile(): void {
        $fileName = $this->configFileName();
        if (FileSystem::fileExists($fileName)) {
            $json        = file_get_contents($fileName);
            $this->_data = json_decode($json);
        } else {
            throw ConfigurationException::fileNotFound();
        }
    }

    /**
     * Возвращает параметры конфигурации портала
     *
     * @return \XEAF\API\Models\Config\PortalConfig
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public function getPortal(): PortalConfig {
        if ($this->_portal == null) {
            $data = $this->_data->{'portal'} ?? null;
            if ($data) {
                $this->_portal = new PortalConfig($data);
            } else {
                throw ConfigurationException::sectionNotFound('portal');
            }
        }
        return $this->_portal;
    }

    /**
     * Возвращает объект секции файла конфигурации
     *
     * @param string $section    Имя секции
     * @param bool   $mustExists Признак обязательной секции
     *
     * @return object|null
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public function getSection(string $section, bool $mustExists = true): ?object {
        $result = $this->_data->{$section} ?? null;
        if (!$result && $mustExists) {
            throw ConfigurationException::sectionNotFound($section);
        }
        return $result;
    }

    /**
     * Возвращает именованный объект секции файла конфигурации
     *
     * @param string $section Имя секции
     * @param string $name    Имя объекта секции
     *
     * @return object
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public function getNamedSection(string $section, string $name): object {
        $section = $this->getSection($section);
        $result  = $section->{$name} ?? null;
        if (!$result) {
            throw ConfigurationException::parameterNotFound($section, $name);
        }
        return $result;
    }
}
