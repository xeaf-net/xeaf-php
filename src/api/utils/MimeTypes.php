<?php

/**
 * MimeTypes.php
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
 * Содержит таблицы поддерживаемых типов MIME
 *
 * @package  XEAF\API\Utils
 */
class MimeTypes {

    /**
     * Тип MIME по умолчанию
     */
    public const DEFAULT_MIME_TYPE = 'application/octet-stream';

    /**
     * Изображения
     */
    public const IMAGE = 1;

    /**
     * Аудиофайлы
     */
    public const AUDIO = 2;

    /**
     * Видеофайлы
     */
    public const VIDEO = 3;

    /**
     * Файлы ресурсов
     */
    public const RESOURCE = 4;

    /**
     * Прочие файлы приложений
     */
    public const FILE = 5;

    /**
     * Список всех поддерживаемых типов MIME
     * @var array
     */
    private static $_mimeTypes = [];

    /**
     * Признак необходимости перестроения общего списка
     * @var bool
     */
    private static $_rebuildList = true;

    /**
     * Форматы файлов изображений
     */
    private static $_mime_images = [
        'bmp'  => 'image/bmp',
        'gif'  => 'image/gif',
        'ico'  => 'image/x-icon',
        'jpg'  => 'jpg - image/jpeg',
        'jpeg' => 'jpg - image/jpeg',
        'png'  => 'image/png',
        'svg'  => 'image/svg+xml',
    ];

    /**
     * Форматы аудиофайлов
     */
    private static $_mime_audio = [
        'mp3' => 'audio/mpeg3',
    ];

    /**
     * Форматы видеофайлов
     */
    private static $_mime_video = [
        'avi' => 'video/x-msvideo',
        'mov' => 'video/quicktime',
        'mp4' => 'video/mp4',
    ];

    /**
     * Форматы файлов ресурсов
     */
    private static $_mime_resources = [
        'css'   => 'text/css',
        'eot'   => 'application/vnd.ms-fontobject',
        'js'    => 'application/x-javascript',
        'json'  => 'application/json',
        'lang'  => 'application/json',
        'map'   => 'application/json',
        'ttf'   => 'application/x-font-ttf',
        'woff'  => 'application/font-woff',
        'woff2' => 'application/font-woff',
    ];

    /**
     * Прочие поддердиваемые форматы файлов
     */
    private static $_mime_files = [
        '7z'   => 'application/x-7z-compressed',
        'ai'   => 'application/illustrator',
        'doc'  => 'application/msword',
        'docx' => 'application/vndopenxmlformats-officedocumentwordprocessingmldocument',
        'ppt'  => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'rar'  => 'application/x-rar',
        'txt'  => 'text/plain',
        'xls'  => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xml'  => 'text/xml',
        'zip'  => 'application/zip'
    ];

    /**
     * Перестраивает общий список типов MIME
     *
     * @return void
     */
    private static function rebuildList() {
        if (self::$_rebuildList) {
            self::$_mimeTypes   = array_merge(self::$_mime_images, self::$_mime_audio, self::$_mime_video, self::$_mime_resources, self::$_mime_files);
            self::$_rebuildList = false;
        }
    }

    /**
     * Возвращает тип MIME для заданного типа файла
     *
     * @param string $fileType Тип файла
     *
     * @return string
     */
    public static function getMimeType(string $fileType): string {
        self::rebuildList();
        return self::$_mimeTypes[strtolower($fileType)] ?? self::DEFAULT_MIME_TYPE;
    }

    /**
     * Возвращает признак поддержки типа файла
     *
     * @param string $fileType Тип файла
     *
     * @return bool
     */
    public static function isSupported(string $fileType): bool {
        self::rebuildList();
        return isset(self::$_mimeTypes[strtolower($fileType)]);
    }

    /**
     * Возвращает признак файла изображения
     *
     * @param string $fileType Тип файла
     *
     * @return bool
     */
    public static function isImage(string $fileType): bool {
        return isset(self::$_mime_images[strtolower($fileType)]);
    }

    /**
     * Возвращает признак аудиофайла
     *
     * @param string $fileType Тип файла
     *
     * @return bool
     */
    public static function isAudio(string $fileType): bool {
        return isset(self::$_mime_audio[strtolower($fileType)]);
    }

    /**
     * Возвращает признак видеофайла
     *
     * @param string $fileType Тип файла
     *
     * @return bool
     */
    public static function isVideo(string $fileType): bool {
        return isset(self::$_mime_video[strtolower($fileType)]);
    }

    /**
     * Возвращает признак файла ресурса
     *
     * @param string $fileType Тип файла
     *
     * @return bool
     */
    public static function isResource(string $fileType): bool {
        return isset(self::$_mime_resources[strtolower($fileType)]);
    }

    /**
     * Регистрирует новый тип MIME
     *
     * @param int    $category Категория
     * @param string $fileType Тип файла
     * @param string $mimeType Тип MIME
     *
     * @return void
     */
    public static function registerMimeType(int $category, string $fileType, string $mimeType): void {
        switch ($category) {
            case self::IMAGE:
                self::$_mime_images[$fileType] = $mimeType;
                break;
            case self::AUDIO:
                self::$_mime_audio[$fileType] = $mimeType;
                break;
            case self::VIDEO:
                self::$_mime_video[$fileType] = $mimeType;
                break;
            case self::RESOURCE:
                self::$_mime_resources[$fileType] = $mimeType;
                break;
            case self::FILE:
                self::$_mime_files[$fileType] = $mimeType;
                break;
        }
        self::$_rebuildList = true;
    }

    /**
     * Отменяет регистрацию типа MIME
     *
     * @param string $fileType Тип файла
     *
     * @return void
     */
    public static function unregisterMimeType(string $fileType): void {
        unset(self::$_mime_images[$fileType]);
        unset(self::$_mime_audio[$fileType]);
        unset(self::$_mime_video[$fileType]);
        unset(self::$_mime_resources[$fileType]);
        unset(self::$_mime_files[$fileType]);
        self::$_rebuildList = true;
    }
}
