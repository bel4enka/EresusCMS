<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * ЭУ элемента виджета списка
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 * @package EresusCMS
 *
 * $Id: WebServer.php 1153 2010-11-15 18:44:29Z mk $
 */

/**
 * ЭУ элемента виджета списка
 *
 * @package EresusCMS
 * @since 2.2x
 */
class ListWidgetControl
{
	/**
	 * Подсказка к ЭУ
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Альтернативный текст ЭУ
	 *
	 * @var string
	 */
	protected $alt;

	/**
	 * Фабрика
	 *
	 * @param string $name  имя стандартного ЭУ
	 *
	 * @return ListWidgetControl
	 *
	 * @since 2.2x
	 */
	public static function factory($name)
	{
		switch ($name)
		{
			case 'edit':
				return new ListWidgetControlEdit();
			break;
			case 'delete':
				return new ListWidgetControlDelete();
			break;
			case 'toggle':
				return new ListWidgetControlToggle();
			break;
		}
		throw new InvalidArgumentException("Unknown standard control name: $name");
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает имя ЭУ
	 *
	 * @return string имя ЭУ
	 *
	 * @since 2.2x
	 */
	public function getName()
	{
		return strtolower(substr(get_class($this), strlen('ListWidgetControl')));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку ЭУ
	 *
	 * @param Doctrine_Record $item  элемент списка, для которого нужен этот ЭУ
	 *
	 * @return string
	 *
	 * @since 2.2x
	 */
	public function getHTML(Doctrine_Record $item)
	{
		$html = '<img src="' . httpRoot . $this->getIcon($item) . '" alt="' . $this->getAlt($item) . '" title="' .
			$this->getTitle($item) . '" />';

		$html = '<a href="' . $this->getURL($item) . '">' . $html . '</a>';

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает URL значка ЭУ
	 *
	 * @param Doctrine_Record $item  элемент списка, для которого нужен этот ЭУ
	 *
	 * @return string
	 *
	 * @since 2.2x
	 */
	protected function getIcon(Doctrine_Record $item = null)
	{
		$theme = $GLOBALS['page']->getUITheme();
		$icon = $theme->getIcon('item-' . $this->getName() . '.png');
		return $icon;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает URL значка ЭУ
	 *
	 * @param Doctrine_Record $item  элемент списка, для которого нужен этот ЭУ
	 *
	 * @return string
	 *
	 * @since 2.2x
	 */
	protected function getURL(Doctrine_Record $item = null)
	{
		return $GLOBALS['page']->url(array('id' => $item->id, 'action' => $this->getName()));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает подсказку к ЭУ
	 *
	 * @param Doctrine_Record $item  элемент списка, для которого нужен этот ЭУ
	 *
	 * @return string
	 *
	 * @since 2.2x
	 */
	protected function getTitle(Doctrine_Record $item = null)
	{
		return $this->title;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает альтернативный текст для ЭУ
	 *
	 * @param Doctrine_Record $item  элемент списка, для которого нужен этот ЭУ
	 *
	 * @return string
	 *
	 * @since 2.2x
	 */
	protected function getAlt(Doctrine_Record $item = null)
	{
		return $this->alt;
	}
	//-----------------------------------------------------------------------------

}



/**
 * ЭУ "Изменить"
 *
 * @package EresusCMS
 * @since 2.2x
 */
class ListWidgetControlEdit extends ListWidgetControl
{
	/**
	 * Конструктор
	 *
	 * @return ListWidgetControlEdit
	 *
	 * @since 2.2x
	 */
	public function __construct()
	{
		$this->title = I18n::getInstance()->getText('Edit', 'ListWidget');
		$this->alt = I18n::getInstance()->getText('[edit]', 'ListWidget');
	}
	//-----------------------------------------------------------------------------
}



/**
 * ЭУ "Удалить"
 *
 * @package EresusCMS
 * @since 2.2x
 */
class ListWidgetControlDelete extends ListWidgetControl
{
	/**
	 * Конструктор
	 *
	 * @return ListWidgetControlEdit
	 *
	 * @since 2.2x
	 */
	public function __construct()
	{
		$this->title = I18n::getInstance()->getText('Delete', 'ListWidget');
		$this->alt = I18n::getInstance()->getText('[del]', 'ListWidget');
	}
	//-----------------------------------------------------------------------------
}



/**
 * ЭУ "Вкл/выкл"
 *
 * @package EresusCMS
 * @since 2.2x
 */
class ListWidgetControlToggle extends ListWidgetControl
{
	/**
	 * Возвращает URL значка ЭУ
	 *
	 * @param Doctrine_Record $item  элемент списка, для которого нужен этот ЭУ
	 *
	 * @return string
	 *
	 * @since 2.2x
	 */
	protected function getIcon(Doctrine_Record $item = null)
	{
		$theme = $GLOBALS['page']->getUITheme();
		$icon = $theme->getIcon('item-' . ($item->active ? 'active' : 'inactive') . '.png');
		return $icon;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает подсказку к ЭУ
	 *
	 * @param Doctrine_Record $item  элемент списка, для которого нужен этот ЭУ
	 *
	 * @return string
	 *
	 * @since 2.2x
	 */
	protected function getTitle(Doctrine_Record $item = null)
	{
		if ($item->active)
		{
			$title = I18n::getInstance()->getText('Disable', 'ListWidget');
		}
		else
		{
			$title = I18n::getInstance()->getText('Enable', 'ListWidget');
		}
		return $title;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает альтернативный текст для ЭУ
	 *
	 * @param Doctrine_Record $item  элемент списка, для которого нужен этот ЭУ
	 *
	 * @return string
	 *
	 * @since 2.2x
	 */
	protected function getAlt(Doctrine_Record $item = null)
	{
		if ($item->active)
		{
			$alt = I18n::getInstance()->getText('[off]', 'ListWidget');
		}
		else
		{
			$alt = I18n::getInstance()->getText('[on]', 'ListWidget');
		}
		return $alt;
	}
	//-----------------------------------------------------------------------------
}
