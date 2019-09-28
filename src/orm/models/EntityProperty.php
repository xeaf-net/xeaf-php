<?php

/**
 * EntityProperty.php
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
use XEAF\API\Utils\Formatter;

/**
 * Описывает свойство сущности
 *
 * @property-read int    $dataType      Тип данных
 * @property-read string $fieldName     Имя поля БД
 * @property-read int    $size          Размер
 * @property-read int    $precision     Точность
 * @property-read bool   $primaryKey    Признак первичного ключа
 * @property-read bool   $readOnly      Признак поля только для чтения
 * @property-read bool   $autoIncrement Признак поля с автоинкрементом
 * @property-read mixed  $defaultValue  Значение по умолчанию
 *
 * @package  XEAF\ORM\Models
 */
class EntityProperty extends DataModel {

    /**
     * Тип данных - UUID
     */
    public const DT_UUID = 1;

    /**
     * Тип данных - строка символов
     */
    public const DT_STRING = 2;

    /**
     * Тип данных - целое число
     */
    public const DT_INTEGER = 3;

    /**
     * Тип данных - действительное число
     */
    public const DT_NUMERIC = 4;

    /**
     * Тип данных - дата
     */
    public const DT_DATE = 5;

    /**
     * Тип данных - дата и время
     */
    public const DT_DATETIME = 6;

    /**
     * Тип данных - логическое значение
     */
    public const DT_BOOL = 7;

    /**
     * Тип данных свойства
     * @var int
     */
    private $_dataType = 0;

    /**
     * Размер
     * @var int
     */
    private $_size = 0;

    /**
     * Точность
     * @var int
     */
    private $_precision = 0;

    /**
     * Имя поля таблицы БД
     * @var string
     */
    private $_fieldName = '';

    /**
     * Признак первичного ключа
     * @var bool
     */
    private $_primaryKey = false;

    /**
     * Признак поля только для чтения
     * @var bool
     */
    private $_readOnly = false;

    /**
     * Признак поля с автоинкрементом
     * @var bool
     */
    private $_autoIncrement = false;

    /**
     * Конструктор класса
     *
     * @param int    $dataType      Тип данных
     * @param int    $size          Размер
     * @param int    $precision     Точность
     * @param string $fieldName     Имя поля таблицы БД
     * @param bool   $primaryKey    Признак первичного ключа
     * @param bool   $readOnly      Признак поля только для чтения
     * @param bool   $autoIncrement Признак поля с автоинкрементом
     */
    protected function __construct(int $dataType, int $size, int $precision, string $fieldName = '', bool $primaryKey = false, bool $readOnly = false, bool $autoIncrement = false) {
        parent::__construct();
        $this->_dataType      = $dataType;
        $this->_size          = $size;
        $this->_precision     = $precision;
        $this->_fieldName     = $fieldName;
        $this->_primaryKey    = $primaryKey;
        $this->_readOnly      = $readOnly;
        $this->_autoIncrement = $autoIncrement;
    }

    /**
     * Возвращает тип данных
     *
     * @return int
     */
    public function getDataType(): int {
        return $this->_dataType;
    }

    /**
     * Возвращает размер
     *
     * @return int
     */
    public function getSize(): int {
        return $this->_size;
    }

    /**
     * Возвращает точность
     *
     * @return int
     */
    public function getPrecision(): int {
        return $this->_precision;
    }

    /**
     * Возвращает имя поля БД
     *
     * @return string
     */
    public function getFieldName(): string {
        return $this->_fieldName;
    }

    /**
     * Возвращает признак первичного ключа
     *
     * @return bool
     */
    public function getPrimaryKey(): bool {
        return $this->_primaryKey;
    }

    /**
     * Возвращает признак поля только для чтения
     *
     * @return bool
     */
    public function getReadOnly(): bool {
        return $this->_readOnly;
    }

    /**
     * Возвращает признак поля с автоинкрементом
     *
     * @return bool
     */
    public function getAutoIncrement(): bool {
        return $this->_autoIncrement;
    }

    /**
     * Возвращает значение по умолчанию
     *
     * @return mixed|null
     */
    public function getDefaultValue() {
        $result = null;
        switch ($this->_dataType) {
            case self::DT_STRING:
                $result = '';
                break;
            case self::DT_INTEGER:
            case self::DT_NUMERIC:
            case self::DT_DATE:
            case self::DT_DATETIME:
                $result = 0;
                break;
            case self::DT_BOOL:
                $result = false;
                break;
        }
        return $result;
    }

    /**
     * Форматирует значение свойства
     *
     * @param mixed|null $value Значение свойства
     *
     * @return string|null
     */
    public function formatValue($value): ?string {
        $result = null;
        switch ($this->_dataType) {
            case self::DT_BOOL:
                $result = Formatter::formatBool($value);
                break;
            case self::DT_INTEGER:
                $result = Formatter::formatInteger($value);
                break;
            case self::DT_NUMERIC:
                $result = Formatter::formatNumeric($value, $this->_precision);
                break;
            case self::DT_DATE:
                $result = Formatter::formatDate($value);
                break;
            case self::DT_DATETIME:
                $result = Formatter::formatDateTime($value);
                break;
            default:
                $result = $value;
                break;
        }
        return $result;
    }

