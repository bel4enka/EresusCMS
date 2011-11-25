<?php
/**
 * ${product.title}
 *
 * Построитель адресов с аргументами в запросе (query)
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package Eresus
 *
 * $Id: Kernel.php 1978 2011-11-22 14:49:17Z mk $
 */


/**
 * Построитель адресов с аргументами в запросе (query)
 *
 * Например, шаблон переключателя страниц может выглядеть так:
 *
 * <code>
 * …/admin.php?mod=ext-myplugin&page=%d
 * </code>
 *
 * Корневой URL может быть задан в {@link __construct конструкторе}.
 *
 * @package UI
 */
class Eresus_UI_List_URL_Query implements Eresus_UI_List_URL_Interface
{
	/**
	 * Базовый URL
	 *
	 * Всегда заканчивается символом & или ?
	 *
	 * @var string
	 * @see __construct()
	 */
	private $baseURL;

	/**
	 * Имя аргумента для передачи идентификатора
	 *
	 * @var string
	 * @see setIdName()
	 */
	private $idName = 'id';

	/**
	 * Конструктор
	 *
	 * @param string $baseURL  базовый URL, все аргументы будет присоединяться к нему. Если не указан,
	 *                         будет использован результат вызова {@link WebPage::url()}.
	 *
	 * @return Eresus_UI_List_URL_Query
	 *
	 * @since 2.17
	 * @uses WebPage::url()
	 */
	public function __construct($baseURL = null)
	{
		if ($baseURL)
		{
			$this->baseURL = $baseURL;
		}
		else
		{
			$this->baseURL = $GLOBALS['page']->url();
		}
		$lastChar = mb_substr($this->baseURL, -1);
		if ('&' != $lastChar && '?' != $lastChar)
		{
			$hasQuery = mb_strpos($this->baseURL, '?') !== false;
			$this->baseURL .= $hasQuery ? '&' : '?';
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Задаёт имя аргумента для передачи идентификатора элмента списка
	 *
	 * По умолчанию имя аргумента «id».
	 *
	 * @param string $name
	 *
	 * @return void
	 *
	 * @since 1,.00
	 */
	public function setIdName($name)
	{
		$this->idName = $name;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает шаблон URL для переключателя страниц
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getPagination()
	{
		return $this->baseURL . 'page=%d';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает URL для ЭУ «Удалить»
	 *
	 * @param Eresus_UI_List_Item_Interface $item
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getDelete(Eresus_UI_List_Item_Interface $item)
	{
		return $this->baseURL . $this->idName . '=' . $item->getId() . '&action=delete';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает URL для ЭУ «Изменить»
	 *
	 * @param Eresus_UI_List_Item_Interface $item
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getEdit(Eresus_UI_List_Item_Interface $item)
	{
		return $this->baseURL . $this->idName . '=' . $item->getId() . '&action=edit';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает шаблон URL для ЭУ «Поднять выше»
	 *
	 * @param Eresus_UI_List_Item_Interface $item
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getOrderingUp(Eresus_UI_List_Item_Interface $item)
	{
		return $this->baseURL . $this->idName . '=' . $item->getId() . '&action=up';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает шаблон URL для ЭУ «Опустить ниже»
	 *
	 * @param Eresus_UI_List_Item_Interface $item
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getOrderingDown(Eresus_UI_List_Item_Interface $item)
	{
		return $this->baseURL . $this->idName . '=' . $item->getId() . '&action=down';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает URL для ЭУ «Включить/Отключить»
	 *
	 * @param Eresus_UI_List_Item_Interface $item
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getToggle(Eresus_UI_List_Item_Interface $item)
	{
		return $this->baseURL . $this->idName . '=' . $item->getId() . '&action=toggle';
	}
	//-----------------------------------------------------------------------------
}
