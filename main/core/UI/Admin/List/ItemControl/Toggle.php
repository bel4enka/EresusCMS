<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
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
 * @package UI
 *
 * $Id$
 */

/**
 * ЭУ "Вкл/Выкл" для {@link Eresus_UI_Admin_List}
 *
 * @package UI
 *
 * @since 2.16
 */
class Eresus_UI_Admin_List_ItemControl_Toggle extends Eresus_UI_Admin_List_ItemControl
{
	/**
	 * @see Eresus_UI_Admin_List_ItemControl::$action
	 */
	protected $action = 'toggle';

	/**
	 * @see Eresus_UI_Admin_List_ItemControl::$icon
	 */
	protected $icon = 'item-%s.png';

	/**
	 * @see Eresus_UI_Admin_List_ItemControl::getTitle()
	 */
	public function getTitle()
	{
		return $this->item->active ? i18n('Disable') : i18n('Enable');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Eresus_UI_Admin_List_ItemControl::getIcon()
	 */
	public function getIcon()
	{
		$icon = parent::getIcon();
		$icon = sprintf($icon, $this->item->active ? 'active' : 'inactive');
		return $icon;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает альтернативный текст для значка
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getAlt()
	{
		return $this->item->active ? '[off]' : '[on]';
	}
	//-----------------------------------------------------------------------------

}
