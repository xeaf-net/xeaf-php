<?php

/**
 * ActionArgs.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Core;

use XEAF\API\App\Router;

/**
 * Содержит аргуметы вызова действия
 *
 * @property string $sessionId      Идентификатор сессии
 * @property string $methodName     Идентификатор метода
 * @property string $actionName     Идентификатор действия
 * @property string $actionMode     Идентификатор режима вызова действия
 * @property string $actionObjectId Идентификатор объекта действия
 * @property string $actionPath     Дополнительный путь действия
 * @property array  $actionArgs     Дополнительные параметры вызова действия
 *
 * @package  XEAF\API\Core
 */
abstract class ActionArgs extends DataModel {

    /**
     * Идентификатор HTTP метода GET
     */
    public const GET_METHOD_NAME = 'GET';

    /**
     * Идентификатор HTTP метода POST
     */
    public const POST_METHOD_NAME = 'POST';

    /**
     * Идентификатор HTTP метода DELETE
     */
    public const DELETE_METHOD_NAME = 'DELETE';

    /**
     * Идентификатор HTTP метода OPTIONS
     */
    public const OPTIONS_METHOD_NAME = 'OPTIONS';

    /**
     * Идентификатор действия по умолчанию
     */
    public const DEFAULT_ACTION_NAME = Router::HOME_MODULE;

    /**
     * Идентификатор режима исполнения действия по умолчанию
     */
    public const DEFAULT_ACTION_MODE = 'default';

    /**
     * Идентификатор сессии
     * @var string
     */
    protected $_sessionId = '';

    /**
     * Идентификатор метода
     * @var string
     */
    protected $_methodName = self::GET_METHOD_NAME;

    /**
     * Идентификатор действия
     * @var string
     */
    protected $_actionName = self::DEFAULT_ACTION_NAME;

    /**
     * Режим вызова действия
     * @var string
     */
    protected $_actionMode = self::DEFAULT_ACTION_MODE;

    /**
     * Идентификатор объекта действия
     * @var string
     */
    protected $_actionObjectId = '';

    /**
     * Дополнительный путь действия
     * @var string
     */
    protected $_actionPath = '';

    /**
     * Дополнительные параметры вызова действия
     * @var array
     */
    protected $_actionArgs = [];

    /**
     * URL текущей страницы
     * @var string
     */
    protected $_actualURL = '';

    /**
     * Возвращает идентификатор сессии
     *
     * @return string
     */
    public function getSessionId(): string {
        return $this->_sessionId;
    }

    /**
     * Задает идентификатор сессии
     *
     * @param string $sessionId Идентификатор сессии
     *
     * @return void
     */
    public function setSessionId(string $sessionId): void {
        $this->_sessionId = $sessionId;
    }

    /**
     * Возвращает идентификатор метода
     *
     * @return string
     */
    public function getMethodName(): string {
        return $this->_methodName;
    }

    /**
     * Задает идентификатор метода
     *
     * @param string $methodName Идентификатор метода
     *
     * @return void
     */
    public function setMethodName(string $methodName): void {
        $this->_methodName = $methodName;
    }

    /**
     * Возвращает идентификатор действия
     *
     * @return string
     */
    public function getActionName(): string {
        if (!$this->_actionName) {
            return Router::HOME_MODULE;
        }
        return $this->_actionName;
    }

    /**
     * Задает идентификатор действия
     *
     * @param string $actionName Идентификатор действия
     *
     * @return void
     */
    public function setActionName(string $actionName): void {
        $this->_actionName = $actionName;
    }

    /**
     * Возвращает режим вызова действия
     *
     * @return string
     */
    public function getActionMode(): string {
        return $this->_actionMode;
    }

    /**
     * Задает редим вызова действия
     *
     * @param string $actionMode Режим вызова действия
     *
     * @return void
     */
    public function setActionMode(string $actionMode): void {
        $this->_actionMode = $actionMode;
    }

    /**
     * Возвращает идентификатор объекта действия
     *
     * @return string
     */
    public function getActionObjectId(): string {
        return $this->_actionObjectId;
    }

    /**
     * Задает идентификатор объекта действия
     *
     * @param string $actionObjectId Идентификатор объекта действия
     *
     * @return void
     */
    public function setActionObjectId(string $actionObjectId): void {
        $this->_actionObjectId = $actionObjectId;
    }

    /**
     * Возвращает дополнительный путь действия
     *
     * @return string
     */
    public function getActionPath(): string {
        return $this->_actionPath;
    }

    /**
     * Задает дополнительный путь действия
     *
     * @param string $actionPath Дополнительный путь
     *
     * @return void
     */
    public function setActionPath(string $actionPath): void {
        $this->_actionPath = $actionPath;
    }

    /**
     * Возвращает дополниительные параметры вызова действия
     *
     * @return array
     */
    public function getActionArgs(): array {
        return $this->_actionArgs;
    }

    /**
     * Задает дополнительные параметры вызова действия
     *
     * @param array $actionArgs Массив значений параметров
     *
     * @return void
     */
    public function setActionArgs(array $actionArgs): void {
        $this->_actionArgs = $actionArgs;
    }

    /**
     * Возвращает параметр вызова действия
     *
     * @param string     $name         Имя параметра
     * @param mixed|null $defaultValue Значение по умолчанию
     *
     * @return mixed
     */
    public function getActionArg(string $name, $defaultValue = null) {
        $result = $defaultValue;
        if (array_key_exists($name, $this->_actionArgs)) {
            if ($this->_actionArgs[$name]) {
                $result = $this->_actionArgs[$name];
            }
        }
        return $result;
    }

    /**
     * Задает параметр вызова действия
     *
     * @param string $name  Идентификатор параметра
     * @param null   $value Значение параметра
     *
     * @return void
     */
    public function setActionArg(string $name, $value = null): void {
        $this->_actionArgs[$name] = $value;
    }
}
