<?php

/**
 * ActionResult.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Core;

/**
 * Реализует общие свойства результатов исполнения действий интерфейса пользователя
 *
 * @package  XEAF\UI\Core
 */
class ActionResult extends \XEAF\API\Core\ActionResult {

    /**
     * Страница
     */
    public const PAGE = 21;

    /**
     * HTML фрагмент
     */
    public const HTML = 22;

    /**
     * Валидация формы
     */
    public const FORM = 23;
}
