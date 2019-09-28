<?php

/**
 * ActionResult.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Core;

use XEAF\API\Utils\HttpStatusCodes;

/**
 * Реализует общие свойства результатов исполнения действий
 *
 * @property int       $resultType Тип результата
 * @property int       $statusCode Код статуса ответа
 * @property-read bool $isError    Признак ошибки
 *
 * @package  XEAF\API\Core
 */
abstract class ActionResult extends DataModel {

    /**
     * Объект данных
     */
    public const DATA = 11;

    /**
     * Список объектов данных
     */
    public const LIST = 12;

    /**
     * Табличные данные в формате JSON
     */
    public const TABLE = 13;

    /**
     * Файл
     */
    public const FILE = 14;

    /**
     * Перенаправление
     */
    public const REDIRECT = 98;

    /**
     * Ошибка
     */
    public const ERROR = 99;

    /**
     * Тип результата
     * @var int
     */
    private $_resultType = 0;

    /**
     * Код статуса ответа
     * @var int
     */
    private $_statusCode = HttpStatusCodes::OK;

    /**
     * Конструктор класса
     *
     * @param int $resultType Тип возвращаемого результата
     * @param int $statusCode Код статуса ответа
     */
    public function __construct(int $resultType, int $statusCode = HttpStatusCodes::OK) {
        parent::__construct();
        $this->_resultType = $resultType;
        $this->_statusCode = $statusCode;
    }

    /**
     * Позволяет изменить код типа рузультата
     *
     * @param int $resultType Тип результата
     *
     * @return void
     */
    protected function internalChangeResultType(int $resultType): void {
        $this->_resultType = $resultType;
    }

    /**
     * Возвращает тип результата
     *
     * @return int
     */
    public function getResultType(): int {
        return $this->_resultType;
    }

    /**
     * Задает тип результата
     *
     * @param int $resultType Тип результата
     *
     * @return void
     */
    public function setResultType(int $resultType): void {
        $this->_resultType = $resultType;
    }

    /**
     * Возвращает код статуса ответа
     *
     * @return int
     */
    public function getStatusCode(): int {
        return $this->_statusCode;
    }

    /**
     * Задает код статуса ответа
     *
     * @param int $statusCode Код статуса ответа
     *
     * @return void
     */
    public function setStatusCode(int $statusCode): void {
        $this->_statusCode = $statusCode;
    }

    /**
     * Возвращает признак ошибки
     *
     * @return bool
     */
    public function getIsError(): bool {
        return $this->statusCode != HttpStatusCodes::OK;
    }
}
