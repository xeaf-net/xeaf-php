<?php

/**
 * Application.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\App;

use Throwable;
use XEAF\API\Core\ActionArgs;
use XEAF\API\Core\ActionResult;
use XEAF\API\Core\Module;
use XEAF\API\Core\StdObject;
use XEAF\API\Models\Results\DataResult;
use XEAF\API\Models\Results\ErrorResult;
use XEAF\API\Models\Results\FileResult;
use XEAF\API\Models\Results\ListResult;
use XEAF\API\Models\Results\RedirectResult;
use XEAF\API\Models\Results\TableResult;
use XEAF\API\Modules\HomeModule;
use XEAF\API\Utils\DateTime;
use XEAF\API\Utils\HttpStatusCodes;
use XEAF\API\Utils\Language;
use XEAF\API\Utils\Logger;
use XEAF\API\Utils\Serializer;
use XEAF\API\Utils\Session;
use XEAF\API\Utils\Strings;

/**
 * Класс приложения проекта
 *
 * @package  XEAF\API\App
 */
class Application extends StdObject {

    /**
     * Время запуска приложения
     */
    private static $_startTime = 0;

    /**
     * Инициализирует значения свойств объекта класса
     *
     * @param \XEAF\API\App\Configuration $configuration Параметры конфигурации
     * @param \XEAF\API\Core\ActionArgs   $parameters    Параметры вызова
     */
    public function __construct(Configuration $configuration = null, ActionArgs $parameters = null) {
        if ($configuration != null) {
            Factory::setConfiguration($configuration);
        }
        if ($parameters != null) {
            Factory::setParameters($parameters);
        }
        self::$_startTime = microtime(true);
    }

    /**
     * Объявляет используемые модули
     *
     * @return array
     */
    protected function declareModules(): array {
        return [
            Router::HOME_MODULE => HomeModule::class
        ];
    }

    /**
     * Определяет набор расширений приложения
     *
     * @return void
     */
    protected function defineExtensions(): void {
        Router::registerModules($this->declareModules());
    }

    /**
     * Метод обработки события начала обработки действия
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    protected function beforeExecute(): void {
        Session::openSession();
        $this->defineExtensions();
        $this->initLanguageResources();
        $this->initSessionResources();
    }

    /**
     * Метод обработки события завершения обработки действия
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    protected function afterExecute(): void {
        Session::closeSession();
    }

    /**
     * Инициализирует языковые ресурсы
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    protected function initLanguageResources(): void {
        $language = Session::getLanguage();
        Language::setLanguage($language);
        $className = $this->className;
        while ($className != StdObject::class) {
            Language::loadClassLanguageFile($className);
            $className = get_parent_class($className);
        }
    }

    /**
     * Инициализирует ресурсы сессии
     *
     * @return void
     */
    protected function initSessionResources(): void {
    }

    /**
     * Создает объект исполняемого модуля
     *
     * @return \XEAF\API\Core\Module|null
     */
    protected function createModule(): ?Module {
        $result    = null;
        $className = $this->moduleClassName();
        if ($className) {
            $result = new $className();
        }
        return $result;
    }

    /**
     * Возвращает имя класса модуля
     *
     * @return string|null
     */
    protected function moduleClassName(): ?string {
        $moduleName = Factory::getParameters()->actionName;
        return Router::moduleClassName($moduleName ? $moduleName : Router::HOME_MODULE);
    }

    /**
     * Метод исполнения действия приложения
     *
     * @return \XEAF\API\Core\ActionResult|null
     */
    protected function execute(): ?ActionResult {
        try {
            $module = $this->createModule();
            $result = ($module) ? $module->execute() : new ErrorResult(HttpStatusCodes::NOT_FOUND);
        } catch (Throwable $error) {
            Logger::error($error->getMessage(), $error);
            $result = new ErrorResult(HttpStatusCodes::FATAL_ERROR);
        }
        return $result;
    }

