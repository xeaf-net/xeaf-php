<?php

/**
 * SessionModule.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Modules\Tools;

use XEAF\API\Core\ActionResult;
use XEAF\API\Core\Module;
use XEAF\API\Models\Results\RedirectResult;
use XEAF\API\Utils\Language;
use XEAF\API\Utils\Session;

/**
 * Контроллер модуля изменения параметров сессии пользователя
 *
 * @package  XEAF\UI\Modules\Tools
 */
class SessionModule extends Module {

    /**
     * Идентификатор модуля
     */
    public const SESSION_MODULE = 'session';

    /**
     * Задает язык сессии
     *
     * @return \XEAF\API\Core\ActionResult|null
     * @throws \XEAF\API\Utils\Exceptions\SessionException
     */
    public function processGetLanguage(): ?ActionResult {
        $language = $this->prm->getActionArg('lang', Language::DEFAULT_LANGUAGE);
        $returnTo = $this->prm->getActionArg('returnTo', $this->cfg->portal->url);
        Session::setLanguage($language);
        return new RedirectResult($returnTo);
    }
}
