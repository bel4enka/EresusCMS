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
 * Виджет списка
 *
 * @package UI
 */
class Eresus_UI_Admin_List
{
	/**
	 * Поставщик данных
	 *
	 * @var Eresus_UI_Admin_List_DataProvider
	 */
	protected $provider;

	/**
	 * Описание столбцов
	 *
	 * @var array
	 */
	protected $cols;

	/**
	 * Скрытые столбцы
	 *
	 * @var array
	 */
	protected $hidden = array();

	/**
	 * Управление элементом списка
	 *
	 * @var array
	 */
	protected $controls = array();

	/**
	 * Создаёт новый виджет
	 *
	 * @param Eresus_UI_Admin_List_DataProvider $provider  поставщик данных для списка
	 *
	 * @return EresusListWidget
	 *
	 * @since 2.16
	 */
	public function __construct(Eresus_UI_Admin_List_DataProvider $provider)
	{
		$this->setProvider($provider);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает поставщика данных для виджета
	 *
	 * @param Eresus_UI_Admin_List_DataProvider $provider новый поставщик данных
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function setProvider(Eresus_UI_Admin_List_DataProvider $provider)
	{
		$this->provider = $provider;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавляет управление элементом списка
	 *
	 * Элементы управления задаются ключевыми словами:
	 * - toggle - переключение
	 * - edit - изменение
	 * - config - настройка
	 * - updown - перемещение вверх/вниз
	 * - move - перемещение в другой список
	 * - delete - удаление
	 *
	 * @param string $control1
	 * @param string $control2
	 * @param ...
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function addControls()
	{
		$args = func_get_args();
		foreach ($args as $arg)
		{
			switch (true)
			{
				case is_string($arg):
					$className = 'Eresus_UI_Admin_List_Control_' . strtoupper(substr($arg, 0, 1)) .
						substr($arg, 1);
					if (!class_exists($className))
					{
						throw new RuntimeException('Unknown list widget control: ' . $arg);
					}
					$this->itemControls []= new $className;
				break;
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Скрывает столбец
	 *
	 * @param string $colName имя столбца
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function hide($colName)
	{
		if (!is_array($this->cols))
		{
			$this->getCols();
		}
		if (isset($this->cols[$colName]))
		{
			$this->hidden[$colName] = $this->cols[$colName];
			unset($this->cols[$colName]);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Показывает столбец
	 *
	 * @param string $colName имя столбца
	 * @param string $caption заголовок столбца
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function show($colName, $caption = null)
	{
		if (is_array($this->cols))
		{
			if (isset($this->hidden[$colName]))
			{
				$this->cols[$colName] = $this->hidden[$colName];
				if ($caption !== null)
				{
					$this->cols[$colName]['caption'] = $caption;
				}
				unset($this->hidden[$colName]);
			}
		}
		else
		{
			$this->getCols();
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Скрывает все столбцы
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function hideAll()
	{
		if (!is_array($this->cols))
		{
			$this->getCols();
		}
		foreach ($this->cols as $colName => $colData)
		{
			$this->hidden[$colName] = $colData;
			unset($this->cols[$colName]);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает правила замены значений для столбца
	 *
	 * @param string $colName имя столбца
	 * @param array  $source  массив заменяемых значений
	 * @param array  $target  массив замещающих значений
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function setReplace($colName, $source, $target)
	{
		$this->getCols();
		if (!isset($this->cols[$colName]))
		{
			return;
		}
		$this->cols[$colName]['replace'] = array(
			'source' => $source,
			'target' => $target
		);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает код виджета
	 *
	 * @return string  HTML
	 *
	 * @since 2.16
	 */
	public function render()
	{
		$data = array();

		$data['list'] = $this;

		$tmpl = new Template('core/templates/widgets/list/main.html');
		$html = $tmpl->compile($data);
		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает описания столбцов
	 *
	 * @return array
	 *
	 * @since 2.16
	 */
	public function getCols()
	{
		if (!$this->cols)
		{
			$names = $this->provider->getCols();
			$this->cols = array();
			foreach ($names as $name)
			{
				$this->cols[$name] = array(
					'name' => $name,
					'caption' => $name,
				);
			}
		}
		return $this->cols;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает строки
	 *
	 * @return array
	 *
	 * @since 2.16
	 */
	public function getRows()
	{
		$rows = $this->provider->getRows();
		return $rows;
	}
	//-----------------------------------------------------------------------------
}
