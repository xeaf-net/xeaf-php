<?php

/**
 * FileSystem.php
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
 * Реализует методы работы с файлами
 *
 * @package  XEAF\API\Utils
 */
class FileSystem {

    /**
     * Режим новой папки
     */
    public const FOLDER_MODE = 0777;

    /**
     * Возвращает признак существования файла
     *
     * @param string|null $filePath Путь к файлу
     *
     * @return bool
     */
    public static function fileExists(?string $filePath): bool {
        return Strings::isEmpty($filePath) ? false : file_exists($filePath);
    }

    /**
     * Возвращает признак существования папки
     *
     * @param string|null $folder Путь к папке
     *
     * @return bool
     */
    public static function folderExists(?string $folder): bool {
        return self::fileExists($folder) && is_dir($folder);
    }

    /**
     * Проверяет существоание папки и создает ее при необходимости
     *
     * @param string|null $folder Путь к папке
     *
     * @return bool
     */
    public static function checkFolderExists(?string $folder): bool {
        $result = self::folderExists($folder);
        if (!$result) {
            if (!Strings::isEmpty($folder)) {
                mkdir($folder, self::FOLDER_MODE, true);
                $result = self::folderExists($folder);
            }
        }
        return $result;
    }

    /**
     * Удаляет файл
     *
     * @param string $filePath Имя файла
     *
     * @return void
     */
    public static function deleteFile(string $filePath): void {
        if (self::fileExists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Возвращает имя файла без расширения
     *
     * @param string $fileName Имя файла
     *
     * @return string
     */
    public static function removeFileNameExt(string $fileName): string {
        return self::getFileDir($fileName) . '/' . self::getFileName($fileName);
    }

    /**
     * Заменяет расширение в имени файла
     *
     * @param string $fileName Имя файла
     * @param string $newExt   Новое расширение
     *
     * @return string
     */
    public static function changeFileNameExt(string $fileName, string $newExt): string {
        return self::removeFileNameExt($fileName) . '.' . $newExt;
    }

    /**
     * Возвращает директорию файла
     *
     * @param string $filePath Путь к файлу
     *
     * @return string
     */
    public static function getFileDir(string $filePath): string {
        return pathinfo($filePath, PATHINFO_DIRNAME);
    }

    /**
     * Возвращает имя файла
     *
     * @param string $filePath Путь к файлу
     *
     * @return string
     */
    public static function getFileName(string $filePath): string {
        return pathinfo($filePath, PATHINFO_FILENAME);
    }

    /**
     * Возвращает имя файла с расширением
     *
     * @param string $filePath Путь к файлу
     *
     * @return string
     */
    public static function getFileBaseName(string $filePath): string {
        return pathinfo($filePath, PATHINFO_BASENAME);
    }

    /**
     * Возвращает расширение имени файла
     *
     * @param string $filePath Путь к файлу
     *
     * @return string
     */
    public static function getFileNameExt(string $filePath): string {
        return pathinfo($filePath, PATHINFO_EXTENSION);
    }

    /**
     * Выводит содержимое файла
     *
     * @param string $filePath Путь к файлу
     *
     * @return void
     */
    public static function readFileChunks(string $filePath): void {
        if (self::fileExists($filePath)) {
            $handle = fopen($filePath, "rb");
            while (!feof($handle)) {
                $chunk = fread($handle, 8192);
                print $chunk;
            }
            fclose($handle);
        }
    }
}
