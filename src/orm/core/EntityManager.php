<?php

/**
 * EntityManager.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-ORM
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\ORM\Core;

use XEAF\API\Core\FactoryObject;
use XEAF\DB\Utils\Database;
use XEAF\DB\Utils\Exceptions\DatabaseException;
use XEAF\ORM\Models\EntityModel;
use XEAF\ORM\Models\EntityProperty;
use XEAF\ORM\Utils\EntityParser;
use XEAF\ORM\Utils\EntityQuery;
use XEAF\ORM\Utils\Exceptions\EntityException;

/**
 * Реализует методы менееджера сущностей
 *
 * @property-read \XEAF\DB\Utils\Database $db Объект подключения к базе данных
 *
 * @package  XEAF\ORM\Core
 */
abstract class EntityManager extends FactoryObject {

    /**
     * Подключение к базе данных
     * @var \XEAF\DB\Utils\Database
     */
    protected $_db = null;

    /**
     * Модели объявленных сущностей
     * @var array
     */
    protected $_entityModels = [];

    /**
     * Объекты отслеживаемых сущностей
     * @var array
     */
    protected $_watchEntities = [];

    /**
     * Оригинальное значение свойств отслеживаемых сущностей
     * @var array
     */
    protected $_watchOriginals = [];

    /**
     * Конструктор класса
     *
     * @param string $name Имя объекта
     *
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     */
    public function __construct(string $name) {
        parent::__construct($name);
        foreach ($this->defineEntities() as $entityName => $className) {
            $this->defineEntity($entityName, $className);
        }
        $this->_db = Database::getInstance($name);
    }

    /**
     * Определяет сущность
     *
     * @param string $name      Имя сущности
     * @param string $className Имя класс сущности
     *
     * @return void
     */
    protected function defineEntity(string $name, string $className): void {
        $item = new $className();
        assert($item instanceof Entity);
        $this->_entityModels[$name] = $item->entityModel;
    }

    /**
     * Возвращает поеределение сущностей
     *
     * @return array
     */
    abstract protected function defineEntities(): array;

    /**
     * Возвращает признак существования сущности
     *
     * @param string $entityName Имя сущности
     *
     * @return bool
     */
    public function entityExists(string $entityName): bool {
        $model = $this->entityModelByName($entityName);
        return $model != null;
    }

    /**
     * Возвращает признак существования свойства сущности
     *
     * @param string $entityName Имя сущности
     * @param string $property   Имя свойства
     *
     * @return bool
     */
    public function entityPropertyExists(string $entityName, string $property): bool {
        $result = false;
        $model  = $this->entityModelByName($entityName);
        if ($model) {
            $result = $model->entityPropertyExists($property);
        }
        return $result;
    }

    /**
     * Возвращает модель сущности по имени сущности
     *
     * @param string $entityName Имя сущности
     *
     * @return \XEAF\ORM\Models\EntityModel|null
     */
    public function entityModelByName(string $entityName): ?EntityModel {
        return $this->_entityModels[$entityName] ?? null;
    }

    /**
     * Возвращает модель сущности по классу сущности
     *
     * @param string $className Имя класса сущности
     *
     * @return \XEAF\ORM\Models\EntityModel|null
     */
    public function entityModelByClassName(string $className): ?EntityModel {
        $result = null;
        foreach ($this->_entityModels as $name => $model) {
            assert($model instanceof EntityModel);
            if ($model->entityClass == $className) {
                $result = $model;
                break;
            }
        }
        return $result;
    }

    /**
     * Возвращает имя сущности по классу сущности
     *
     * @param string $className Имя класса сущности
     *
     * @return string|null
     */
    public function entityNameByClassName(string $className): ?string {
        $result = null;
        foreach ($this->_entityModels as $name => $model) {
            assert($model instanceof EntityModel);
            if ($model->entityClass == $className) {
                $result = $name;
                break;
            }
        }
        return $result;
    }