    /**
     * Метод обработки результата исполнения действия
     *
     * @param \XEAF\API\Core\ActionResult $result Результат исполнения действия
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    protected function processResult(ActionResult $result): void {
        http_response_code($result->statusCode);
        switch ($result->resultType) {
            case ActionResult::DATA:
                $this->processDataResult($result);
                break;
            case ActionResult::LIST:
                $this->processListResult($result);
                break;
            case ActionResult::TABLE:
                $this->processTableResult($result);
                break;
            case ActionResult::FILE:
                $this->processFileResult($result);
                break;
            case ActionResult::REDIRECT:
                $this->processRedirectResult($result);
                break;
            case ActionResult::ERROR:
                $this->processErrorResult($result);
                break;
        }
    }

    /**
     * Обрабатывает действие отправки объекта данных
     *
     * @param \XEAF\API\Core\ActionResult $result Результат исполнения действия
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    protected function processDataResult(ActionResult $result): void {
        assert($result instanceof DataResult);
        header("Content-type: application/json; charset=utf-8");
        if ($result->cached) {
            $cacheSecs = DateTime::SECONDS_PER_HOUR;
            $cacheTime = DateTime::dateTimeToCache(time() + $cacheSecs);
            header("Expires: $cacheTime");
            header("Pragma: cache");
            header("Cache-Control: max-age=$cacheSecs");
        }
        print Serializer::jsonDataObjectEncode($result->dataObject);
    }

    /**
     * Обрабатывает действие отправки списка объектов данных
     *
     * @param \XEAF\API\Core\ActionResult $result Результат исполнения действия
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    protected function processListResult(ActionResult $result): void {
        assert($result instanceof ListResult);
        header("Content-type: application/json; charset=utf-8");
        print Serializer::jsonDataObjectListEncode($result->list);
    }

    /**
     * Обрабатывает действие отправки табличных данных
     *
     * @param \XEAF\API\Core\ActionResult $result Результат исполнения действия
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    protected function processTableResult(ActionResult $result): void {
        assert($result instanceof TableResult);
        header("Content-type: application/json; charset=utf-8");
        $data = [
            'data'            => $result->list->toArray(),
            'recordsTotal'    => $result->recordsTotal,
            'recordsFiltered' => $result->recordsFiltered
        ];
        print Serializer::jsonArrayEncode($data);
    }

    /**
     * Обрабатывает действие отправки файла
     *
     * @param \XEAF\API\Core\ActionResult $result Результат исполнения действия
     *
     * @return void
     */
    protected function processFileResult(ActionResult $result): void {
        assert($result instanceof FileResult);
        if ($result->mimeType) {
            header('Content-Type: ' . $result->mimeType);
        }
        if ($result->attachment) {
            header('Content-Disposition: attachment;filename=' . $result->fileName);
        } else {
            $cacheSecs = DateTime::SECONDS_PER_HOUR;
            $cacheTime = DateTime::dateTimeToCache(time() + $cacheSecs);
            header("Expires: $cacheTime");
            header("Pragma: cache");
            header("Cache-Control: max-age=$cacheSecs");
        }
        readfile($result->filePath);
        if ($result->delete && file_exists($result->filePath)) {
            unlink($result->filePath);
        }
    }

    /**
     * Обрабатывает действие перенаправления
     *
     * @param \XEAF\API\Core\ActionResult $actionResult Результат исполнения действия
     *
     * @return void
     */
    protected function processRedirectResult(ActionResult $actionResult): void {
        assert($actionResult instanceof RedirectResult);
        header('Location: ' . $actionResult->redirectURL);
    }

    /**
     * Обрабатывает действие ошибочного вызова
     *
     * @param \XEAF\API\Core\ActionResult $result Результат исполнения действия
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    protected function processErrorResult(ActionResult $result): void {
        assert($result instanceof ErrorResult);
        if (!Strings::isEmpty($result->message) || $result->objectErrors) {
            header("Content-type: application/json; charset=utf-8");
            print Serializer::jsonDataObjectEncode($result);
        }
    }

    /**
     * Точка входа
     *
     * @return void
     */
    public function run(): void {
        try {
            $this->beforeExecute();
            $result = $this->execute();
            $this->afterExecute();
            if ($result) {
                $this->processResult($result);
            }
        } catch (Throwable $reason) {
            $errorMsg = HttpStatusCodes::MESSAGES[HttpStatusCodes::FATAL_ERROR];
            Logger::error($errorMsg, $reason);
            Logger::fatalError($errorMsg, $reason);
        }
    }

    /**
     * Возвращает время запуска приложения
     *
     * @return float
     */
    public static function startTime(): float {
        return self::$_startTime;
    }
}
