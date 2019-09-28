<?php

/**
 * FormResult.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Models\Results;

use XEAF\API\Models\Results\ErrorResult;
use XEAF\API\Utils\HttpStatusCodes;
use XEAF\API\Utils\Language;
use XEAF\UI\Core\ActionResult;

/**
 * Содержит данные валидации формы
 *
 * @property int         $alert      Тип предупреждения
 * @property-read string $alertName  Идентификатор типа предупреждения
 * @property-read string $alertTitle Заголовок предупреждения
 *
 * @package  XEAF\UI\Models\Results
 */
class FormResult extends ErrorResult {

    /**
     * Информационное сообщения
     */
    public const ALERT_INFO = 1;

    /**
     * Успех
     */
    public const ALERT_SUCCESS = 2;

    /**
     * Предупреждение
     */
    public const ALERT_WARNING = 3;

    /**
     * Ошибка
     */
    public const ALERT_DANGER = 4;

    /**
     * Наименования типов предупреждений
     */
    protected const ALERT_NAMES = [
        self::ALERT_INFO    => 'info',
        self::ALERT_SUCCESS => 'success',
        self::ALERT_WARNING => 'warning',
        self::ALERT_DANGER  => 'danger'
    ];

    /**
     * Тип предупреждения
     * @var int
     */
    protected $_alert = self::ALERT_SUCCESS;

    /**
     * Конструктор класса
     *
     * @param int    $statusCode Код статуса ответа
     * @param int    $alert      Тип предупреждения
     * @param string $message    Текст сообщения
     */
    public function __construct(int $statusCode = HttpStatusCodes::OK, int $alert = self::ALERT_SUCCESS, string $message = '') {
        parent::__construct($statusCode, $message);
        $this->resultType = ActionResult::FORM;
        $this->_alert     = $alert;
    }

    /**
     * Возвращает тип предупреждения
     *
     * @return int
     */
    public function getAlert(): int {
        return $this->_alert;
    }

    /**
     * Задает тип предупреждения
     *
     * @param int $alert Тип предупреждения
     *
     * @return void
     */
    public function setAlert(int $alert): void {
        if (isset(self::ALERT_NAMES[$alert])) {
            $this->_alert = $alert;
        }
    }

    /**
     * Возвращает наименование типа предупреждения
     *
     * @return string
     */
    public function getAlertName(): string {
        return self::ALERT_NAMES[$this->_alert];
    }

    /**
     * Возвращает заголовок предупреждения
     *
     * @return string
     */
    public function getAlertTitle(): string {
        return Language::getLanguageVar('alerts.' . self::ALERT_NAMES[$this->_alert]);
    }

    /**
     * Добавляет ообщение об ошибке объекта
     *
     * @param string $id         Идентификатор
     * @param string $message    Текст сообщения
     * @param int    $alert      Тип предупреждения
     * @param int    $statusCode Код статуса ответа
     *
     * @return void
     */
    public function addObjectError(string $id, string $message, int $alert = self::ALERT_DANGER, int $statusCode = HttpStatusCodes::BAD_REQUEST): void {
        parent::addObjectError($id, $message);
        $this->_alert     = $alert;
        $this->statusCode = $statusCode;
    }

    /**
     * Устанавливает сообщение об успешном сохранении данных
     *
     * @return void
     */
    public function messageDataSaved(): void {
        $this->message = Language::getLanguageVar('messages.dataSaved');
    }

    /**
     * Устанавливает сообщение об успешном удалении данных
     *
     * @return void
     */
    public function messageDataDeleted(): void {
        $this->message = Language::getLanguageVar('messages.dataDeleted');
    }

    /**
     * Создает объект c успешным кодом исполнения
     *
     * @param string $message Текст сообщения
     *
     * @return \XEAF\UI\Models\Results\FormResult
     */
    public static function success(string $message = ''): self {
        return new self(HttpStatusCodes::OK, self::ALERT_DANGER, $message);
    }

    /**
     * Создает объект ошибки в данных запроса
     *
     * @param string $message Текст сообщения
     *
     * @return \XEAF\UI\Models\Results\FormResult
     */
    public static function badRequest(string $message = ''): self {
        return new self(HttpStatusCodes::BAD_REQUEST, self::ALERT_DANGER, $message);
    }

    /**
     * Создает объект ошибки неавторизованного пользователя
     *
     * @param string $message Текст сообщения
     *
     * @return \XEAF\UI\Models\Results\FormResult
     */
    public static function unauthorized(string $message = ''): self {
        return new self(HttpStatusCodes::UNAUTHORIZED, self::ALERT_DANGER, $message);
    }

    /**
     * Создает объект ошибки прав доступа к ресурсу
     *
     * @param string $message Текст сообщения
     *
     * @return \XEAF\UI\Models\Results\FormResult
     */
    public static function forbidden(string $message = ''): self {
        return new self(HttpStatusCodes::FORBIDDEN, self::ALERT_DANGER, $message);
    }

    /**
     * Создает объект ошибки обращения к неизвестному ресурсу
     *
     * @param string $message Текст сообщения
     *
     * @return \XEAF\UI\Models\Results\FormResult
     */
    public static function notFound(string $message = ''): self {
        return new self(HttpStatusCodes::NOT_FOUND, self::ALERT_DANGER, $message);
    }

    /**
     * Создает объект фатальной ошибки сервера
     *
     * @param string $message Текст сообщения
     *
     * @return \XEAF\UI\Models\Results\FormResult
     */
    public static function fatalError(string $message = ''): self {
        return new self(HttpStatusCodes::FATAL_ERROR, self::ALERT_DANGER, $message);
    }

    /**
     * Создает новый объект на основе ErrorResult
     *
     * @param \XEAF\API\Models\Results\ErrorResult $errorResult Объект ошибки
     *
     * @return \XEAF\UI\Models\Results\FormResult
     */
    public static function cloneFromError(ErrorResult $errorResult): self {
        $result = new self($errorResult->statusCode, self::ALERT_SUCCESS, $errorResult->message);
        if ($errorResult->statusCode != HttpStatusCodes::OK) {
            $result->alert = self::ALERT_DANGER;
            foreach ($errorResult->objectErrors as $id => $message) {
                $result->addObjectError($id, $message);
            }
        }
        return $result;
    }
}
