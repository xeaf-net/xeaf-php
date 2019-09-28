<?php

/**
 * ListResult.php
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
 * Содержит список объектов данных
 *
 * @property \XEAF\API\Utils\DataObjectList $list Список объектов данных
 *
 * @package  XEAF\API\Models\Results
 */
class ListResult extends ActionResult {

    /**
     * Список объектов данных
     * @var \XEAF\API\Utils\DataObjectList
     */
    protected $_list = null;

    /**
     * Конструктор класса
     *
     * @param \XEAF\API\Utils\DataObjectList|null $list       Список объектов данных
     * @param int                                 $statusCode Код статуса ответа
     */
    public function __construct(DataObjectList $list = null, int $statusCode = HttpStatusCodes::OK) {
        parent::__construct(self::LIST, $statusCode);
        $this->_list = $list == null ? new DataObjectList() : $list;
    }

    /**
     * Возвращает список объектов данных
     *
     * @return \XEAF\API\Utils\DataObjectList|null
     */
    public function getList(): ?DataObjectList {
        return $this->_list;
    }

    /**
     * Задает список объектов данных
     *
     * @param \XEAF\API\Utils\DataObjectList|null $list Список объектов данных
     *
     * @return void
     */
    public function setList(?DataObjectList $list): void {
        $this->_list = $list;
    }
}
