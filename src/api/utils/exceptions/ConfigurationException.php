<?php

/**
 * ConfigurationException.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils\Exceptions;

use Throwable;
use XEAF\API\Core\Exception;

/**
 * Исключения при разборе параметров конфигурации
 *
 * @package  XEAF\API\Utils\Exceptions
 */
class ConfigurationException extends Exception {

    /**
     * Не найден файл конфигурации
     *
     * @return \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public static function fileNotFound(): self {
        return new self('Could not open configuration file.');
    }

    /**
     * Ошибка разбора файла конфигурации
     *
     * @param \Throwable $reason Причина возникновения ошибки
     *
     * @return \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public static function parsingError(Throwable $reason): self {
        return new self('Error while parsing configuration file.', [], $reason);
    }

    /**
     * Не найден раздел в файле конфигурации
     *
     * @param string $section Имя раздела
     *
     * @return \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public static function sectionNotFound(string $section): self {
        return new self('Could not find section [%s] of configuration file.', [$section]);
    }

    /**
     * Не найден параметр в файле конфигурации
     *
     * @param string $section Имя раздела
     * @param string $name    Параметр
     *
     * @return \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public static function parameterNotFound(string $section, string $name): self {
        return new self('Could not find parameter [%s::%s] of configuration file.', [$section, $name]);
    }
}
