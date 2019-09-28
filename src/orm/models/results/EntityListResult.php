<?php

/**
 * EntityListResult.php
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
use XEAF\API\Models\Results\ListResult;
use XEAF\API\Utils\DataObjectList;
use XEAF\API\Utils\HttpStatusCodes;

/**
 * Содержит список сущностей
 *
 * @package  XEAF\ORM\Models\Results
 */
class EntityListResult extends ListResult {

    /**
     * Конструктор класса
     *
     * @param \XEAF\API\Utils\DataObjectList|null $list       Список сущностей
     * @param array                               $map        Карта свойств
     * @param int                                 $statusCode Код статуса ответа
     */
    public function __construct(DataObjectList $list = null, array $map = [], int $statusCode = HttpStatusCodes::OK) {
        if (!$map) {
            $realList = $list;
        } else {
            $realList = new DataObjectList();
            foreach ($list as $dataObject) {
                assert($dataObject instanceof DataObject);
                $realList->push(EntityResult::mappingDataObject($dataObject, $map));
            }
        }
        parent::__construct($realList, $statusCode);
    }
}
