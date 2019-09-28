<?php

/**
 * RedirectResult.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-API
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\API\Models\Results;

use XEAF\API\Core\ActionResult;
use XEAF\API\Utils\HttpStatusCodes;

/**
 * Содержит данные для переадресации
 *
 * @property string redirectURL URL для переадресации
 *
 * @package  XEAF\API\Models\Results
 */
class RedirectResult extends ActionResult {

    /**
     * URL для переадресации
     * @var string
     */
    private $_redirectURL = '';

    /**
     * Конструктор класса
     *
     * @param string $redirectURL URL для переадресации
     * @param int    $statusCode  Код статуса ответа
     */
    public function __construct(string $redirectURL, int $statusCode = HttpStatusCodes::MOVED_PERMANENTLY) {
        parent::__construct(ActionResult::REDIRECT, $statusCode);
        $this->_redirectURL = $redirectURL;
    }

    /**
     * Возвращает URL для переадресации
     *
     * @return string
     */
    public function getRedirectURL(): string {
        return $this->_redirectURL;
    }

    /**
     * Задает URL для переадресации
     *
     * @param string $value URL для переадресации
     *
     * @return void
     */
    public function setRedirectURL(string $value): void {
        $this->_redirectURL = $value;
    }
}
