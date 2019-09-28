<?php

/**
 * LoggerConfig.php
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
use XEAF\API\Utils\Logger;

/**
 * Содержит парамеры журнала операций
 *
 * @property-read int    $level  Уровень записей журанала
 * @property-read string $prefix Префикс имени файла
 * @property-read string $path   Директория файлов журналов
 *
 * @package  XEAF\API\Models\Config
 */
class LoggerConfig extends DataModel {

    /**
     * Уровень записей в журнале
     * @var int
     */
    private $_level = Logger::ERROR;

    /**
     * Префикс имени файла журнала
     * @var string
     */
    private $_prefix = 'xeaf';

    /**
     * Директория файлов журнала
     * @var string
     */
    private $_path = '/var/logs';

    /**
     * Конструктор класса
     *
     * @param object $data |null Неразобранные параметры конфигурации
     */
    public function __construct(?object $data) {
        parent::__construct();
        if ($data) {
            $levelName = $data->{'level'} ?? null;
            if ($levelName) {
                $level = array_search($levelName, Logger::LEVEL_NAMES);
                if ($level !== false) {
                    $this->assignVarIfNotNull($this->_level, $level);
                }
            }
            $this->assignVarIfNotNull($this->_prefix, $data->{'prefix'} ?? null);
            $this->assignVarIfNotNull($this->_path, $data->{'path'} ?? null);
        }
    }

    /**
     * Возвращает уровень записей журнала
     *
     * @return int
     */
    public function getLevel(): int {
        return $this->_level;
    }

    /**
     * Возвращет префикс имени файла
     *
     * @return string
     */
    public function getPrefix(): string {
        return $this->_prefix;
    }

    /**
     * Возвращает имя диектории файлов журналов
     *
     * @return string
     */
    public function getPath(): string {
        return $this->_path;
    }
}
