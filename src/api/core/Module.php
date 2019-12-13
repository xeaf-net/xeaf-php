<?php

/**
 * Module.php
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
use XEAF\API\Models\Results\DataResult;
use XEAF\API\Models\Results\ErrorResult;
use XEAF\API\Models\Results\RedirectResult;
use XEAF\API\Utils\HttpStatusCodes;
use XEAF\API\Utils\Language;
use XEAF\API\Utils\Parameters;
use XEAF\API\Utils\Session;
use XEAF\API\Utils\Strings;

/**
 * Реализует методы работы модуля проекта
 *
 * @package  XEAF\API\Core
 */
abstract class Module extends Extension {

    /**
     * Префикс метода исполнения действия модуля
     */
    private const ACTION_METHOD_PREFIX = 'process';

    /**
     * Суффикс метода исполнения действия модуля
     */
    private const ACTION_METHOD_SUFFIX = 'API';

    /**
     * Исполняет действие модуля
     *
     * @return \XEAF\API\Core\ActionResult|null
     */
    public function execute(): ?ActionResult {
        $method = $this->actionModeMethod($this->prm->actionMode, $this->prm->methodName);
        if ($method) {
            $this->beforeExecute();
            $result = $this->$method();
            $this->afterExecute();
        } else {
            $result = new ErrorResult(HttpStatusCodes::NOT_FOUND);
        }
        return $result;
    }

    /**
     * Метод обработки действия для метода GET по умолчанию
     *
     * @return \XEAF\API\Core\ActionResult|null
     */
    protected function processGetDefault(): ?ActionResult {
        return new ErrorResult(HttpStatusCodes::NOT_FOUND);
    }

    /**
     * Метод обработки действия для метода POST по умолчанию
     *
     * @return \XEAF\API\Core\ActionResult|null
     */
    protected function processPostDefault(): ?ActionResult {
        return new ErrorResult(HttpStatusCodes::NOT_FOUND);
    }

    /**
     * Метод обработки действия для метода DELETE по умолчанию
     *
     * @return \XEAF\API\Core\ActionResult|null
     */
    protected function processDeleteDefault(): ?ActionResult {
        return new ErrorResult(HttpStatusCodes::FORBIDDEN);
    }

    /**
     * Возвращает значения языкоых переменных
     *
     * @return \XEAF\API\Core\ActionResult|null
     */
    public function processGetLocale(): ?ActionResult {
        $data = Language::getLanguageVars();
        foreach ($data as $name => $value) {
            if (Strings::startsWith($name, 'i18n.')) {
                unset($data[$name]);
            }
        }
        return new DataResult(new DataObject($data), HttpStatusCodes::OK, true);
    }

    /**
     * Выполняется перед исполнением метода обработки действия
     *
     * @return void
     */
    protected function beforeExecute(): void {
    }

    /**
     * Выполняется перед исполнением метода обработки действия
     *
     * @return void
     */
    protected function afterExecute(): void {
    }

    /**
     * Возвращает идентификатор метода для исполнения режима действия
     *
     * @param string $actionMode Идентификатор режима действия
     * @param string $methodName Идентификатор метода
     *
     * @return string|null
     */
    protected function actionModeMethod(string $actionMode, string $methodName = Parameters::GET_METHOD_NAME): ?string {
        $mode   = ($actionMode) ? $actionMode : Parameters::DEFAULT_ACTION_MODE;
        $result = self::ACTION_METHOD_PREFIX . ucfirst(strtolower($methodName)) . ucfirst($mode);
        if ($this->apiMode()) {
            $apiMethod = $result . self::ACTION_METHOD_SUFFIX;
            if (method_exists($this, $apiMethod)) {
                return $apiMethod;
            }
        }
        return method_exists($this, $result) ? $result : null;
    }

    /**
     * Возвращает признак режима API
     *
     * @return bool
     */
    protected function apiMode(): bool {
        return !Session::isNative();
    }

    /**
     * Возвращает код ошибки UNAUTHORIZED, если пользователь не авторизован
     *
     * @return \XEAF\API\Core\ActionResult|null
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    protected function checkUserAuthorized(): ?ActionResult {
        if (!Session::authorized()) {
            return new ErrorResult(HttpStatusCodes::UNAUTHORIZED);
        }
        return null;
    }

    /**
     * Перенаправляет на заданный URL, если пользователь не авторизован
     *
     * @param string|null $url URL для переадресации
     *
     * @return \XEAF\API\Core\ActionResult|null
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    protected function redirectIfNotAuthorized(string $url = null): ?ActionResult {
        if (!Session::authorized()) {
            if ($url) {
                return new RedirectResult($url);
            }
            return $this->redirectToLogin();
        }
        return null;
    }

    /**
     * Возвращает перенаправление на домашнюю страницу
     *
     * @return \XEAF\API\Core\ActionResult
     */
    protected function redirectToHome(): ActionResult {
        $url = $this->cfg->portal->url;
        return new RedirectResult($url);
    }

    /**
     * Возвращает перенаправление на страницу логина
     *
     * @return \XEAF\API\Core\ActionResult
     */
    protected function redirectToLogin(): ActionResult {
        $url = $this->cfg->portal->url . '/' . Router::LOGIN_MODULE;
        return new RedirectResult($url);
    }
}
