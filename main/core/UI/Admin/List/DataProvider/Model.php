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
 * Поставщик данных для Eresus_UI_Admin_List из модели
 *
 * @package UI
 *
 * @since 2.16
 */
class Eresus_UI_Admin_List_DataProvider_Model implements Eresus_UI_Admin_List_DataProvider
{
	/**
	 * Имя компонента (модели)
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
	 * Все поля записи
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Конструктор
	 *
	 * @param string $componentName
	 *
	 * @return Eresus_UI_Admin_List_DataProvider_Model
	 *
	 * @since 2.16
	 */
	public function __construct($componentName)
	{
		$this->componentName = $componentName;
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 * @see Eresus_UI_Admin_List_DataProvider::getCols()
	 */
	public function getCols()
	{
		if (!$this->fields)
		{
			$this->fields = $this->getTable()->getColumnNames();
		}
		return $this->fields;
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 * @see Eresus_UI_Admin_List_DataProvider::getRows()
	 */
	public function getRows()
	{
		$rows = $this->getTable()->findAll();

		return $rows->getData();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает таблицу модели
	 *
	 * @return Doctrine_Table
	 *
	 * @since 2.16
	 */
	protected function getTable()
	{
		if (!$this->table)
		{
			$this->table = EresusORM::getTable($this->componentName);
		}
		return $this->table;
	}
	//-----------------------------------------------------------------------------
}