    /**
     * Возвращает объект описания свойства сущности
     *
     * @param string $entityName Имя сущности
     * @param string $property   Свойство
     *
     * @return \XEAF\ORM\Models\EntityProperty|null
     */
    public function entityProperty(string $entityName, string $property): ?EntityProperty {
        $result = null;
        $model  = $this->entityModelByName($entityName);
        if ($model) {
            $result = $model->entityProperty($property);
        }
        return $result;
    }

    /**
     * Создает объект запроса
     *
     * @param string|null $xql Текст запроса XQL
     *
     * @return \XEAF\ORM\Utils\EntityQuery
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function query(string $xql = null): EntityQuery {
        if ($xql) {
            $result = EntityParser::createEntityQuery($xql, $this);
        } else {
            $result = new EntityQuery($this);
        }
        return $result;
    }

    /**
     * Начинает транзакцию
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function startTransaction(): void {
        try {
            $this->_db->startTransaction();
        } catch (DatabaseException $dbe) {
            throw EntityException::internalError($dbe);
        }
    }

    /**
     * Подтверждает изменения в транзакции
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function commit(): void {
        try {
            $this->_db->commit();
        } catch (DatabaseException $dbe) {
            throw EntityException::internalError($dbe);
        }
    }

    /**
     * Отменяет изменения в транзакции
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function rollback(): void {
        try {
            $this->_db->rollback();
        } catch (DatabaseException $dbe) {
            throw EntityException::internalError($dbe);
        }
    }

    /**
     * Загружает объект сущности из БД по первичному ключу
     *
     * @param string $name        Имя сущностие
     * @param array  $primaryKeys Значения первичного ключа
     *
     * @return \XEAF\ORM\Core\Entity|null
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function get(string $name, array $primaryKeys): ?Entity {
        $xql    = EntityParser::defaultSelectEntityXQL($this, $name);
        $result = $this->query($xql)
                       ->getFirst($primaryKeys);
        assert($result instanceof Entity);
        return $result;
    }

    /**
     * Сохранение изменений сущности в базе данных
     *
     * @param \XEAF\ORM\Core\Entity $entity Объект сущности
     *
     * @return \XEAF\ORM\Core\Entity
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function persist(Entity $entity): Entity {
        try {
            return $this->watched($entity) ? $this->persistUpdate($entity) : $this->persistInsert($entity);
        } catch (DatabaseException $dbe) {
            throw EntityException::internalError($dbe);
        }
    }

    /**
     * Сохраняет изменения сущности посредством создания новой записи
     *
     * @param \XEAF\ORM\Core\Entity $entity Объект сущности
     *
     * @return \XEAF\ORM\Core\Entity
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected function persistInsert(Entity $entity): Entity {
        $model = $this->entityModelByClassName($entity->className);
        if ($model) {
            $sql = $model->insertSQL();
            $prm = $model->insertParams($entity);
            $this->_db->execute($sql, $prm);
            if ($model->autoIncrementName) {
                $ai          = $model->autoIncrementName;
                $entity->$ai = $this->_db->lastInsertId();
            }
            $this->watch($entity);
            return $entity;
        }
        throw EntityException::unknownEntityClass($entity->className);
    }

    /**
     * Сохраняет изменения сущности посредством изменения записи
     *
     * @param \XEAF\ORM\Core\Entity $entity Объект сущности
     *
     * @return \XEAF\ORM\Core\Entity
     * @throws \XEAF\DB\Utils\Exceptions\DatabaseException
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    protected function persistUpdate(Entity $entity): Entity {
        $model = $this->entityModelByClassName($entity->className);
        if ($model) {
            if ($this->modified($entity)) {
                $sql = $model->updateSQL();
                $prm = $model->updateParams($entity);
                $this->_db->execute($sql, $prm);
            }
            return $entity;
        }
        throw EntityException::unknownEntityClass($entity->className);
    }

    /**
     * Удаление объекта сущности
     *
     * @param \XEAF\ORM\Core\Entity $entity Объект сущности
     *
     * @return void
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function delete(Entity $entity): void {
        if ($this->watched($entity)) {
            $model = $this->entityModelByClassName($entity->className); // sic!
            if ($model) {
                try {
                    $sql = $model->deleteSQL();
                    $prm = $model->deleteParams($entity);
                    $this->_db->execute($sql, $prm);
                    $this->stopWatch($entity);
                } catch (DatabaseException $dbe) {
                    throw EntityException::internalError($dbe);
                }
            } else {
                throw EntityException::unknownEntityClass($entity->className);
            }
        }
    }

    /**
     * Возвращает признак отслеживания сущности
     *
     * @param \XEAF\ORM\Core\Entity $entity Объект сущности
     *
     * @return bool
     */
    public function watched(Entity $entity): bool {
        return array_key_exists($entity->entityId, $this->_watchOriginals);
    }

