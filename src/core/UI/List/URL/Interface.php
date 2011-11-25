<?php
/**
 * ${product.title}
 *
 * Интерфейс построителя шаблонов адресов
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
 * Интерфейс построителя шаблонов адресов
 *
 * Построитель адресов используется {@link Eresus_UI_List списком} чтобы создавать шаблоны адресов для
 * различных элементов управления, таких как переключатель страниц, «Изменить», «Удалить» и т. д.
 *
 * Вы можете создать собственный построитель или воспользоваться входящим в модуль UI:
 *
 * - {@link Eresus_UI_List_URL_Query}
 *
 * @package UI
 */
interface Eresus_UI_List_URL_Interface
{
	/**
	 * Должен возвращать шаблон URL для переключателя страниц
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getPagination();
	//-----------------------------------------------------------------------------

	/**
	 * Должен возвращать шаблон URL для ЭУ «Удалить»
	 *
	 * @param Eresus_UI_List_Item_Interface $item
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getDelete(Eresus_UI_List_Item_Interface $item);
	//-----------------------------------------------------------------------------

	/**
	 * Должен возвращать шаблон URL для ЭУ «Изменить»
	 *
	 * @param Eresus_UI_List_Item_Interface $item
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getEdit(Eresus_UI_List_Item_Interface $item);
	//-----------------------------------------------------------------------------

	/**
	 * Должен возвращать шаблон URL для ЭУ «Поднять выше»
	 *
	 * @param Eresus_UI_List_Item_Interface $item
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getOrderingUp(Eresus_UI_List_Item_Interface $item);
	//-----------------------------------------------------------------------------

	/**
	 * Должен возвращать шаблон URL для ЭУ «Опустить ниже»
	 *
	 * @param Eresus_UI_List_Item_Interface $item
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getOrderingDown(Eresus_UI_List_Item_Interface $item);
	//-----------------------------------------------------------------------------

	/**
	 * Должен возвращать шаблон URL для ЭУ «Включить/Отключить»
	 *
	 * @param Eresus_UI_List_Item_Interface $item
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getToggle(Eresus_UI_List_Item_Interface $item);
	//-----------------------------------------------------------------------------
}
