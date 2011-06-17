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
 * Абстрактный ЭУ для {@link Eresus_UI_Admin_List}
 *
 * @package UI
 *
 * @since 2.16
 */
abstract class Eresus_UI_Admin_List_ItemControl
{
	/**
	 * Действие
	 *
	 * Фрагмент, дописываемый к текущему URL для запроса нужного действия
	 *
	 * @var string
	 */
	protected $action;

	/**
	 * Название действия
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Имя файла значка
	 *
	 * @var string
	 */
	protected $icon;

	/**
	 * Альтернативный текст значка
	 *
	 * @var string
	 */
	protected $alt;

	/**
	 * Обрабатываемый элемент списка
	 *
	 * @var array
	 */
	protected $item;

	/**
	 * Функция для проверки необходимости показа ЭУ
	 *
	 * @var callback
	 */
	private $switch;

	/**
	 * Устанавливает элемент списка, для которого нужно отобразить этот элемент управления
	 *
	 * @param array $item
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function setListItem($item)
	{
		$this->item = $item;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает действие
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getAction()
	{
		return $this->action . '/' . $this->item->id;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает название действия
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getTitle()
	{
		return $this->title;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает URL значка
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getIcon()
	{
		$theme = $GLOBALS['page']->getUITheme();
		return Eresus_Kernel::app()->getWebRoot() . '/' . $theme->getImage('medium/' . $this->icon);
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
		return $this->alt;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает функцию, включающую или отключающую ЭУ для конкретного объекта
	 *
	 * callback-функция в качестве аргумента должна принимать объект и возвращать булево значение —
	 * true, если ЭУ надо отрисовывать и false если не надо.
	 *
	 * @param callback $callback
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function setSwitch($callback)
	{
		$this->switch = $callback;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает true, если этот ЭУ доступен для текущего объекта
	 *
	 * @return false
	 *
	 * @since 2.16
	 */
	public function isAvailable()
	{
		if ($this->switch)
		{
			return call_user_func($this->switch, $this->item);
		}
		return true;
	}
	//-----------------------------------------------------------------------------
}