    /**
     * Добавление сущности в список отслеживаемых
     *
     * @param \XEAF\ORM\Core\Entity $entity Объект сущности
     *
     * @return \XEAF\ORM\Core\Entity
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public function watch(Entity $entity): Entity {
        $result   = $entity;
        $entityId = $entity->entityId;
        if ($entityId) {
            if (!$this->watched($entity)) {
                $this->_watchOriginals[$entityId] = clone $entity;
                $this->_watchEntities[$entityId]  = $entity;
            } else {
                $result = $this->_watchEntities[$entityId];
            }
        } else {
            throw EntityException::watchNullObject();
        }
        return $result;
    }

    /**
     * Удаляет объект сущности из списка отслеживаемых
     *
     * @param \XEAF\ORM\Core\Entity $entity Объект сущности
     *
     * @return void
     */
    public function stopWatch(Entity $entity): void {
        $entityId = $entity->entityId;
        unset($this->_watchOriginals[$entityId]);
        unset($this->_watchEntities[$entityId]);
    }

    /**
     * Возвращает признак измененного объекта сущности
     *
     * @param \XEAF\ORM\Core\Entity $entity Объект сущности
     *
     * @return bool
     */
    public function modified(Entity $entity): bool {
        $result = !$this->watched($entity);
        if (!$result) {
            $original = $this->_watchOriginals[$entity->entityId];
            assert($original instanceof Entity);
            foreach ($entity->entityModel->entityProperties as $name => $property) {
                assert($property instanceof EntityProperty);
                if (!$property->readOnly) {
                    if ($entity->$name != $original->$name) {
                        $result = true;
                        break;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Возвращает признак измененного свойства объекта сущности
     *
     * @param \XEAF\ORM\Core\Entity $entity Объект сущности
     * @param string                $name   Имя свойства
     *
     * @return bool
     */
    public function propertyModified(Entity $entity, string $name): bool {
        $result = !$this->watched($entity);
        if (!$result) {
            $original = $this->_watchOriginals[$entity->entityId];
            $property = $entity->entityModel->entityProperties[$name];
            assert($property instanceof EntityProperty);
            if (!$property->readOnly) {
                if ($entity->$name != $original->$name) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    /**
     * Восстанавливает значения свойств объекта сущности
     *
     * @param \XEAF\ORM\Core\Entity $entity Объект сущности
     *
     * @return void
     */
    public function restore(Entity $entity): void {
        if ($this->watched($entity)) {
            $original = $this->_watchOriginals[$entity->entityId];
            assert($original instanceof Entity);
            foreach ($entity->entityModel->entityProperties as $name => $property) {
                assert($property instanceof EntityProperty);
                $entity->$name = $original->$name;
            }
        }
    }

    /**
     * Возвращает объект подключения к базе данных
     *
     * @return \XEAF\DB\Utils\Database|null
     */
    public function getDb(): ?Database {
        return $this->_db;
    }
}
