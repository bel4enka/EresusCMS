<?php
/**
 * ${product.title}
 *
 * Поставщик данных из массива для {@link Eresus_Eresus_UI_List списка}
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
 * Поставщик данных из массива для {@link Eresus_Eresus_UI_List списка}
 *
 * @package Eresus
 */
class Eresus_UI_List_DataProvider_Array implements Eresus_UI_List_DataProvider_Interface
{
	/**
	 * Массив с данными
	 *
	 * @var array
	 */
	private $array;

	/**
	 * Конструктор
	 *
	 * @param array $array
	 * @param array  $map   карта соответствия свойств
	 *
	 * @since 2.17
	 */
	public function __construct(array $array, array $map = array())
	{
		$this->array = array();
		foreach ($array as $item)
		{
			$this->array []= new Eresus_UI_List_Item_Object($item, $map);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Eresus_UI_List_DataProvider_Interface::getItems
	 * @since 2.17
	 */
	public function getItems($limit = null, $offset = 0)
	{
		return $this->array;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Eresus_UI_List_DataProvider_Interface::getCount
	 * @since 2.17
	 */
	public function getCount()
	{
		return count($this->array);
	}
	//-----------------------------------------------------------------------------
}