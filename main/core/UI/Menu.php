<?php
/**
 * ${product.title}
 *
 * Меню
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
 * $Id$
 */


/**
 * Меню
 *
 * Меню представляет собой контейнер пунктов ({@link Eresus_UI_Menu_Item}). Пункты можно добавлять
 * в меню при помощи {@link addItem()}. Отрисовка меню производится вызовом {@link render()}.
 *
 * @package Eresus
 */
class Eresus_UI_Menu
{
	/**
	 * Пункты меню
	 *
	 * @var array
	 */
	private $items = array();

	/**
	 * Добавляет новый пункт в меню
	 *
	 * @param Eresus_UI_Menu_Item $item
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function addItem($access, $path, $caption)
	{
		$item = new Eresus_UI_Menu_Admin_Item();
		$item->setPath($path);
		$item->setAccess($access);
		$item->setCaption($caption);
		$this->items []= $item;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает отрисованное меню
	 *
	 * @param string $templateName  имя файла шаблона
	 *
	 * @return string  HTML
	 *
	 * @since 2.16
	 */
	public function render($templateName)
	{
		return 'MENU';
	}
	//-----------------------------------------------------------------------------
}

