<?php
/**
 * ${product.title}
 *
 * Список элементов
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
 * Список элементов
 *
 * Позволяет создавать многостраничные списки произвольных элементов, получаемых из различных
 * источников и оформлять их на основе произвольных шаблонов.
 *
 * Для получения данных используется специальный объект-посредник, реализующий интерфейс
 * {@link Eresus_UI_List_DataProvider_Interface}.
 *
 * Можно задать размер страницы и номер текущей страницы списка при помощи {@link setPageSize()} и
 * {@link setPage()} соответственно.
 *
 * Для отрисовки списка надо передать в шаблон объект {@link Eresus_UI_List}, а затем использовать
 * методы {@link getItems()}, {@link getPagination()} и {@link getControls()} для вставки в шаблон
 * соответствующих частей списка.
 *
 * Чтобы использовать в шаблоне переключатель страниц и другие элементы управления списокм, надо
 * задать генератор адресов методом {@link setURL()}.
 *
 * Пример шаблона:
 *
 * <code>
 * {if count($list->getItems())}
 * <ul>
 *   {foreach $list->getItems() item}
 *   <li>
 *     {$list->getControls($item, 'edit', 'toggle', 'delete')}
 *     {$item->foo}
 *     {$item->bar}
 *   </li>
 *   {/foreach}
 * </ul>
 * {$list->getPagination()->render()}
 * {/if}
 * </code>
 *
 * @package Eresus
 */
class Eresus_UI_List
{
	/**
	 * Поставщик данных
	 *
	 * @var Eresus_UI_List_DataProvider_Interface
	 */
	private $dataProvider;

	/**
	 * Размер страницы
	 *
	 * @var int
	 */
	private $pageSize;

	/**
	 * Элементы списка
	 *
	 * @var array
	 */
	private $items;

	/**
	 * Корневой URL
	 *
	 * @var Eresus_UI_List_URL_Interface
	 */
	private $url;

	/**
	 * Страница списка
	 *
	 * @var int
	 */
	private $page = 1;

	/**
	 * Переключатель страниц
	 *
	 * @var PaginationHelper
	 */
	private $pagination = null;

	/**
	 * Доступные ЭУ
	 *
	 * @var array
	 * @see registerControl()
	 * @see getControls()
	 */
	private $controls = array(
		'config' => 'Eresus_UI_List_Control_Config',
		'delete' => 'Eresus_UI_List_Control_Delete',
		'edit' => 'Eresus_UI_List_Control_Edit',
		'up' => 'Eresus_UI_List_Control_Up',
		'down' => 'Eresus_UI_List_Control_Down',
		'toggle' => 'Eresus_UI_List_Control_Toggle',
	);

	/**
	 * Конструктор
	 *
	 * @param Eresus_UI_List_DataProvider_Interface $provider  поставщик данных
	 * @param Eresus_UI_List_URL_Interface          $url       построитель адресов
	 *
	 * @return Eresus_UI_List
	 *
	 * @since 2.17
	 */
	public function __construct(Eresus_UI_List_DataProvider_Interface $provider = null,
		Eresus_UI_List_URL_Interface $url = null)
	{
		if ($provider)
		{
			$this->setDataProvider($provider);
		}
		if ($url)
		{
			$this->setURL($url);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает поставщика данных
	 *
	 * @return Eresus_UI_List_DataProvider_Interface
	 *
	 * @since 2.17
	 */
	public function getDataProvider()
	{
		return $this->dataProvider;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает поставщика данных
	 *
	 * @param Eresus_UI_List_DataProvider_Interface $provider
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	public function setDataProvider(Eresus_UI_List_DataProvider_Interface $provider)
	{
		$this->dataProvider = $provider;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает построитель адресов
	 *
	 * @return Eresus_UI_List_URL_Interface
	 *
	 * @since 2.17
	 */
	public function getURL()
	{
		if (!$this->url)
		{
			$this->url = new Eresus_UI_List_URL_Query();
		}
		return $this->url;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает построитель адресов
	 *
	 * См. {@link Eresus_UI_List_URL_Interface}
	 *
	 * @param Eresus_UI_List_URL_Interface $url
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	public function setURL(Eresus_UI_List_URL_Interface $url)
	{
		$this->url = $url;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает номер текущей страницы списка
	 *
	 * @param int $page
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	public function setPage($page)
	{
		$this->page = $page;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливет размер страницы (в строках)
	 *
	 * @param int $size
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	public function setPageSize($size)
	{
		$this->pageSize = $size;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает массив элементов списка для подстановки в шаблон
	 *
	 * @return array
	 *
	 * @since 2.17
	 */
	public function getItems()
	{
		if (is_null($this->items))
		{
			$this->items = $this->dataProvider->getItems($this->pageSize,
				($this->page - 1) * $this->pageSize);
		}
		return $this->items;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает переключатель страниц
	 *
	 * @return PaginationHelper
	 *
	 * @since 2.17
	 */
	public function getPagination()
	{
		if (!$this->pagination)
		{
			$totalPages = $this->pageSize ? ceil($this->dataProvider->getCount() / $this->pageSize) : 0;
			$this->pagination = new PaginationHelper($totalPages, $this->page,
				$this->getURL()->getPagination());
		}
		return $this->pagination;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Регистрирует дополнительный ЭУ
	 *
	 * @param string $name   имя ЭУ для использования в {@link getControls()}
	 * @param string $class  имя класса ЭУ
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	public function registerControl($name, $class)
	{
		$this->controls[$name] = $class;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку элементов управления для использования в шаблоне
	 *
	 * Стандартные имена ЭУ:
	 *
	 * - delete — Удаление
	 * - config/edit — Изменение (разница в значках)
	 * - up — Переместить выше в списке
	 * - down — Переместить ниже в списке
	 * - toggle — Включить/Отключить
	 *
	 * Другие ЭУ могут быть заданы при помощи {@link registerControl()}
	 *
	 * @param Eresus_UI_List_Item_Interface $item         элемент списка, для которого нужны ЭУ
	 * @param string                 $control1…$controlN  список ЭУ, которые нужны
	 *
	 * @throws LogicException  если запрошенный ЭУ не зарегистрирван
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getControls(Eresus_UI_List_Item_Interface $item)
	{
		$controls = func_get_args();
		array_shift($controls);

		$html = '';

		foreach ($controls as $control)
		{
			if (!isset($this->controls[$control]))
			{
				throw new LogicException('Unknown list control: ' . $control);
			}

			if (is_string($this->controls[$control]))
			{
				/* Объект ЭУ ещё не создан. Создаём */
				$class = $this->controls[$control];
				if (!class_exists($class))
				{
					throw new LogicException('Class "' . $class . '" not found');
				}
				$this->controls[$control] = new $class($this);
			}

			$html .= $this->controls[$control]->render($item);
		}

		return $html;
	}
	//-----------------------------------------------------------------------------
}
