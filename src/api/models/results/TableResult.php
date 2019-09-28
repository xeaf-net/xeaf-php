<?php

/**
 * TableResult.php
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
use XEAF\API\Utils\DataObjectList;
use XEAF\API\Utils\HttpStatusCodes;

/**
 * Содержит данные для построения таблицы
 *
 * @property int $recordsTotal    Общее количество записей
 * @property int $recordsFiltered Количество отфильтрованных записей
 *
 * @package  XEAF\API\Models\Results
 */
class TableResult extends ListResult {

    /**
     * Общее количество записей
     * @var int
     */
    protected $_recordsTotal = 0;

    /**
     * Количество отфильтрованных записей
     * @var int
     */
    protected $_recordsFiltered = 0;

    /**
     * Конструктор класса
     *
     * @param \XEAF\API\Utils\DataObjectList|null $list            Списое объектов данных
     * @param int                                 $recordsTotal    Общее количество записей
     * @param int                                 $recordsFiltered Количество отфильтрованных записей
     * @param int                                 $statusCode      Код статуса ответа
     */
    public function __construct(DataObjectList $list = null, int $recordsTotal = 0, int $recordsFiltered = 0, int $statusCode = HttpStatusCodes::OK) {
        parent::__construct($list, $statusCode);
        $this->internalChangeResultType(ActionResult::TABLE);
        $this->_recordsTotal    = $recordsTotal;
        $this->_recordsFiltered = $recordsFiltered;
    }

    /**
     * Возвращает общее количество записей
     *
     * @return int
     */
    public function getRecordsTotal(): int {
        if ($this->_recordsTotal == 0) {
            $this->_recordsTotal = $this->list->count();
        }
        return $this->_recordsTotal;
    }

    /**
     * Задает общее количество записей
     *
     * @param int $recordsTotal Общее количество записей
     *
     * @return void
     */
    public function setRecordsTotal(int $recordsTotal): void {
        $this->_recordsTotal = $recordsTotal;
    }

    /**
     * Возвращает количество отфильтрованных записей
     *
     * @return int
     */
    public function getRecordsFiltered(): int {
        if ($this->_recordsFiltered == 0) {
            $this->_recordsFiltered = $this->list->count();
        }
        return $this->_recordsFiltered;
    }

    /**
     * Задает количество отфильтрованных записей
     *
     * @param int $recordsFiltered Количество отфильтрованных записей
     *
     * @return void
     */
    public function setRecordsFiltered(int $recordsFiltered): void {
        $this->_recordsFiltered = $recordsFiltered;
    }
}
