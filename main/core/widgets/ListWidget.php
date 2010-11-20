<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Виджет списка
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
 * Виджет списка
 *
 * @package EresusCMS
 * @since 2.2x
 */
class ListWidget
{
	/**
	 * Имя компонента Doctrine
	 *
	 * @var string
	 */
	protected $componentName;

	/**
	 * Таблица данных
	 *
	 * @var Doctrine_Table
	 */
	protected $table;

	/**
	 * Размер страницы списка (в строках)
	 *
	 * @var int
	 */
	protected $pageSize = 10;

	/**
	 * Текущая страница списка
	 *
	 * @var int
	 */
	protected $page = 1;

	/**
	 * Столбцы списка
	 *
	 * @var array(ListWidgetColumn)
	 */
	protected $columns = array();

	/**
	 * Элементы управления
	 *
	 * @var array(ListWidgetControl)
	 */
	protected $controls = array();

	/**
	 * Создаёт новый виджет списка
	 *
	 * @param string $componentName  имя компонента Doctrine
	 *
	 * @return ListWidget
	 *
	 * @since 2.2x
	 */
	public function __construct($componentName = null)
	{
		if ($componentName)
		{
			$this->componentName = $componentName;
			$this->table = Doctrine_Core::getTable($componentName);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает размер страницы (в строках)
	 *
	 * @param int $pageSize новый размер страницы. Должен быть больше 0.
	 *
	 * @return ListWidget  Fluent
	 *
	 * @since 2.2x
	 */
	public function setPageSize($pageSize)
	{
		if ($pageSize)
		{
			$this->pageSize = $pageSize;
		}

		return $this;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавляет в список новый столбец
	 *
	 * @param string $name  имя столбца БД из которого надо брать данные
	 * @param string $title заголовок столбца в списке
	 *
	 * @return ListWidgetColumn  добавленный столбец
	 *`
	 * @since 2.2x
	 */
	public function addColumn($name = null, $title = null)
	{
		$col = new ListWidgetColumn();
		$this->columns []= $col;

		if ($name)
		{
			$col->setName($name);
		}
		if ($title)
		{
			$col->setTitle($title);
		}

		return $col;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавляет элементы управления элементами списка
	 *
	 * @param string|ListWidgetControl $control1
	 * @param string|ListWidgetControl $controlN
	 *
	 * @return ListWidget Fluent
	 *
	 * @since 2.2x
	 */
	public function addControls()
	{
		$controls = func_get_args();
		foreach ($controls as $control)
		{
			switch (true)
			{
				case $control instanceof ListWidgetControl:
					// TODO
				break;

				case is_string($control):
					$control = ListWidgetControl::factory($control);
					$this->controls[$control->getName()] = $control;
				break;
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку виджета
	 *
	 * @return string  HTML
	 *
	 * @since 2.2x
	 */
	public function getHTML()
	{
		$data = array(
			'header' => $this->buildHeader(),
			'body' => $this->buildBody(),
			'footer' => $this->buildFooter()
		);

		$tmpl = new Template('core/widgets/ListWidget.html');
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Строит заголовок списка
	 *
	 * @return string
	 *
	 * @since 2.2x
	 */
	protected function buildHeader()
	{
		$html = '';
		if (count($this->controls) > 0)
		{
			$html .= '<th>&nbsp;</th>';
		}

		foreach ($this->columns as $col)
		{
			$html .= $col->buildTitle();
		}
		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Строит тело списка
	 *
	 * @return string  HMTL
	 *
	 * @since 2.2x
	 */
	protected function buildBody()
	{
		$query = new Doctrine_Query();
		$query->from($this->componentName)->
			offset(($this->page - 1) * $this->pageSize)->
			limit($this->pageSize);

		$items = $query->execute();

		foreach ($items as $item)
		{
			$html .= '<tr>';

			/* ЭУ */
			if (count($this->controls) > 0)
			{
				$html .= '<td>';
				$arr = array();
				foreach ($this->controls as $control)
				{
					$arr []= $control->getHTML($item);
				}
				$html .= implode(' ', $arr);
				$html .= '</td>';
			}

			/* Данные */
			foreach ($this->columns as $col)
			{
				$html .= $col->buildCell($item[$col->getName()]);
			}
			$html .= '</tr>';
		}

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Строит нижнюю часть списка
	 *
	 * @return string
	 *
	 * @since 2.2x
	 */
	protected function buildFooter()
	{
		return '';
	}
	//-----------------------------------------------------------------------------
}
