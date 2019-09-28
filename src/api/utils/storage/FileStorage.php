<?php

/**
 * FileStorage.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Utils\Storage;

use XEAF\API\App\Factory;
use XEAF\API\Core\Storage;
use XEAF\API\Models\Config\FileStorageConfig;
use XEAF\API\Utils\FileSystem;
use XEAF\API\Utils\Serializer;

/**
 * Реализует методы файлового кеширования
 *
 * @package  XEAF\API\Utils\Storage
 */
class FileStorage extends Storage {

    /**
     * Имя раздела файла конфигурации
     */
    protected const CONFIG_SECTION_NAME = 'files';

    /**
     * Расширение имени файла
     */
    protected const FILE_NAME_EXT = 'tmp';

    /**
     * Возвращает ранее сохраненное значение
     *
     * @param string     $key          Ключ
     * @param mixed|null $defaultValue Значение по умолчанию
     *
     * @return mixed
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public function get(string $key, $defaultValue = null) {
        $result = parent::get($key);
        if ($result == $defaultValue) {
            $path = $this->fileName($key);
            if (FileSystem::fileExists($path)) {
                $data   = file_get_contents($path);
                $result = Serializer::unserialize($data);
            }
        }
        return $result != null ? $result : $defaultValue;
    }

    /**
     * Сохраняет значение
     *
     * @param string     $key   Ключ
     * @param mixed|null $value Значение
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public function put(string $key, $value = null): void {
        parent::put($key, $value);
        $path = $this->fileName($key);
        $data = Serializer::serialize($value);
        file_put_contents($path, $data);
    }

    /**
     * Удаляет ранее установленное значение
     *
     * @param string $key Ключ
     *
     * @return void
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public function delete(string $key): void {
        parent::delete($key);
        FileSystem::deleteFile($this->fileName($key));
    }

    /**
     * Возвращает признак существования значения
     *
     * @param string $key Ключ
     *
     * @return bool
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    public function exists(string $key): bool {
        $result = parent::exists($key);
        if (!$result) {
            $result = FileSystem::fileExists($this->fileName($key));
        }
        return $result;
    }

    /**
     * Возвращает имя файла данных
     *
     * @param string $key Ключ
     *
     * @return string
     * @throws \XEAF\API\Utils\Exceptions\ConfigurationException
     */
    protected function fileName(string $key): string {
        $config = Factory::getConfiguration();
        $data   = $config->getNamedSection(self::CONFIG_SECTION_NAME, $this->name);
        $fc     = new FileStorageConfig($data);
        return $fileName = $fc->path . '/' . md5($this->name) . md5($key) . '.' . self::FILE_NAME_EXT;
    }
}
