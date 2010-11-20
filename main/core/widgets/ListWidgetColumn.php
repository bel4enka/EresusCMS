<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Столбцец виджета списка
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
 * Столбцец виджета списка
 *
 * @package EresusCMS
 * @since 2.2x
 */
class ListWidgetColumn
{
	/**
	 * Имя столбца
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Заголовок столбца
	 *
	 * @var string
	 */
	private $table;

	/**
	 * Создаёт новый столбец
	 *
	 * @return ListWidgetColumn
	 *
	 * @since 2.2x
	 */
	public function __construct()
	{
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает имя
	 *
	 * @param string $name  имя столбца БД из которого надо брать данные
	 *
	 * @return ListWidgetColumn
	 *
	 * @since 2.2x
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает имя
	 *
	 * @return string имя столбца БД из которого надо брать данные
	 *
	 * @since 2.2x
	 */
	public function getName()
	{
		return $this->name;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает заголовок
	 *
	 * @param string $title  заголовок столбца
	 *
	 * @return ListWidgetColumn
	 *
	 * @since 2.2x
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возварщает заголовок
	 *
	 * @return string  заголовок столбца
	 *
	 * @since 2.2x
	 */
	public function getTitle()
	{
		return $this->title;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возварщает разметку заголовока
	 *
	 * @return string  HTML
	 *
	 * @since 2.2x
	 */
	public function buildTitle()
	{
		return '<th>' . $this->getTitle() . '</tr>';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку ячейки
	 *
	 * @param mixed $value значение ячейки
	 *
	 * @return string  HTML
	 *
	 * @since 2.2x
	 */
	public function buildCell($value)
	{
		return '<td>' . $value . '</td>';
	}
	//-----------------------------------------------------------------------------

}
