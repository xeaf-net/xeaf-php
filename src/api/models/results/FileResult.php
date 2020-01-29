<?php

/**
 * FileResult.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Models\Results;

use XEAF\API\Core\ActionResult;
use XEAF\API\Utils\FileSystem;
use XEAF\API\Utils\MimeTypes;

/**
 * Содержит данные для отправки файла
 *
 * @property      string $filePath   Путь к файлу
 * @property      bool   $attachment Признак отправки файла как вложения
 * @property      bool   $delete     Признак уаделния после отправки
 * @property      string $mimeType   Тип MIME
 * @property      string $fileName   Имя файла
 * @property-read string $fileType   Тип файла
 *
 * @package  XEAF\API\Models\Results
 */
class FileResult extends ActionResult {

    /**
     * Путь к файлу
     * @var string
     */
    private $_filePath = '';

    /**
     * Имя файла
     * @var string
     */
    private $_fileName = '';

    /**
     * Тип файла
     * @var string
     */
    private $_fileType = '';

    /**
     * Признак отправки файла как вложения
     * @var bool
     */
    private $_attachment = true;

    /**
     * Признак удаления после отправки
     * @var bool
     */
    private $_delete = false;

    /**
     * Тип MIME
     * @var string
     */
    private $_mimeType = '';

    /**
     * Конструктор класса
     *
     * @param string $filePath   Путь к файлу
     * @param bool   $attachment признак оправки как вложение
     */
    public function __construct(string $filePath, bool $attachment = true) {
        parent::__construct(self::FILE);
        $this->setFilePath($filePath);
        $this->_attachment = $attachment;
    }

    /**
     * Возвращает путь к файлу
     *
     * @return string
     */
    public function getFilePath(): string {
        return $this->_filePath;
    }

    /**
     * Задает путь к файлу
     *
     * @param string $filePath Путь к файлу
     *
     * @return void
     */
    public function setFilePath(string $filePath): void {
        $this->_filePath = $filePath;
        $this->_fileType = FileSystem::getFileNameExt($filePath);
        $this->_fileName = FileSystem::getFileBaseName($filePath);
        $this->_mimeType = MimeTypes::getMimeType($this->_fileType);
    }

    /**
     * Возвращает тип MIME
     *
     * @return string
     */
    public function getMimeType(): string {
        return $this->_mimeType;
    }

    /**
     * Задает тип MIME
     *
     * @param string $value Имя файла
     *
     * @return void
     */
    public function setMimeType(string $value): void {
        $this->_mimeType = $value;
    }

    /**
     * Возвращает призанк отправки файла как вложения
     *
     * @return bool
     */
    public function getAttachment(): bool {
        return $this->_attachment;
    }

    /**
     * Задает признак отправки файла как вложения
     *
     * @param bool $value Признак отправки файла
     *
     * @return void
     */
    public function setAttachment(bool $value): void {
        $this->_attachment = $value;
    }

    /**
     * Возвращает признак удаления после отправки
     * @return bool
     */
    public function getDelete(): bool {
        return $this->_delete;
    }

    /**
     * Задает признак уаления после отправки
     *
     * @param bool $delete Признак удаления после отправки
     *
     * @return void
     */
    public function setDelete(bool $delete): void {
        $this->_delete = $delete;
    }

    /**
     * Возвращает отображаемое имя файла
     *
     * @return string
     */
    public function getFileName(): string {
        return $this->_fileName;
    }

    /**
     * Задает отображаемое имя файла
     *
     * @param string $fileName Имя файла
     *
     * @return void
     */
    public function setFileName(string $fileName): void {
        $this->_fileName = $fileName;
    }

    /**
     * Возвращает тип файла
     *
     * @return string
     */
    public function getFileType(): string {
        return $this->_fileType;
    }
}
