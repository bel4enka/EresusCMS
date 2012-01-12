<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-2008, Eresus Project, http://eresus.ru/
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
 * @package Eresus
 *
 * $Id$
 */


/**
 * Работа с учётными записями пользователей
 * @package Eresus
 */
class EresusAccounts {
	var $table = 'users';
	var $cache = array();
 /**
	* Возвращает список полей
	*
	* @access public
	*
	* @return array Список полей
	*/
	function fields()
	{
		global $Eresus;

		if (isset($this->cache['fields'])) $result = $this->cache['fields']; else {
			$result = $Eresus->db->fields($this->table);
			$this->cache['fields'] = $result;
		}
		return $result;
	}
	//------------------------------------------------------------------------------
	/**
	 * Возвращает учётную запись или список записей
	 *
	 * @access public
	 *
	 * @param int    $id  ID пользователя
	 * или
	 * @param array  $id  Список идентификаторов
	 * или
	 * @param string $id  SQL-условие
	 *
	 * @return array
	 */
	public function get($id)
	{
		global $Eresus;

		if (is_array($id))
		{
			// FIXME
			return null;
		}
		elseif (is_numeric($id))
		{
			return Doctrine_Core::getTable('Eresus_Entity_User')->find($id);
		}
		else
		{
			//FIXME
			return null;
		}
	}
	//------------------------------------------------------------------------------
	function getByName($name)
	{
		return $this->get("`login` = '$name'");
	}
	//-----------------------------------------------------------------------------
	/**
	*	Добавляет	учётную	запись
	*
	*	@access	public
	*
	*	@param	array	$item	Учётная	запись
	*
	*	@return	mixed	Описание	записи	или	false	в	случае	неудачи
	*/
	function	add($item)
	{
		global	$Eresus;

		$result	=	false;
		if	(isset($item['id']))	unset($item['id']);
		if	(!isset($item['profile']))	$item['profile']	=	array();
		$item['profile']	=	encodeOptions($item['profile']);
		if	($Eresus->db->insert($this->table,	$item))
			$result	=	$this->get($Eresus->db->getInsertedId());
		return	$result;
	}
	//------------------------------------------------------------------------------
	/**
	*	Изменяет	учётную	запись
	*
	*	@access	public
	*
	*	@param	array	$item	Учётная	запись
	*
	*	@return	mixed	Описание	изменённой	записи	или	false	в	случае	неудачи
	*/
	function	update($item)
	{
		global	$Eresus;

		$result	=	false;
		$item['profile']	=	encodeOptions($item['profile']);
		$result	=	$Eresus->db->updateItem($this->table,	$item,	"`id`={$item['id']}");
		return	$result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Удаляет учётную запись
	 *
	 * @param int $id  Идентификатор записи
	 *
	 * @return	void
	 */
	public function delete($id)
	{
		$user = Doctrine_Core::getTable('Eresus_Entity_User')->find($id);
		$user->delete();
	}
	//------------------------------------------------------------------------------
}

/**
 * @deprecated since Eresus 2.11
 *
 * @package Eresus
 */
class	Accounts extends EresusAccounts {}
