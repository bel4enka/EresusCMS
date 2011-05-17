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
 * Меню в АИ
 *
 * @package UI
 */
class Eresus_UI_Admin_Menu
{
	/**
	 * Пункты меню
	 *
	 * @var array
	 */
	private $items = array();

	/**
	 * Имя шаблона
	 *
	 * @var string
	 */
	private $tmplName = 'default';

	/**
	 * Конструктор
	 *
	 * @param string $tmplName имя шаблона меню
	 *
	 * @return Eresus_UI_Admin_Menu
	 */
	public function __construct($tmplName = null)
	{
		if ($tmplName)
		{
			$this->tmplName = $tmplName;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку меню
	 *
	 * @return string
	 */
	public function render()
	{
		$items = array();
		foreach ($this->items as $item)
		{
			if (!UserRights($item['accessLevel']))
			{
				continue;
			}
			$item['url'] = Eresus_CMS::app()->getWebRoot() . '/admin' . $item['url'];
			$items []= $item;
		}

		$tmpl = Eresus_Template::fromFile('core/templates/widgets/menu/' . $this->tmplName . '.html');
		$html = $tmpl->compile(array('items' => $items));
		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавляет новый пункт в меню
	 *
	 * @param string $title        текст пункта меню
	 * @param string $url          адрес
	 * @param int    $accessLevel  требуемый уровень доступа
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function addItem($title, $url = null, $accessLevel = ADMIN)
	{
		$this->items []= array(
			'title' => $title,
			'url' => $url,
			'accessLevel' => $accessLevel
		);
	}
	//-----------------------------------------------------------------------------
}
