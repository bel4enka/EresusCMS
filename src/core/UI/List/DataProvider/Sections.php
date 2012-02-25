<?php
/**
 * ${product.title}
 *
 * Поставщик данных для списка разделов
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
 * Поставщик данных для списка разделов
 *
 * @package Eresus
 * @since 2.17
 */
class Eresus_UI_List_DataProvider_Sections implements Eresus_UI_List_DataProvider_Interface
{
	/**
	 * Разделы сайта
	 *
	 * @var array
	 */
	private static $sections;

	/**
	 * @see Eresus_UI_List_DataProvider_Interface::getItems
	 * @since 2.17
	 */
	public function getItems($limit = null, $offset = 0)
	{
		if (!self::$sections)
		{
			self::loadData();
		}
		return self::$sections;
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

	/**
	 * Загружает данные
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	private static function loadData()
	{
		$q = Doctrine_Query::create()->
			select('s.id, s.owner, s.title, s.caption, s.description, s.hint, s.keywords, s.position,
				s.active, s.access, s.visible, s.template, s.type, s.options, s.created, s.updated')->
			from('Eresus_Entity_Section s')->
			orderBy('s.owner, s.position');
		$sections = $q->execute();

		$index = new EresusCollection();
		$index->setDefaultValue(array());
		foreach ($sections as $key => $section)
		{
			$index[$section->owner] []= $key;
		}

		self::$sections = self::buildBranch($sections, $index, 0, 1);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список подразделов
	 *
	 * @param Doctrine_Collection $sections  все разделы
	 * @param EresusCollection    $index     индекс
	 * @param int                 $owner     идентификатор родителя
	 * @param int                 $level     уровень вложенности
	 *
	 * @return array
	 *
	 * @since 2.17
	 */
	private static function buildBranch(Doctrine_Collection $sections, EresusCollection $index,
		$owner, $level)
	{
		assert('preg_match("/^\d+$/", $owner)');
		assert('preg_match("/^\d+$/", $level)');

		$result = array();
		foreach ($index[$owner] as $id)
		{
			$obj = new Eresus_UI_List_Item_Section($sections[$id]);
			$obj->level = $level;
			$result []= $obj;
			$result = array_merge($result, self::buildBranch($sections, $index, $sections[$id]->id,
				$level + 1));
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
}