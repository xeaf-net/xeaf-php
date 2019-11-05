<?php

/**
 * Parameters.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils;

use XEAF\API\App\Factory;
use XEAF\API\Core\ActionArgs;

/**
 * Реализует методы обработки параметров вызова приложения
 *
 * @package  XEAF\API\Utils
 */
class Parameters extends ActionArgs {

    /**
     * Идентификатор параметра сессии
     */
    public const SESSION_ID_NAME = 'X-Session';

    /**
     * Идентификатор параметра пути
     */
    private const PATH_PARAMETER_NAME = 'path';

    /**
     * Конструктор класса
     */
    public function __construct() {
        parent::__construct();
        $this->_methodName = $_SERVER['REQUEST_METHOD'];
        switch ($this->_methodName) {
            case self::GET_METHOD_NAME:
                $this->processRequestOrigin();
                $this->processRequestHeaders();
                $this->processRequestParameters($_GET);
                break;
            case self::POST_METHOD_NAME:
                $this->processRequestOrigin();
                $this->processRequestHeaders();
                $this->processRequestParameters($_GET);
                $this->processRequestParameters($_POST);
                $this->processInputJsonStream();
                $this->processRequestFiles();
                break;
            case self::DELETE_METHOD_NAME:
                $this->processRequestOrigin();
                $this->processRequestHeaders();
                $this->processRequestParameters($_GET);
                $this->processRequestParameters($_POST);
                $this->processInputJsonStream();
                break;
            case self::OPTIONS_METHOD_NAME:
                $this->processRequestOrigin();
                $this->processOptionsHeaders();
                die(); // Не обрабатываем,
                break; // но и не ошибка
            default:
                if (defined('STDIN')) {
                    $this->_methodName = ActionArgs::COMMAND_LINE_METHOD_NAME;
                } else {
                    Logger::fatalError('Invalid HTTP Method, ' . $this->_methodName . '.');
                }
                break;
        }
        $this->postProcessParameters();
    }

    /**
     * Устанавливает необходимые заголовки
     *
     * @return void
     */
    protected function processRequestOrigin(): void {
        $config = Factory::getConfiguration()->portal;
        if ($config->origin == '*' || $_SERVER['HTTP_ORIGIN'] == $config->origin) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: ' . DateTime::SECONDS_PER_HOUR);
            }
        }
    }

    /**
     * Разбирает параметер пути
     *
     * @param string $path Путь
     *
     * @return void
     */
    protected function processPathParameter(string $path): void {
        $arr = explode('/', $path);
        switch (count($arr)) {
            case 1:
                $this->_actionName = $arr[0];
                break;
            case 2:
                $this->_actionName = $arr[0];
                if (Strings::isObjectId($arr[1])) {
                    $this->_actionObjectId = $arr[1];
                } else {
                    $this->_actionMode = $arr[1];
                }
                break;
            case 3:
                $this->_actionName = $arr[0];
                $this->_actionMode = $arr[1];
                if (Strings::isObjectId($arr[2])) {
                    $this->_actionObjectId = $arr[2];
                } else {
                    $this->_actionPath = $arr[2];
                }
                break;
            default:
                $this->_actionName = $arr[0];
                $this->_actionMode = $arr[1];
                unset($arr[0]);
                unset($arr[1]);
                $this->_actionPath = implode('/', $arr);
                break;
        }
    }

    /**
     * Обрабатывает параметр идентификатора сессии
     *
     * @param string $value Значение параметра
     *
     * @return void
     */
    protected function processSessionParameter(string $value): void {
        $this->_sessionId = $value;
    }

    /**
     * Разбирает заголовки запроса
     *
     * @return void
     */
    protected function processRequestHeaders(): void {
        /** @noinspection PhpComposerExtensionStubsInspection */
        $headers          = getallheaders();
        $this->_sessionId = $headers[self::SESSION_ID_NAME] ?? '';
    }

    /**
     * Разбирает параметры вызова приложения
     *
     * @param array $parameters
     *
     * @return void
     */
    protected function processRequestParameters(array $parameters): void {
        $lowerSessionId = strtolower(self::SESSION_ID_NAME);
        foreach ($parameters as $name => $value) {
            if ($name == self::PATH_PARAMETER_NAME) {
                $this->processPathParameter($value);
            } else if ($name == $lowerSessionId) {
                $this->processSessionParameter($value);
            } else {
                $this->_actionArgs[$name] = $value;
            }
        }
    }

    /**
     * Разбирает данные из входного JSON потока
     *
     * @return void
     */
    protected function processInputJsonStream(): void {
        $jsonData = file_get_contents('php://input');
        if ($jsonData) {
            $params = json_decode($jsonData, true);
            if (is_array($params)) {
                foreach ($params as $name => $value) {
                    $this->_actionArgs[$name] = $value;
                }
            }
        }
    }

    /**
     * Обрабатывает заголовки метода OPTIONS
     *
     * @return void
     */
    protected function processOptionsHeaders(): void {
        if ($this->methodName == self::OPTIONS_METHOD_NAME) {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
            }
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
        }
    }

    /**
     * Разбирает данные о переданных файлах
     *
     * @return void
     */
    protected function processRequestFiles(): void {
        foreach ($_FILES as $name => $file) {
            if ($file['name']) {
                $this->_actionArgs[$name] = $file;
            }
        }
    }

    /**
     * Дополнительная обработка парамеров
     *
     * @return void
     */
    protected function postProcessParameters(): void {
        if (Strings::isEmpty($this->_sessionId)) {
            $this->_sessionId = Crypto::generateUUIDv4();
        }
        foreach ($this->_actionArgs as $name => $value) {
            if (!is_array($value)) {
                $this->_actionArgs[$name] = trim($value);
            }
        }
    }
}
