<?php

/**
 * Extension.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Core;

use XEAF\API\App\Configuration;
use XEAF\API\App\Factory;
use XEAF\API\Utils\Language;

/**
 * Реализует свойства всех классов расширений проекта
 *
 * @property-read \XEAF\API\App\Configuration $cfg Параметры конфигурации
 * @property-read \XEAF\API\Core\ActionArgs   $prm Параметрвы вызова
 *
 * @package  XEAF\API\Core
 */
abstract class Extension extends StdObject {

    /**
     * Параметры конфигурации
     * @var \XEAF\API\App\Configuration
     */
    private $_cfg = null;

    /**
     * Параметры вызова
     * @var null
     */
    private $_prm = null;

    /**
     * Конструктор класса
     */
    public function __construct() {
        $this->loadLanguageFiles();
    }

    /**
     * Возвращает параметры конфигурации
     *
     * @return \XEAF\API\App\Configuration
     */
    protected function getCfg(): Configuration {
        if ($this->_cfg == null) {
            $this->_cfg = Factory::getConfiguration();
        }
        return $this->_cfg;
    }

    /**
     * Возвращает параметры вызова приложения
     *
     * @return \XEAF\API\Core\ActionArgs
     */
    protected function getPrm(): ActionArgs {
        if ($this->_prm == null) {
            $this->_prm = Factory::getParameters();
        }
        return $this->_prm;
    }

    /**
     * Загружает файлы языковых переменных
     *
     * @return void
     */
    protected function loadLanguageFiles(): void {
        $className = $this->className;
        while ($className != __CLASS__) {
            Language::loadClassLanguageFile($className);
            $className = get_parent_class($className);
        }
    }

    /**
     * Возвращает значение языковой переменной
     *
     * @param string $name
     *
     * @return string
     */
    protected function lang(string $name): string {
        return Language::getLanguageVar($name);
    }
}
