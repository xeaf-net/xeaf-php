<?php

/**
 * RestApiProvider.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Core;

use XEAF\API\Utils\HttpStatusCodes;
use XEAF\API\Utils\Serializer;

/**
 * Провайдер для доступа к внешним сервисам
 *
 * @package XEAF\API\Core
 */
abstract class RestApiProvider {

    /**
     * Заголовки
     * @var array
     */
    private $_headers = [];

    /**
     * Код статуса последнего обращения к API
     * @var int
     */
    private $_lastStatusCode = HttpStatusCodes::OK;

    /**
     * Конструктор класса
     */
    public function __construct() {
        $this->_headers = $this->defaultHeaders();
    }

    /**
     * Возвращает набор заголовков по умолчанию
     *
     * @return array
     */
    protected function defaultHeaders(): array {
        return [];
    }

    /**
     * Обращается к API по методу GET и возвращает JSON
     *
     * @param string $url  URL стороннего API
     * @param array  $args Массив параметров
     *
     * @return string
     */
    protected function get(string $url, array $args = []): string {
        $api    = curl_init();
        $apiURL = $this->buildURL($url, $args);
        $header = $this->_headers;
        curl_setopt_array($api, [
            CURLOPT_URL            => $apiURL,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true
        ]);
        $result                = curl_exec($api);
        $this->_lastStatusCode = $this->statusCode($api);
        curl_close($api);
        return $result;
    }

    /**
     * Обращается к API по методу POST и возвращает JSON
     *
     * @param string $url      URL стороннего API
     * @param array  $args     Агрументы пути
     * @param array  $postArgs Агрументы метода POST
     *
     * @return string
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    protected function post(string $url, array $args = [], array $postArgs = []): string {
        $api      = curl_init();
        $apiURL   = $this->buildURL($url, $args);
        $json     = Serializer::jsonArrayEncode($postArgs);
        $header   = $this->_headers;
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: ' . strlen($json);
        curl_setopt_array($api, [
            CURLOPT_URL            => $apiURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $json
        ]);
        $result                = curl_exec($api);
        $this->_lastStatusCode = $this->statusCode($api);
        curl_close($api);
        return $result;
    }

    /**
     * Обращается к API по методу DELETE и возвращает JSON
     *
     * @param string $url      URL стороннего API
     * @param array  $args     Агрументы пути
     * @param array  $postArgs Агрументы метода POST
     *
     * @return string
     * @throws \XEAF\API\Utils\Exceptions\SerializerException
     */
    protected function delete(string $url, array $args = [], array $postArgs = []): string {
        $api      = curl_init();
        $apiURL   = $this->buildURL($url, $args);
        $json     = Serializer::jsonArrayEncode($postArgs);
        $header   = $this->_headers;
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: ' . strlen($json);
        curl_setopt_array($api, [
            CURLOPT_URL            => $apiURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
            CURLOPT_POSTFIELDS     => $json
        ]);
        $result                = curl_exec($api);
        $this->_lastStatusCode = $this->statusCode($api);
        curl_close($api);
        return $result;
    }

    /**
     * Возвращает код статуса последнего обращения к API
     *
     * @return int
     */
    public function getLastStatusCode(): int {
        return $this->_lastStatusCode;
    }

    /**
     * Возвращает признак состояния ошибки при обращении к стороннему API
     *
     * @return bool
     */
    public function getErrorState(): bool {
        return $this->getLastStatusCode() != HttpStatusCodes::OK;
    }

    /**
     * Добавляет относительный путь к URL API портала
     *
     * @param string $url  URL стороннего API
     * @param array  $args Массив параметров
     *
     * @return string
     */
    protected function buildURL(string $url, array $args = []): string {
        $result = rtrim($url, '/');
        if ($args) {
            $result .= '?' . http_build_query($args);
        }
        return $result;
    }

    /**
     * Пеобразует полученный код состояния
     *
     * @param resource|null $api Ресурс подключения к API
     *
     * @return int
     */
    protected function statusCode($api = null): int {
        $result = HttpStatusCodes::OK;
        if ($api) {
            $code   = curl_getinfo($api, CURLINFO_HTTP_CODE);
            $result = ($code) ? intval($code) : HttpStatusCodes::OK;
            if ($code == 0) {
                $result = HttpStatusCodes::OK;
            }
        }
        return $result;
    }
}
