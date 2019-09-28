<?php

/**
 * EntityModel.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-ORM
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\ORM\Models;

use XEAF\API\Core\DataModel;
use XEAF\ORM\Core\Entity;
use XEAF\ORM\Utils\EntityParser;
use XEAF\ORM\Utils\EntityQuery;

/**
 * Реализует базовые свойства сущностей данных
 *
 * @property-read string      $entityClass       Имя класса сущности
 * @property-read string      $tableName         Имя таблицы БД
 * @property-read array       $entityProperties  Массив определений свойств
 * @property-read array       $tableFields       Массив определений полей таблиц БД
 * @property-read array       $primaryKeys       Массив свойств первичного ключа
 * @property-read string|null $autoIncrementName Имя свойства с автоинкрементом
 *
 * @package  XEAF\ORM\Models
 */
class EntityModel extends DataModel {

    /**
     * Имя класса сущности
     * @var string
     */
    private $_entityClass = '';

    /**
     * Идентификатор таблицы
     * @var string
     */
    private $_tableName = '';

    /**
     * Массив определений свойств сущности
     * @var array
     */
    private $_entityProperties = [];

    /**
     * Массив определений полей таблицы БД
     * @var array
     */
    private $_tableFields = [];

    /**
     * Свойства первичного ключа
     * @var array
     */
    private $_primaryKeys = [];

    /**
     * Текст SQL команды добавления записи
     * @var string
     */
    private $_insertSQL = '';

    /**
     * Текст SQL команды изменения записи
     * @var string
     */
    private $_updateSQL = '';

    /**
     * Текст SQL команды удаления записи
     * @var string
     */
    private $_deleteSQL = '';

    /**
     * Имя свойства с автоинкрементом
     * @var string|null
     */
    private $_autoIncrementName = null;

    /**
     * Конструктор класса
     *
     * @param string $entityClass Имя класса сущности
     * @param string $tableName   Имя таблицы БД
     * @param array  $properties  Определения свойств
     */
    public function __construct(string $entityClass, string $tableName, array $properties) {
        parent::__construct();
        $this->_entityClass      = $entityClass;
        $this->_tableName        = $tableName;
        $this->_entityProperties = $properties;
        $this->calcFieldDefinition();
    }

    /**
     * Вычисляет определение поля БД
     *
     * @return void
     */
    private function calcFieldDefinition(): void {
        foreach ($this->_entityProperties as $name => $property) {
            assert($property instanceof EntityProperty);
            $this->_tableFields[$property->fieldName] = $name;
            if ($property->primaryKey) {
                $this->_primaryKeys[] = $name;
                if ($property->autoIncrement && !$this->_autoIncrementName) {
                    $this->_autoIncrementName = $name;
                }
            }
        }
    }

    /**
     * Возвращает имя класса сущности
     *
     * @return string
     */
    public function getEntityClass(): string {
        return $this->_entityClass;
    }

    /**
     * Возвращает имя таблицы БД
     *
     * @return string
     */
    public function getTableName(): string {
        return $this->_tableName;
    }

    /**
     * Возвращает определения свойств сущности
     *
     * @return array
     */
    public function getEntityProperties(): array {
        return $this->_entityProperties;
    }

    /**
     * Возвращает массив определений полей таблицы БД
     *
     * @return array
     */
    public function getTableFields(): array {
        return $this->_tableFields;
    }

    /**
     * Возвращает свойства первичного ключа
     *
     * @return array
     */
    public function getPrimaryKeys(): array {
        return $this->_primaryKeys;
    }

    /**
     * Возвращает признак существоания свойства сущности
     *
     * @param string $name Имя свойства сущности
     *
     * @return bool
     */
    public function entityPropertyExists(string $name): bool {
        return isset($this->_entityProperties[$name]);
    }

    /**
     * Возвращает объект описания свойства сущности
     *
     * @param string $name Имя свойства сущности
     *
     * @return \XEAF\ORM\Models\EntityProperty|null
     */
    public function entityProperty(string $name): ?EntityProperty {
        return $this->_entityProperties[$name] ?? null;
    }

    /**
     * Возвращает объект описания свойства сущности по имени поля
     *
     * @param string $fieldName Имя поля
     *
     * @return \XEAF\ORM\Models\EntityProperty|null
     */
    public function entityPropertyByField(string $fieldName): ?EntityProperty {
        $name = $this->_tableFields[$fieldName] ?? null;
        return $name ? $this->entityProperty($name) : null;
    }

    /**
     * Возвращает текст SQL запроса добавления записи
     *
     * @return string
     */
    public function insertSQL(): string {
        if (!$this->_insertSQL) {
            $this->_insertSQL = EntityParser::defaultInsertEntitySQL($this);
        }
        return $this->_insertSQL;
    }

    /**
     * Возвращает массив параметров для вставки записи
     *
     * @param \XEAF\ORM\Core\Entity $entity Объект сущности
     *
     * @return array
     */
    public function insertParams(Entity $entity): array {
        $result = [];
        $values = $entity->propertyValues;
        foreach ($this->entityProperties as $name => $property) {
            assert($property instanceof EntityProperty);
            if (!$property->autoIncrement && (!$property->readOnly || $property->primaryKey)) {
                $result[$name] = EntityQuery::convertParameter($values[$name], $property->dataType);
            }
        }
        return $result;
    }

    /**
     * Возвращает текст SQL запроса изменения записи
     *
     * @return string
     */
    public function updateSQL(): string {
        if (!$this->_updateSQL) {
            $this->_updateSQL = EntityParser::defaultUpdateEntitySQL($this);
        }
        return $this->_updateSQL;
    }

    /**
     * Возвращает массив параметров для изменения записи
     *
     * @param \XEAF\ORM\Core\Entity $entity Объект сущности
     *
     * @return array
     */
    public function updateParams(Entity $entity): array {
        $result = [];
        $values = $entity->propertyValues;
        foreach ($this->entityProperties as $name => $property) {
            assert($property instanceof EntityProperty);
            if (!$property->readOnly || $property->primaryKey) {
                $result[$name] = EntityQuery::convertParameter($values[$name], $property->dataType);
            }
        }
        return $result;
    }

    /**
     * Возвращает текст SQL запроса удаления записи
     *
     * @return string
     */
    public function deleteSQL(): string {
        if (!$this->_deleteSQL) {
            $this->_deleteSQL = EntityParser::defaultDeleteEntitySQL($this);
        }
        return $this->_deleteSQL;
    }

    /**
     * Возвращает массив параметров для удаления записи
     *
     * @param \XEAF\ORM\Core\Entity $entity Объект сущности
     *
     * @return array
     */
    public function deleteParams(Entity $entity): array {
        $result = [];
        foreach ($this->primaryKeys as $primaryKey) {
            $result[$primaryKey] = $entity->$primaryKey;
        }
        return $result;
    }

    /**
     * Возвращает имя свойства с автоинкрементом
     *
     * @return string|null
     */
    public function getAutoIncrementName(): ?string {
        return $this->_autoIncrementName;
    }
}
