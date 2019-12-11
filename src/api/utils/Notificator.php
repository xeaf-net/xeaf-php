<?php

/**
 * Notificator.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

use Throwable;
use XEAF\API\App\Factory;
use XEAF\API\Core\DataObject;
use XEAF\API\Core\RestApiProvider;
use XEAF\API\Models\Config\NotificatorConfig;

/**
 * Клиент сервера отправки нотификационных сообщений
 *
 * @package  XEAF\API\Utils
 */
class Notificator extends RestApiProvider {

    /**
     * Cookie URL службы нотификаций
     */
    public const XNS_URL = 'xns-url';

    /**
     * Cookie сессии пользователя
     */
    public const XNS_SESSION_ID = 'xns-session-id';

    /**
     * Путь действия регистрации сессии
     */
    protected const LOGIN_PATH = 'login';

    /**
     * Путь действия отмены регистрации сессии
     */
    protected const LOGOUT_PATH = 'logout';

    /**
     * Путь действия нотификации
     */
    protected const NOTIFY_PATH = 'notify';

    /**
     * Поле идентификаторов пользователей
     */
    protected const USERS_FIELD = 'users';

    /**
     * Поле типа сообщения
     */
    protected const TYPE_FIELD = 'type';

    /**
     * Поле данных сообщения
     */
    protected const DATA_FIELD = 'data';

    /**
     * Идентификатор раздела файла конфигурации
     */
    protected const CONFIG_SECTION = 'notificator';

    /**
     * URL сервера
     * @var string
     */
    protected $_serverURL = NotificatorConfig::DEFAULT_URL;

    /**
     * Ключ доступа к серверу
     * @var string|null
     */
    protected $_serverKey = null;

    /**
     * Признак разрешения отправки сообщений
     * @var bool
     */
    protected $_enabled = false;

    /**
     * Ссылка на единичных экземпляр объекта класса
     * @var \XEAF\API\Utils\Notificator
     */
    private static $_instance = null;

    /**
     * Конструктор класса
     */
    protected function __construct() {
        parent::__construct();
        $config           = $this->loadConfiguration();
        $this->_serverURL = $config->url;
        $this->_serverKey = $config->key;
        $this->_enabled   = $config->enabled;
    }

    /**
     * Возвращает признак возможности использования сервиса
     *
     * @return bool
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public function canUseService(): bool {
        return $this->_enabled && $this->_serverURL != null && $this->_serverKey != null && Session::authorized();
    }

    /**
     * Отправляет нотификационное сообщение
     *
     * @param string                         $userId     Идентификатор пользователя
     * @param string                         $type       Тип сообщения
     * @param \XEAF\API\Core\DataObject|null $dataObject Объект данных сообщения
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public function notify(string $userId, string $type, DataObject $dataObject = null): void {
        self::notifyGroup([$userId], $type, $dataObject);
    }

    /**
     * Отправляет сообщение пользователю сессии
     *
     * @param string                         $type       Тип сообщеия
     * @param \XEAF\API\Core\DataObject|null $dataObject Объект данных
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public function notifyMe(string $type, DataObject $dataObject = null): void {
        if (Session::authorized()) {
            $this->notify(Session::getUserId(), $type, $dataObject);
        }
    }

    /**
     * Отправляет сообщение всем пользователям
     *
     * @param string                         $type       Тип сообщения
     * @param \XEAF\API\Core\DataObject|null $dataObject Объект данных
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public function notifyAll(string $type, DataObject $dataObject = null): void {
        if (Session::authorized()) {
            $this->notify($this->_serverKey, $type, $dataObject);
        }
    }

    /**
     * Отправляет нотификационное сообщение группе пользователей
     *
     * @param array                     $users      Список идентификаторов пользователей
     * @param string                    $type       Тип сообщения
     * @param \XEAF\API\Core\DataObject $dataObject Объект данных сообщения
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public function notifyGroup(array $users, string $type, DataObject $dataObject = null): void {
        if ($this->canUseService()) {
            $url     = $this->_serverURL . '/' . self::NOTIFY_PATH;
            $json    = Serializer::jsonDataObjectEncode($dataObject);
            $message = [
                self::USERS_FIELD => $users,
                self::TYPE_FIELD  => $type,
                self::DATA_FIELD  => $json
            ];
            $this->post($url, ['sender' => $this->_serverKey], $message);
        }
    }

    /**
     * Регистрирует сессию пользователя
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    public function registerUserSession(): void {
        if ($this->canUseService()) {
            $url = $this->_serverURL . '/' . self::LOGIN_PATH;
            $this->post($url, [
                'sender'  => $this->_serverKey,
                'session' => Session::getSessionId(),
                'user'    => Session::getUserId()
            ]);
            self::setupNotificationCookie();
        }
    }

    /**
     * Отменяет регистрацию сессии пользователя
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public function unregisterUserSession(): void {
        if ($this->canUseService()) {
            $url = $this->_serverURL . '/' . self::LOGOUT_PATH;
            $this->post($url, [
                'sender'  => $this->_serverKey,
                'session' => Session::getSessionId()
            ]);
        }
        self::cleanupNotificationCookie();
    }

    /**
     * Устанавливает cookie для службы нотификаций
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    protected function setupNotificationCookie(): void {
        if ($this->canUseService() && Session::isNative()) {
            Cookie::put(self::XNS_URL, $this->_serverURL);
            Cookie::put(self::XNS_SESSION_ID, Session::getSessionId());
        }
    }

    /**
     * Удаляет cookie для службы нотификаций
     *
     * @return void
     */
    protected function cleanupNotificationCookie(): void {
        if (Session::isNative()) {
            Cookie::delete(self::XNS_URL);
            Cookie::delete(self::XNS_SESSION_ID);
        }
    }

    /**
     * Загружает параметры конфигурации
     *
     * @return \XEAF\API\Models\Config\NotificatorConfig
     */
    protected function loadConfiguration(): NotificatorConfig {
        $data = null;
        try {
            $config = Factory::getConfiguration();
            $data   = $config->getSection(self::CONFIG_SECTION);
        } catch (Throwable $reason) {
            Logger::fatalError($reason->getMessage(), $reason);
        }
        return new NotificatorConfig($data);
    }

    /**
     * Возвращает ссылку на единичных экземпляр объекта класса
     *
     * @return \XEAF\API\Utils\Notificator
     */
    public static function getInstance(): Notificator {
        if (self::$_instance == null) {
            self::$_instance = new Notificator();
        }
        return self::$_instance;
    }
}
