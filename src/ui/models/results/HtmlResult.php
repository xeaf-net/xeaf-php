<?php

/**
 * HtmlResult.php
 *
 * Файл является неотъемлемой частью проекта XEAF-PHP-UI
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @license   Apache 2.0
 */
namespace XEAF\UI\Models\Results;

use XEAF\API\Core\DataObject;
use XEAF\UI\Core\ActionResult;

/**
 * Содержит данные для отправки фрагмента HTML кода
 *
 * @property \XEAF\API\Core\DataObject|null $dataObject Объект данных
 * @property string|null                    $layoutFile Файл шаблона построения
 *
 * @package  XEAF\UI\Models\Results
 */
class HtmlResult extends ActionResult {

    /**
     * Объект данных
     * @var \XEAF\API\Core\DataObject
     */
    private $_dataObject = null;

    /**
     * Файл шаблона построения
     * @var string
     */
    private $_layoutFile = '';

    /**
     * Конструктор класса
     *
     * @param \XEAF\API\Core\DataObject|null $dataObject Объект данных
     * @param string|null                    $layoutFile Файл шаблона построения
     */
    public function __construct(DataObject $dataObject = null, string $layoutFile = null) {
        parent::__construct(ActionResult::HTML);
        $this->_dataObject = $dataObject;
        $this->_layoutFile = $layoutFile;
    }

    /**
     * Возвращает объект данных
     *
     * @return \XEAF\API\Core\DataObject|null
     */
    public function getDataObject(): ?DataObject {
        return $this->_dataObject;
    }

    /**
     * Задает объект данных
     *
     * @param \XEAF\API\Core\DataObject $dataObject Объект данных
     *
     * @return void
     */
    public function setDataObject(DataObject $dataObject): void {
        $this->_dataObject = $dataObject;
    }

    /**
     * Возвращает файл шаблона построения
     *
     * @return string
     */
    public function getLayoutFile(): ?string {
        return $this->_layoutFile;
    }

    /**
     * Задает файл шаблона построения
     *
     * @param string $layoutFile Файл шаблона построения
     *
     * @return void
     */
    public function setLayoutFile(string $layoutFile): void {
        $this->_layoutFile = $layoutFile;
    }
}
