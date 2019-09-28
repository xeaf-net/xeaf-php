<?php

/**
 * ErrorResult.php
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
use XEAF\API\Utils\HttpStatusCodes;

/**
 * Содержит код ошибки исполнения
 *
 * @property      string $message      Общее сообщение об ошибке
 * @property-read array  $objectErrors Список ошибок по объектам
 *
 * @package  XEAF\API\Models\Results
 */
class ErrorResult extends ActionResult {

    /**
     * Общее сообщение об ошибке
     * @var string
     */
    protected $_message = '';

    /**
     * Список ошибок по объектам
     * @var array
     */
    protected $_objectErrors = [];

    /**
     * Конструктор класса
     *
     * @param int    $statusCode Код статуса ответа
     * @param string $message    Сообщение об ошибке
     */
    public function __construct(int $statusCode = HttpStatusCodes::OK, string $message = '') {
        parent::__construct(self::ERROR, $statusCode);
        $this->_message = $message;
    }

    /**
     * Возвращает общее сообщение об ошибке
     *
     * @return string
     */
    public function getMessage(): string {
        return $this->_message;
    }

    /**
     * Задает общее сообщение об ошибке
     *
     * @param string $message Текст сообщения об ошибке
     *
     * @return void
     */
    public function setMessage(string $message): void {
        $this->_message = $message;
    }

    /**
     * Возвращает список ошибок по объектам
     *
     * @return array
     */
    public function getObjectErrors(): array {
        return $this->_objectErrors;
    }

    /**
     * Добавляет ообщение об ошибке объекта
     *
     * @param string $id      Идентификатор
     * @param string $message Текст сообщения
     *
     * @return void
     */
    public function addObjectError(string $id, string $message): void {
        $this->_objectErrors[$id] = $message;
        $this->statusCode         = HttpStatusCodes::BAD_REQUEST;
    }

    /**
     * Возвращает признак ошибки
     *
     * @return bool
     */
    public function getIsError(): bool {
        return parent::getIsError() || count($this->_objectErrors) > 0;
    }

}
