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
	 * Кэш описаний столбцов
	 *
	 * @var array
	 */
	protected $cols;

	/**
	 * Кэш строк
	 *
	 * @var array
	 */
	protected $rows;

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
	protected $itemControls = array();

	/**
	 * Размер страницы
	 *
	 * @var int
	 */
	protected $pageSize = 100;

	/**
	 * Задаёт порядок расположения ЭУ
	 *
	 * @var array
	 */
	private $itemControlsOrderMap = array(
		'Eresus_UI_Admin_List_ItemControl_Edit' => 0,
		'Eresus_UI_Admin_List_ItemControl_Toggle' => 99,
		'Eresus_UI_Admin_List_ItemControl_Delete' => 100,
	);

	/**
	 * Индекс массива $itemControls, куда надо вставить следующий нестандартный ЭУ
	 *
	 * @var int
	 */
	private $itemControlIndex = 1;

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
	public function addItemControls()
	{
		$args = func_get_args();
		foreach ($args as $arg)
		{
			$control = $this->getItemControlObject($arg);
			$controlClass = get_class($control);
			if (isset($this->itemControlsOrderMap[$controlClass]))
			{
				$this->itemControls[$this->itemControlsOrderMap[$controlClass]]= $control;
			}
			else
			{
				$this->itemControls[$this->itemControlIndex++]= $control;
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
	 * Устанавливает заменитель значений столбца
	 *
	 * @param string                        $colName  имя столбца
	 * @param Eresus_UI_Admin_List_Mutator  $mutator  заменитель, если не указан или null, предыдущий
	 *                                                заменитель будет удалён
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function setMutator($colName, Eresus_UI_Admin_List_Mutator $mutator = null)
	{
		$this->getCols();
		if (!isset($this->cols[$colName]))
		{
			return;
		}
		$this->cols[$colName]['mutator'] = $mutator;
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

		ksort($this->itemControls);
		$data['list'] = $this;

		$totalPages = ceil($this->provider->getCount() / $this->pageSize);
		$data['pagination'] = new Eresus_UI_Pagination($totalPages);

		$tmpl = Eresus_Template::fromFile('core/templates/widgets/list/main.html');
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
		if (!$this->rows)
		{
			$rows = $this->provider->getRows();
			$this->rows = array();
			$cols = $this->getCols();
			foreach ($rows as $model)
			{
				$row = array('model' => $model, 'values' => array());
				foreach ($cols as $key => $options)
				{
					$value = $model[$key];
					if (isset($options['mutator']))
					{
						$value = $options['mutator']->mutate($value);
					}
					$row['values'] []= $value;
				}
				$this->rows []= $row;
			}
		}
		return $this->rows;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возращает true если в списке предусмотрены ЭУ
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	public function hasItemControls()
	{
		return count($this->itemControls) > 0 ;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возращает элементы управления для строки списка
	 *
	 * @param object $item  элемент, для которого надо отобразить ЭУ
	 *
	 * @return array
	 *
	 * @since 2.16
	 */
	public function getItemControls($item)
	{
		if (!is_object($item))
		{
			throw new InvalidArgumentException('$item must be an object, ' . gettype($item) . 'given');
		}

		foreach ($this->itemControls as &$control)
		{
			$control->setListItem($item);
		}
		return $this->itemControls;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает размер страницы
	 *
	 * @param int $size
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function setPageSize($size)
	{
		$this->pageSize = $size;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает объект ЭУ
	 *
	 * @param mixed $control  объект или ключевое слово
	 *
	 * @return Eresus_UI_Admin_List_ItemControl
	 *
	 * @since 2.16
	 */
	private function getItemControlObject($control)
	{
		if ($control instanceof Eresus_UI_Admin_List_ItemControl)
		{
			return $control;
		}

		if (is_string($control))
		{
			$className = 'Eresus_UI_Admin_List_ItemControl_' . strtoupper(substr($control, 0, 1)) .
				substr($control, 1);
			if (!class_exists($className))
			{
				throw new RuntimeException('Unknown list widget control: ' . $control);
			}
			return new $className;
		}

		throw new InvalidArgumentException();
	}
	//-----------------------------------------------------------------------------
}
