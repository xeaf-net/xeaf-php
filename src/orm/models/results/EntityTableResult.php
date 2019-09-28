<?php

/**
 * EntityTableResult.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-ORM
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\ORM\Models\Results;

use XEAF\API\Core\DataObject;
use XEAF\API\Models\Results\TableResult;
use XEAF\API\Utils\DataObjectList;
use XEAF\API\Utils\HttpStatusCodes;

/**
 * Содержит список сущностей для построения таблицы
 *
 * @package  XEAF\ORM\Models\Results
 */
class EntityTableResult extends TableResult {

    /**
     * Конструктор класса
     *
     * @param \XEAF\API\Utils\DataObjectList|null $list            Список сущностей
     * @param array                               $map             Карта свойств
     * @param int                                 $recordsTotal    Общее количество записей
     * @param int                                 $recordsFiltered Количество отфильтрованных записей
     * @param int                                 $statusCode      Код статуса ответа
     */
    public function __construct(DataObjectList $list = null, array $map = [], int $recordsTotal = 0, int $recordsFiltered = 0, int $statusCode = HttpStatusCodes::OK) {
        if (!$map) {
            $realList = $list;
        } else {
            $realList = new DataObjectList();
            foreach ($list as $dataObject) {
                assert($dataObject instanceof DataObject);
                $realList->push(EntityResult::mappingDataObject($dataObject, $map));
            }
        }
        parent::__construct($realList, $recordsTotal, $recordsFiltered, $statusCode);
    }
}