    /**
     * Создает описание свойства типа UUID
     *
     * @param string $fieldName  Имя поля БД
     * @param bool   $primaryKey Признак первичного ключа
     * @param bool   $readOnly   Признак поля только для чтения
     *
     * @return \XEAF\ORM\Models\EntityProperty
     */
    public static function uuid(string $fieldName = '', bool $primaryKey = false, bool $readOnly = false): EntityProperty {
        return new self(self::DT_UUID, 0, 0, $fieldName, $primaryKey, $readOnly);
    }

    /**
     * Создает описание свойства строкового типа
     *
     * @param string $fieldName  Имя поля БД
     * @param int    $length     Длина
     * @param bool   $primaryKey Признак первичного ключа
     * @param bool   $readOnly   Признак поля только для чтения
     *
     * @return \XEAF\ORM\Models\EntityProperty
     */
    public static function string(string $fieldName = '', int $length = 255, bool $primaryKey = false, bool $readOnly = false): EntityProperty {
        return new self(self::DT_STRING, $length, 0, $fieldName, $primaryKey, $readOnly);
    }

    /**
     * Создает описание свойства строкового типа для текста
     *
     * @param string $fieldName  Имя поля БД
     * @param bool   $primaryKey Признак первичного ключа
     * @param bool   $readOnly   Признак поля только для чтения
     *
     * @return \XEAF\ORM\Models\EntityProperty
     */
    public static function text(string $fieldName = '', bool $primaryKey = false, bool $readOnly = false): EntityProperty {
        return new self(self::DT_STRING, 0, 0, $fieldName, $primaryKey, $readOnly);
    }

    /**
     * Создает описание свойства целочисленного типа
     *
     * @param string $fieldName     Имя поля БД
     * @param bool   $primaryKey    Признак первичного ключа
     * @param bool   $readOnly      Признак поля только для чтения
     * @param bool   $autoIncrement Признак поля с автоинкрементом
     *
     * @return \XEAF\ORM\Models\EntityProperty
     */
    public static function integer(string $fieldName = '', bool $primaryKey = false, bool $readOnly = false, bool $autoIncrement = false): EntityProperty {
        return new self(self::DT_INTEGER, 0, 0, $fieldName, $primaryKey, $readOnly, $autoIncrement);
    }

    /**
     * Создает описание свойства действительного типа
     *
     * @param string $fieldName  Имя поля БД
     * @param int    $size       Размер
     * @param int    $precision  Точность
     * @param bool   $primaryKey Признак первичного ключа
     * @param bool   $readOnly   Признак поля только для чтения
     *
     * @return \XEAF\ORM\Models\EntityProperty
     */
    public static function numeric(string $fieldName = '', int $size = 15, int $precision = 2, bool $primaryKey = false, bool $readOnly = false): EntityProperty {
        return new self(self::DT_NUMERIC, $size, $precision, $fieldName, $primaryKey, $readOnly);
    }

    /**
     * Создает описание свойства типа календарной даты
     *
     * @param string $fieldName  Имя поля БД
     * @param bool   $primaryKey Признак первичного ключа
     * @param bool   $readOnly   Признак поля только для чтения
     *
     * @return \XEAF\ORM\Models\EntityProperty
     */
    public static function date(string $fieldName = '', bool $primaryKey = false, bool $readOnly = false): EntityProperty {
        return new self(self::DT_DATE, 0, 0, $fieldName, $primaryKey, $readOnly);
    }

    /**
     * Создает описание свойства типа календарной даты и времени
     *
     * @param string $fieldName  Имя поля БД
     * @param bool   $primaryKey Признак первичного ключа
     * @param bool   $readOnly   Признак поля только для чтения
     *
     * @return \XEAF\ORM\Models\EntityProperty
     */
    public static function dateTime(string $fieldName = '', bool $primaryKey = false, bool $readOnly = false): EntityProperty {
        return new self(self::DT_DATETIME, 0, 0, $fieldName, $primaryKey, $readOnly);
    }

    /**
     * Создает описание свойства логического типа
     *
     * @param string $fieldName  Имя поля БД
     * @param bool   $primaryKey Признак первичного ключа
     * @param bool   $readOnly   Признак поля только для чтения
     *
     * @return \XEAF\ORM\Models\EntityProperty
     */
    public static function bool(string $fieldName = '', bool $primaryKey = false, bool $readOnly = false): EntityProperty {
        return new self(self::DT_BOOL, 0, 0, $fieldName, $primaryKey, $readOnly);
    }
}
