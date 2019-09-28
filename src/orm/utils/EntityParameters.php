<?php

/**
 * EntityParameters.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\ORM\Utils;

use XEAF\API\App\Factory;
use XEAF\API\Core\ActionArgs;
use XEAF\ORM\Models\EntityOrderModel;

/**
 * Реализует служебные методы обработки параметров к спискам сущностей
 *
 * @package  XEAF\ORM\Utils
 */
class EntityParameters {

    /**
     * Лимит по умолчанию для табличных данных
     */
    protected const TABLE_LIMIT = 100;

    /**
     * Лимит по умолчанию для компонента DataTable
     */
    protected const DATA_TABLE_LIMIT = 10;

    /**
     * Признак завершения разбора параметров
     * @var bool
     */
    private static $_parsed = false;

    /**
     * Количество возвращаемых сущностей
     * @var int
     */
    private static $_limit = 0;

    /**
     * Смещение
     * @var int
     */
    private static $_offset = 0;

    /**
     * Строка поиска
     * @var string
     */
    private static $_search = '';

    /**
     * Порядок сортировки
     * @var array
     */
    private static $_order = [];

    /**
     * Применяет к запросу параметры фильтрации
     *
     * @param \XEAF\ORM\Utils\EntityQuery $entityQuery Запрос
     * @param array                       $properties  Фильтруемые свойства
     *
     * @return void
     */
    public static function applyFilter(EntityQuery $entityQuery, array $properties): void {
        self::parseParameters();
        $entityQuery->filter($properties, self::$_search);
    }

    /**
     * Применяет к запросу параметры сортировки
     *
     * @param \XEAF\ORM\Utils\EntityQuery $entityQuery Запрос
     *
     * @return void
     */
    public static function applyOrders(EntityQuery $entityQuery): void {
        self::parseParameters();
        foreach (self::$_order as $column => $dir) {
            $entityQuery->andOrderBy($column, $dir);
        }
    }

    /**
     * Разбирает параметры запроса к сущностям
     *
     * @return void
     */
    protected static function parseParameters(): void {
        if (!self::$_parsed) {
            $args = Factory::getParameters();
            if ($args->getActionArg('columns')) {
                self::parseDataTableParameters($args);
            } else {
                self::parseTableParameters($args);
            }
            self::$_parsed = true;
        }
    }

    /**
     * Разбирает параметры запроса табличных данных
     *
     * @param \XEAF\API\Core\ActionArgs $args Параметры
     *
     * @return void
     */
    protected static function parseTableParameters(ActionArgs $args): void {
        self::$_limit  = $args->getActionArg('limit', self::TABLE_LIMIT);
        self::$_offset = $args->getActionArg('offset', 0);
        self::$_search = $args->getActionArg('search', '');
        self::$_order  = $args->getActionArg('order', []);
    }

    /**
     * Разбирает параметры запроса от компонента DataTable
     *
     * @param \XEAF\API\Core\ActionArgs $args Параметры
     *
     * @return void
     */
    protected static function parseDataTableParameters(ActionArgs $args): void {
        $columns = $args->getActionArg('columns', []);
        $order   = $args->getActionArg('order', []);

        self::$_limit  = $args->getActionArg('length', self::DATA_TABLE_LIMIT);
        self::$_offset = $args->getActionArg('start', 0);
        self::$_search = $args->getActionArg('search', [])['value'] ?? '';

        foreach ($order as $item) {
            $column = $columns[$item['column']]['data'] ?? null;
            if ($column) {
                if ($item['dir'] == 'asc') {
                    $dir = EntityOrderModel::ORDER_ASCENDING;
                } else {
                    $dir = EntityOrderModel::ORDER_DESCENDING;
                }
                self::$_order[$column] = $dir;
            }
        }
    }

    /**
     * Исполняет запрос и возвращает табличные данные
     *
     * @param \XEAF\ORM\Utils\EntityQuery $entityQuery Запрос
     * @param array                       $filtered    Фильтруемые свойства
     * @param array                       $args        Аргументы запроса
     *
     * @return \XEAF\ORM\Utils\EntityCollection
     * @throws \XEAF\ORM\Utils\Exceptions\EntityException
     */
    public static function getFilteredData(EntityQuery $entityQuery, array $filtered = [], $args = []): EntityCollection {
        self::parseParameters();
        if ($filtered) {
            self::applyFilter($entityQuery, $filtered);
        }
        self::applyOrders($entityQuery);
        return $entityQuery->get($args, self::$_limit, self::$_offset);
    }
}
