<?php

/**
 * Entity.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-ORM
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\ORM\Core;

use XEAF\API\App\Factory;
use XEAF\API\Core\DataObject;
use XEAF\API\Utils\Crypto;
use XEAF\API\Utils\DateTime;
use XEAF\API\Utils\Strings;
use XEAF\ORM\Models\EntityModel;
use XEAF\ORM\Models\EntityProperty;

/**
 * Реализует базовые методы сущности
 *
 * @property-read \XEAF\ORM\Models\EntityModel $entityModel Модель сущности
 * @property-read string|null                  $primaryKey  Значение первичного ключа
 * @property-read string|null                  $entityId    Уникальный идентификатор сущности
 *
 * @package  XEAF\ORM\Core
 */
abstract class Entity extends DataObject {

    /**
     * Модель сущности
     * @var \XEAF\ORM\Models\EntityModel
     */
    private $_entityModel = null;

    /**
     * Значение первичного ключа
     * @var string|null
     */
    private $_primaryKey = null;

    /**
     * Уникальный идентификатор сущности
     * @var null
     */
    private $_entityId = null;

    /**
     * Конструктор класса
     *
     * @param array|null $data Данные инициализации
     */
    public function __construct(array $data = null) {
        $initData = $this->initializationData($data);
        parent::__construct($initData);
    }

    /**
     * Загружает определение сущности
     *
     * @return void
     */
    private function loadEntityDefinition(): void {
        $storage = Factory::getStaticStorage(__CLASS__);
        $model   = $storage->get($this->className);
        if (!$model) {
            $model = $this->defineEntity();
            $storage->put($this->className, $model);
        }
        $this->_entityModel = $model;
    }

    /**
     * Возвращает массив данных инициализации
     *
     * @param array|null $data Данные инициализации
     *
     * @return array
     */
    protected function initializationData(array $data = null): array {
        $result = [];
        $this->loadEntityDefinition();
        $pkInit = !count($this->_entityModel->primaryKeys) == 1;
        foreach ($this->_entityModel->entityProperties as $name => $property) {
            assert($property instanceof EntityProperty);
            $result[$name] = $data[$name] ?? $property->defaultValue;
            switch ($property->dataType) {
                case EntityProperty::DT_UUID:
                    if ($property->primaryKey && !$pkInit && Strings::isEmpty($result[$name])) {
                        $result[$name] = Crypto::generateUUIDv4();
                    }
                    break;
                case EntityProperty::DT_BOOL:
                    $result[$name] = $result[$name] == 1;
                    break;
                case EntityProperty::DT_DATE:
                    $result[$name] = DateTime::dateFromSQL($result[$name]);
                    break;
                case EntityProperty::DT_DATETIME:
                    $result[$name] = DateTime::dateTimeFromSQL($result[$name]);
                    break;
            }
        }
        return $result;
    }

    /**
     * Задает значения свойств сущности из массива значений полей
     *
     * @param array|null $fieldsData Значения полей
     *
     * @return void
     */
    public function assignFields(array $fieldsData = null): void {
        foreach ($this->_entityModel->entityProperties as $name => $property) {
            assert($property instanceof EntityProperty);
            if (array_key_exists($property->fieldName, $fieldsData)) {
                switch ($property->dataType) {
                    case EntityProperty::DT_BOOL:
                        $value = $fieldsData[$property->fieldName] == 1;
                        break;
                    case EntityProperty::DT_DATE:
                        $value = DateTime::dateFromSQL($fieldsData[$property->fieldName]);
                        break;
                    case EntityProperty::DT_DATETIME:
                        $value = DateTime::dateTimeFromSQL($fieldsData[$property->fieldName]);
                        break;
                    default:
                        $value = $fieldsData[$property->fieldName];
                        break;
                }
                $this->$name = $value;
            }
        }
    }

    /**
     * Возвращает модель сущности
     *
     * @return \XEAF\ORM\Models\EntityModel|null
     */
    public function getEntityModel(): ?EntityModel {
        return $this->_entityModel;
    }

    /**
     * Возвращает значение первичного ключа
     *
     * @return string|null
     */
    public function getPrimaryKey(): ?string {
        if ($this->_primaryKey == null) {
            $buf = '';
            foreach ($this->_entityModel->primaryKeys as $primaryKey) {
                $value = $this->$primaryKey;
                if ($value == null) {
                    return null;
                } else {
                    $buf .= $value . ':';
                }
            }
            $this->_primaryKey = rtrim($buf, ':');
        }
        return $this->_primaryKey;
    }

    /**
     * Возвращает уникальный идентификатор сущности
     *
     * @return string|null
     */
    public function getEntityId(): ?string {
        if (!$this->_entityId) {
            $this->_entityId = md5($this->className . ':' . $this->getPrimaryKey());
        }
        return $this->_entityId;
    }

    /**
     * Возвращает свойства сущности в форматированном виде
     *
     * @return array
     */
    public function formattedPropertyValues(): array {
        $result = [];
        foreach ($this->_entityModel->entityProperties as $name => $property) {
            assert($property instanceof EntityProperty);
            $result[$name] = $property->formatValue($this->$name);
        }
        return $result;
    }

    /**
     * Возвращает определение сущности
     *
     * @return \XEAF\ORM\Models\EntityModel
     */
    abstract protected function defineEntity(): EntityModel;
}
