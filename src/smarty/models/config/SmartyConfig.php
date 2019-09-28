<?php

/**
 * SmartyConfig.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-SMARTY
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\Smarty\Models\Config;

use XEAF\API\Core\DataModel;

/**
 * Соержит параметры конфигурации шаблонизатора Smarty
 *
 * @property-read string $cacheDir      Директория кеша
 * @property-read string $compileDir    Директория скомилированных файлов
 * @property-read bool   $enableCaching Признак использования кеширования
 * @property-read bool   $forceCompile  Признак принудительной перекомпиляции шаблонов
 *
 * @package  XEAF\Smarty\Models\Config
 */
class SmartyConfig extends DataModel {

    /**
     * Директория файлов кеша
     * @var string
     */
    private $_cacheDir = '/tmp';

    /**
     * Директория скомпилированных файлов
     * @var string
     */
    private $_compileDir = '/tmp';

    /**
     * Признак использования кеширования
     * @var bool
     */
    private $_enableCaching = false;

    /**
     * Признак принудительной перекомпиляции шаблонов
     * @var bool
     */
    private $_forceCompile = false;

    /**
     * Конструктор класса
     *
     * @param object|null $data Неразобранные параметры конфигурации
     */
    public function __construct(?object $data) {
        parent::__construct();
        if ($data) {
            $this->assignVarIfNotNull($this->_cacheDir, $data->{'cacheDir'} ?? null);
            $this->assignVarIfNotNull($this->_compileDir, $data->{'compileDir'} ?? null);
            $this->assignVarIfNotNull($this->_enableCaching, $data->{'enableCaching'} ?? null);
            $this->assignVarIfNotNull($this->_forceCompile, $data->{'forceCompile'} ?? null);
        }
    }

    /**
     * Возвращаед директорию файлов кеша
     *
     * @return string
     */
    public function getCacheDir(): string {
        return $this->_cacheDir;
    }

    /**
     * Возвращает директорию скомпилированных файлов
     *
     * @return string
     */
    public function getCompileDir(): string {
        return $this->_compileDir;
    }

    /**
     * Возвращает признак использования кеширования
     *
     * @return bool
     */
    public function getEnableCaching(): bool {
        return __XEAF_DEBUG_MODE__ ? false : $this->_enableCaching;
    }

    /**
     * Возвращает признак принудительной перекомпиляции шаблонов
     *
     * @return bool
     */
    public function getForceCompile(): bool {
        return __XEAF_DEBUG_MODE__ ? true : $this->_forceCompile;
    }
}
