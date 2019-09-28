<?php

/**
 * EntityResult.php
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
use XEAF\API\Models\Results\DataResult;
use XEAF\API\Utils\HttpStatusCodes;
use XEAF\ORM\Core\Entity;

/**
 * Содержит один или несколько объектов сущностей
 *
 * @package  XEAF\ORM\Models\Results
 */
class EntityResult extends DataResult {

    /**
     * Конструктор класса
     *
     * @param \XEAF\API\Core\DataObject|null $dataObject Объект данных
     * @param array                          $map        Карта свойств
     * @param int                            $statusCode Код статуса ответа
     */
    public function __construct(DataObject $dataObject = null, array $map = [], int $statusCode = HttpStatusCodes::OK) {
        $realDataObject = self::mappingDataObject($dataObject, $map);
        parent::__construct($realDataObject, $statusCode);
    }

    /**
     * Подготавливает объект данных к отправке
     *
     * @param \XEAF\API\Core\DataObject|null $dataObject Объект данных
     * @param array                          $map        Карта свойств
     *
     * @return \XEAF\API\Core\DataObject|null
     */
    public static function mappingDataObject(?DataObject $dataObject, array $map): ?DataObject {
        $result = $dataObject;
        if ($dataObject) {
            if ($dataObject instanceof Entity) {
                $result = self::prepareSingleEntity($dataObject, $map);
            } else {
                $result = self::prepareMultipleEntities($dataObject, $map);
            }
        }
        return $result;
    }

    /**
     * Подготавливает единичную сущность
     *
     * @param \XEAF\API\Core\DataObject $dataObject Объект данных
     * @param array                     $map        Карта свойств
     *
     * @return \XEAF\API\Core\DataObject|null
     */
    protected static function prepareSingleEntity(DataObject $dataObject, array $map): ?DataObject {
        $result = [];
        assert($dataObject instanceof Entity);
        foreach ($dataObject->formattedPropertyValues() as $name => $value) {
            if (!$map || in_array($name, $map)) {

                $result[$name] = $value;
            }
        }
        return new DataObject($result);
    }

    /**
     * Подготавливает множество сущностей
     *
     * @param \XEAF\API\Core\DataObject $dataObject Объект данных
     * @param array                     $map        Карта свойств
     *
     * @return \XEAF\API\Core\DataObject|null
     */
    protected static function prepareMultipleEntities(DataObject $dataObject, array $map): ?DataObject {
        $result = [];
        foreach ($dataObject->propertyValues as $alias => $entity) {
            assert($entity instanceof DataObject);
            $entityMap = $map[$alias] ?? [];
            if (is_array($entityMap) && $entityMap) {
                $result[$alias] = self::prepareSingleEntity($entity, $entityMap);
            } else {
                $result[$alias] = $entity;
            }
        }
        return new DataObject($result);
    }
}
