<?php
/**
 * ${product.title}
 *
 * Меню АИ
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
 * $Id: URI.php 1746 2011-07-27 06:53:41Z mk $
 */


/**
 * Меню АИ
 *
 * @package Eresus
 */
class Eresus_UI_Menu_Admin extends Eresus_UI_Menu
{
	/**
	 * Добавляет новый пункт в меню
	 *
	 * @param mixed $item  объект {@link Eresus_UI_Menu_Admin_Item} или массив с такими же элементами
	 *
	 * @return void
	 *
	 * @since 2.20
	 */
	public function addItem($item)
	{
		if (is_array($item))
		{
			$tmp = new Eresus_UI_Menu_Admin_Item();
			$tmp->setPath(@$item['path']);
			$tmp->setAccess(@$item['access']);
			$tmp->setCaption(@$item['caption']);
			$tmp->setHint(@$item['hint']);
			$item = $tmp;
		}
		elseif (!($item instanceof Eresus_UI_Menu_Admin_Item))
		{
			throw new InvalidArgumentException('Argument 1 passed to ' . __METHOD__ .
				' must be an instance of Eresus_UI_Menu_Admin_Item or an array, ' . gettype($item) .
				' given');
		}
		if (Eresus_Security::getInstance()->isGranted($item->access))
		{
			parent::addItem($item);
		}
	}
	//-----------------------------------------------------------------------------
}

