<?php

/**
 * DataResult.php
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
use XEAF\API\Core\DataObject;
use XEAF\API\Utils\HttpStatusCodes;

/**
 * Содержит объект данных
 *
 * @property \XEAF\API\Core\DataObject $dataObject Объект данных
 * @property bool                      $cached     Признак использования кеширования
 *
 * @package  XEAF\API\Models\Results
 */
class DataResult extends ActionResult {

    /**
     * Объект данных
     * @var \XEAF\API\Core\DataObject
     */
    private $_dataObject = null;

    /**
     * Признак использования кеширования
     * @var bool
     */
    private $_cached = false;

    /**
     * Конструктор класса
     *
     * @param \XEAF\API\Core\DataObject|null $dataObject Объект данных
     * @param int                            $statusCode Код статуса ответа
     * @param bool                           $cached     Признак использования кеширования
     */
    public function __construct(DataObject $dataObject = null, int $statusCode = HttpStatusCodes::OK, bool $cached = false) {
        parent::__construct(self::DATA, $statusCode);
        $this->_dataObject = $dataObject == null ? new DataObject() : $dataObject;
        $this->_cached     = $cached;
    }

    /**
     * Возвращает объект данных
     *
     * @return \XEAF\API\Core\DataObject|null
     */
    public function getDataObject(): ?DataObject {
        return $this->_dataObject;
    }

    /**
     * Задает объект данных
     *
     * @param \XEAF\API\Core\DataObject|null $dataObject Объект данных
     *
     * @return void
     */
    public function setDataObject(?DataObject $dataObject): void {
        $this->_dataObject = $dataObject;
    }

    /**
     * Возвращает признак использования кеширования
     *
     * @return bool
     */
    public function getCached(): bool {
        return $this->_cached;
    }

    /**
     * Задает признак использования кеширования
     *
     * @param bool $cached Признак использования кеширования
     *
     * @return void
     */
    public function setCached(bool $cached): void {
        $this->_cached = $cached;
    }

    /**
     * Создает объект данных результата по объявлению из массива
     *
     * @param array $data Данные инициализации
     *
     * @return \XEAF\API\Models\Results\DataResult
     */
    public static function fromArray(array $data): self {
        $dataObject = new DataObject($data);
        return new self($dataObject);
    }
}
