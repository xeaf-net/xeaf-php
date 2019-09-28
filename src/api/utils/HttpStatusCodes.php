<?php

/**
 * HttpStatusCodes.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

/**
 * Содержит константы кодов и сообщений состояний протокола HTTP
 *
 * @package  XEAF\API\Utils
 */
class HttpStatusCodes {

    /**
     * Успешное завершение с возвратом данных
     */
    public const OK = 200;

    /**
     * Успешное завершение с созданием нового ресурса
     */
    public const CREATED = 201;

    /**
     * Успешное завершение без возврата данных
     */
    public const NO_CONTENT = 204;

    /**
     * Постоянная переадресация
     */
    public const MOVED_PERMANENTLY = 301;

    /**
     * Временная переадресация
     */
    public const MOVED_TEMPORARILY = 302;

    /**
     * Ошибка в запросе
     */
    public const BAD_REQUEST = 400;

    /**
     * Пользователь не авторизован
     */
    public const UNAUTHORIZED = 401;

    /**
     * Недостаточно прав доступа к ресурсу
     */
    public const FORBIDDEN = 403;

    /**
     * Объект не найден
     */
    public const NOT_FOUND = 404;

    /**
     * Конфликт изменения (или удаления) данных
     */
    public const CONFLICT = 409;

    /**
     * Фатальная ошибка сервера
     */
    public const FATAL_ERROR = 500;

    /**
     * Тексты сообщений для кодов ответов HTTP протокола
     */
    public const MESSAGES = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
    ];
}
