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
	 * @param mixed $item  объект {@link Eresus_UI_Menu_Item} или массив с такими же элементами
	 *
	 * @return void
	 *
	 * @since 2.20
	 */
	public function addItem($item)
	{
		if (is_array($item))
		{
			$tmp = new Eresus_UI_Menu_Item();
			$tmp->setPath(@$item['path']);
			$tmp->setCaption(@$item['caption']);
			$tmp->setHint(@$item['hint']);
			$item = $tmp;
		}
		elseif (!($item instanceof Eresus_UI_Menu_Item))
		{
			throw new InvalidArgumentException('Argument 1 passed to ' . __METHOD__ .
				' must be an instance of Eresus_UI_Menu_Item or an array, ' . gettype($item) .
				' given');
		}
		$this->items []= $item;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает отрисованное меню
	 *
	 * @param string $templateName  имя файла шаблона
	 * @param string $module        имя модуля
	 *
	 * @return string  HTML
	 *
	 * @since 2.20
	 */
	public function render($templateName, $module = 'core')
	{
		$ts = Eresus_Template_Service::getInstance();
		$tmpl = $ts->get($templateName, $module);
		return $tmpl->compile(array('menu' => $this));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает пункты меню
	 *
	 * @return array
	 *
	 * @since 2.20
	 */
	public function getItems()
	{
		return $this->items;
	}
	//-----------------------------------------------------------------------------
}

