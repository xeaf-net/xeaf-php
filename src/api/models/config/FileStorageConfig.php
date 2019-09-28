<?php

/**
 * FileStorageConfig.php
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

/**
 * Содержит парамеры файлового хранилища
 *
 * @property-read string $path Директория файлов
 *
 * @package  XEAF\API\Models\Config
 */
class FileStorageConfig extends DataModel {

    /**
     * Директория файлов журнала
     * @var string
     */
    private $_path = '/tmp';

    /**
     * Конструктор класса
     *
     * @param object $data |null Неразобранные параметры конфигурации
     */
    public function __construct(?object $data) {
        parent::__construct();
        if ($data) {
            $this->assignVarIfNotNull($this->_path, $data->{'path'} ?? null);
        }
    }

    /**
     * Возвращает имя диектории файлов
     *
     * @return string
     */
    public function getPath(): string {
        return rtrim($this->_path, '/');
    }
}
