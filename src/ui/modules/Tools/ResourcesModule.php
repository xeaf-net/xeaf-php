<?php

/**
 * ResourcesModule.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Modules\Tools;

use Throwable;
use XEAF\API\Core\ActionResult;
use XEAF\API\Core\Module;
use XEAF\API\Models\Results\DataResult;
use XEAF\API\Models\Results\ErrorResult;
use XEAF\API\Models\Results\FileResult;
use XEAF\API\Utils\FileSystem;
use XEAF\API\Utils\HttpStatusCodes;
use XEAF\API\Utils\Logger;
use XEAF\API\Utils\MimeTypes;
use XEAF\API\Utils\Reflection;
use XEAF\API\Utils\Serializer;
use XEAF\UI\App\Router;

/**
 * Контроллер модуля собработки ссылок на ресурсы портала
 *
 * @package  XEAF\UI\Modules\Tools
 */
class ResourcesModule extends Module {

    /**
     * Идентификатор модуля
     */
    public const MODULE_NAME = 'resources';

    /**
     * Идентификатор публичных ресурсов
     */
    public const PUBLIC_RESOURCES = 'public';

    /**
     * Идентификатор ресурсов модулей
     */
    public const MODULE_RESOURCES = 'module';

    /**
     * Идентификатор ресурсов разметок страниц
     */
    public const TEMPLATE_RESOURCES = 'template';

    /**
     * Идентификатор ресурсов сторонних поставщиков
     */
    public const VENDOR_RESOURCES = 'vendor';

    /**
     * Идентификатор ресурсов установленных при помощи NPM
     */
    public const NPM_RESOURCES = 'node_modules';

    /**
     * Исполняет действие модуля
     *
     * @return \XEAF\API\Core\ActionResult|null
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    public function execute(): ?ActionResult {
        $result = null;
        switch ($this->prm->actionName) {
            case self::MODULE_RESOURCES:
            case self::TEMPLATE_RESOURCES:
                $result = $this->processElementResources();
                break;
//            case self::TEMPLATE_RESOURCES:
//                $result = $this->processElementResources();
//                break;
            case self::PUBLIC_RESOURCES:
                $result = $this->processPublicResources();
                break;
            case self::VENDOR_RESOURCES:
                $result = $this->processVendorResources();
                break;
            case self::NPM_RESOURCES:
                $result = $this->processNpmResources();
                break;
            default:
                $result = parent::execute();
                break;
        }
        return $result;
    }

    /**
     * Обрабатывает запрос к файлам ресурсов исполняемого элемента
     *
     * @return \XEAF\API\Core\ActionResult
     * @throws \XEAF\API\Utils\Exceptions\CoreException
     */
    protected function processElementResources(): ActionResult {
        if (!$this->prm->actionPath) {
            $resourceName = FileSystem::getFileName($this->prm->actionMode);
            $resourceType = FileSystem::getFileNameExt($this->prm->actionMode);

            $arr = explode('.', $resourceName);
            if (count($arr) > 1) {
                $resourceName = $arr[0];
                $className    = $this->getElementClassName($resourceName);
                $baseName     = FileSystem::removeFileNameExt(Reflection::classFileName($className));
                $resourcePath = $baseName . '-' . ucfirst($arr[1]) . '.' . $resourceType;
            } else {
                $className    = $this->getElementClassName($resourceName);
                $resourcePath = FileSystem::changeFileNameExt(Reflection::classFileName($className), $resourceType);
            }
        } else {
            $resourceName = $this->prm->actionMode;
            $resourceType = FileSystem::getFileNameExt($this->prm->actionPath);
            $className    = $this->getElementClassName($resourceName);
            $fileName     = Reflection::classFileName($className);
            $resourcePath = FileSystem::getFileDir($fileName) . '/' . $this->prm->actionPath;
        }
        return $this->sendResourceFile($resourcePath, $resourceType);
    }

    /**
     * Обрабатывает запрос к файлам публичных ресурсов
     *
     * @return \XEAF\API\Core\ActionResult
     */
    protected function processPublicResources(): ActionResult {
        $resourceName = $this->prm->actionMode;
        $internalPath = __DIR__ . '/../../public';
        $resourcePath = rtrim($internalPath . '/' . $resourceName . '/' . $this->prm->actionPath, '/');
        if (!FileSystem::fileExists($resourcePath)) {
            $internalPath = __XEAF_VENDOR_DIR__ . '/../src/public';
            $resourcePath = rtrim($internalPath . '/' . $resourceName . '/' . $this->prm->actionPath, '/');
        }
        $resourceType = FileSystem::getFileNameExt($resourcePath);
        return $this->sendResourceFile($resourcePath, $resourceType);
    }

    /**
     * Обрабатывает запрос к файлам ресурсов сторонних поставщиков
     *
     * @return \XEAF\API\Core\ActionResult
     */
    protected function processVendorResources(): ActionResult {
        $resourceName = $this->prm->actionMode;
        $resourcePath = __XEAF_VENDOR_DIR__ . '/' . $resourceName . '/' . $this->prm->actionPath;
        $resourceType = FileSystem::getFileNameExt($resourcePath);
        return $this->sendResourceFile($resourcePath, $resourceType);
    }

    /**
     * Обрабатывает запрос к файлам ресурсов установленных при помощи NPM
     *
     * @return \XEAF\API\Core\ActionResult
     */
    protected function processNpmResources(): ActionResult {
        $resourceName = $this->prm->actionMode;
        $nodeModules  = __XEAF_VENDOR_DIR__ . '/../node_modules';
        $resourcePath = $nodeModules . '/' . $resourceName . '/' . $this->prm->actionPath;
        $resourceType = FileSystem::getFileNameExt($resourcePath);
        return $this->sendResourceFile($resourcePath, $resourceType);
    }

    /**
     * Возвращает идентификатор класса ресурса
     *
     * @param string $name Идентификатор ресурса
     *
     * @return string|null
     */
    protected function getElementClassName(string $name): ?string {
        $result = null;
        switch ($this->prm->actionName) {
            case self::MODULE_RESOURCES:
                $result = Router::moduleClassName($name);
                break;
            case self::TEMPLATE_RESOURCES:
                $result = Router::templateClassName($name);
                break;
        }
        return $result;
    }

    /**
     * Возвращает результат отправки файла ресурса
     *
     * @param string $fileName Имя файла ресурса
     * @param string $fileType Тип ресурса
     *
     * @return \XEAF\API\Core\ActionResult
     */
    protected function sendResourceFile(string $fileName, string $fileType): ActionResult {
        $result   = null;
        $mimeType = MimeTypes::getMimeType($fileType);
        if ($mimeType != MimeTypes::DEFAULT_MIME_TYPE) {
            if (file_exists($fileName)) {
                if ($fileType != 'lang') {
                    $result = new FileResult($fileName, false);
                } else {
                    try {
                        $data   = Serializer::jsonDecodeFile($fileName, true);
                        $result = DataResult::fromArray($data);
                    } catch (Throwable $e) {
                        Logger::error($e->getMessage(), $e);
                    }
                }
            }
        }
        if (!$result) {
            $result = new ErrorResult(HttpStatusCodes::NOT_FOUND);
        }
        return $result;
    }
}
