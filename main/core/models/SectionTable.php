<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Таблица разделов
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
 * $Id$
 */

/**
 * Таблица разделов
 *
 * @package EresusCMS
 */
class SectionTable extends Doctrine_Table
{
	/**
	 * Returns an instance of this class.
	 *
	 * @return object SectionTable
	 */
	public static function getInstance()
	{
		return Doctrine_Core::getTable('Section');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Ищет разделы по родителю
	 *
	 * @param int  $owner                   идентификатор родительского раздела
	 * @param bool $visibleOnly [optional]  вернуть только видимые разделы
	 * @param bool $activeOnly [optional]   вернуть только активные разделы
	 * @param int  $access [optional]       минимальный уровень доступа
	 *
	 * @return Doctrine_Collection
	 *
	 * @since #548
	 */
	public function findByOwner($owner, $visibleOnly = true, $activeOnly = true, $access = USER)
	{
		$where = array('s.owner = ?', 's.access >= ?');
		if ($visibleOnly)
		{
			$where []= 's.visible = 1';
		}
		if ($activeOnly)
		{
			$where []= 's.active = 1';
		}

		return EresusQuery::create()->
			from('Section s')->
			where(implode(' AND ', $where), array($owner, $access))->
			orderBy('position')->
			execute();
	}
	//-----------------------------------------------------------------------------
}