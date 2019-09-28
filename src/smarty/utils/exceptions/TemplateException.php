<?php

/**
 * TemplateException.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-SMARTY
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\Smarty\Utils\Exceptions;

use Throwable;
use XEAF\API\Core\Exception;

/**
 * Исключения шаблонизатора
 *
 * @package  XEAF\Smarty\Utils\Exceptions
 */
class TemplateException extends Exception {

    /**
     * Инициализации Ыьфкен
     *
     * @param \Throwable $reason Причина возникновения ошибки
     *
     * @return \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    public static function errorInitializingSmarty(Throwable $reason): self {
        return new self('Could not initialize Smarty.', [], $reason);
    }

    /**
     * Ошибка инициализации встроенных плагинов
     *
     * @param \Throwable $reason Причина ошибки
     *
     * @return \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    public static function errorInitializingDefaultPlugins(Throwable $reason): self {
        return new self('Could not initialize default plugins.', [], $reason);
    }

    /**
     * Ошибка инициализации плагина
     *
     * @param string $name Идентификатор плагина
     *
     * @return \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    public static function errorInitializingPlugin(string $name): self {
        return new self('Could not initialize plugin "%s".', [$name]);
    }

    /**
     * Ошибка во время обработки плагина
     *
     * @param string     $name   Идентификатор плагина
     * @param \Throwable $reason Причина ошибки
     *
     * @return \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    public static function errorProcessingPlugin(string $name, Throwable $reason): self {
        return new self('Error while processing plugin "%s".', [$name], $reason);
    }

    /**
     * Ошибка разбора файла шаблона разметки
     *
     * @param string     $fileName Имя файла
     * @param \Throwable $reason   Причина ошибки
     *
     * @return \XEAF\Smarty\Utils\Exceptions\TemplateException
     */
    public static function errorParsingTemplateFile(string $fileName, Throwable $reason): self {
        return new self('Error while parsing template file "%s".', [$fileName], $reason);
    }
}
