<?php

/**
 * DataObjectList.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

use XEAF\API\Core\DataObject;

/**
 * Реализует методы работы со списком объектов данных
 *
 * @package  XEAF\API\Utils
 */
class DataObjectList extends Queue {

    /**
     * Извлекает объект из коллекции
     *
     * @return \XEAF\API\Core\DataObject|null
     * @throws \XEAF\API\Utils\Exceptions\CollectionException
     */
    public function pop(): ?DataObject {
        return parent::pop();
    }

    /**
     * Помещает объект в коллекцию
     *
     * @param \XEAF\API\Core\DataObject|null $item Элемент коллекции
     *
     * @return void
     */
    public function push($item): void {
        assert($item instanceof DataObject);
        parent::push($item);
    }

    /**
     * Возвращает первый элемент коллекции
     *
     * @return \XEAF\API\Core\DataObject|null
     * @throws \XEAF\API\Utils\Exceptions\CollectionException
     */
    public function first(): ?DataObject {
        return parent::first();
    }

    /**
     * Возвращает последний элемент коллекции
     *
     * @return \XEAF\API\Core\DataObject|null
     * @throws \XEAF\API\Utils\Exceptions\CollectionException
     */
    public function last(): ?DataObject {
        return parent::last();
    }
}
