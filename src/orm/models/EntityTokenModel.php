<?php

/**
 * EntityTokenModel.php
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

/**
 * Модель данных лексемы
 *
 * @property string $type     Тип лексемы
 * @property string $token    Текст лексемы
 * @property int    $position Позиция в строке символов
 * @property int    $phase    Номер фазы
 * @property string $extra    Дополнительная информация
 *
 * @package  XEAF\ORM\Models
 */
class EntityTokenModel extends DataModel {

    /**
     * Неизвестная лексема
     */
    public const T_UNKNOWN = 0;

    /**
     * Зарезервированное слово
     */
    public const T_WORD = 1;

    /**
     * Константа
     */
    public const T_CONSTANT = 2;

    /**
     * Свойство
     */
    public const T_PROPERTY = 3;

    /**
     * Сущность
     */
    public const T_ENTITY = 4;

    /**
     * Псевдоним
     */
    public const T_ALIAS = 5;

    /**
     * Оператор
     */
    public const T_OPERATOR = 6;

    /**
     * Точка
     */
    public const T_POINT = 7;

    /**
     * Запятая
     */
    public const T_COMMA = 8;

    /**
     * Двоеточие
     */
    public const T_COLON = 9;

    /**
     * Скобка
     */
    public const T_BRACKET = 10;

    /**
     * Параметер
     */
    public const T_PARAMETER = 11;

    /**
     * Тип лексемы
     * @var int
     */
    private $_type = self::T_UNKNOWN;

    /**
     * Текст лексемы
     * @var string
     */
    private $_token = '';

    /**
     * Позиция в строке символов
     * @var int
     */
    private $_position = 0;

    /**
     * Номер фазы
     * @var int
     */
    private $_phase = 0;

    /**
     * Дополнительная информация
     * @var string
     */
    private $_extra = '';

    /**
     * Конструктор класса
     *
     * @param int    $type     Тип лексемы
     * @param string $token    Текст лексемы
     * @param int    $position Поизиция в строке символов
     * @param int    $phase    Номер фазы
     */
    public function __construct(int $type, string $token, int $position, int $phase = 0) {
        parent::__construct();
        $this->_type     = $type;
        $this->_token    = $token;
        $this->_position = $position;
        $this->_phase    = $phase;
    }

    /**
     * Возвращает тип лексемы
     * @return int
     */
    public function getType(): int {
        return $this->_type;
    }

    /**
     * Задает тип лексемы
     *
     * @param int $type Тип лексемы
     *
     * @return void
     */
    public function setType(int $type): void {
        $this->_type = $type;
    }

    /**
     * Возвращает текст лексемы
     * @return string
     */
    public function getToken(): string {
        return $this->_token;
    }

    /**
     * Задает текст лексемы
     *
     * @param string $token Текст лексемы
     *
     * @return void
     */
    public function setToken(string $token): void {
        $this->_token = $token;
    }

    /**
     * Возвращает позицию в строке символов
     * @return int
     */
    public function getPosition(): int {
        return $this->_position;
    }

    /**
     * Задает позицию в строке символов
     *
     * @param int $position Позиция в строке символов
     *
     * @return void
     */
    public function setPosition(int $position): void {
        $this->_position = $position;
    }

    /**
     * Возвращает номер фазы
     *
     * @return int
     */
    public function getPhase(): int {
        return $this->_phase;
    }

    /**
     * Задает номер фазы
     *
     * @param int $phase Номер фазы
     *
     * @return void
     */
    public function setPhase(int $phase): void {
        $this->_phase = $phase;
    }

    /**
     * Возвращает дополнительную информацию
     *
     * @return string
     */
    public function getExtra(): string {
        return $this->_extra;
    }

    /**
     * Задаетдополнительную информацию
     *
     * @param string $extra Дополнительная информация
     *
     * @return void
     */
    public function setExtra(string $extra): void {
        $this->_extra = $extra;
    }
}
