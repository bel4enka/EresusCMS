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
 * ������ ��������� �������� ��������� ����������� ������������. ��
 * ������ �������������� �� �/��� �������������� � ������������ �
 * ��������� ������ 3 ���� (�� ������ ������) � ��������� ����� �������
 * ������ ����������� ������������ �������� GNU, �������������� Free
 * Software Foundation.
 *
 * �� �������������� ��� ��������� � ������� �� ��, ��� ��� ����� ���
 * ��������, ������ �� ������������� �� ��� ������� ��������, � ���
 * ����� �������� ��������� ��������� ��� ������� � ����������� ���
 * ������������� � ���������� �����. ��� ��������� ����� ���������
 * ���������� ������������ �� ����������� ������������ ��������� GNU.
 *
 * �� ������ ���� �������� ����� ����������� ������������ ��������
 * GNU � ���� ����������. ���� �� �� �� ��������, �������� �������� ��
 * <http://www.gnu.org/licenses/>
 *
 * @package Eresus
 *
 * $Id$
 */


/**
 * ������ � �������� �������� �������������
 * @package Eresus
 */
class EresusAccounts {
	var $table = 'users';
	var $cache = array();
 /**
	* ���������� ������ �����
	*
	* @access public
	*
	* @return array ������ �����
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
	* ���������� ������� ������ ��� ������ �������
	*
	* @access public
	*
	* @param int    $id  ID ������������
	*	���
	*	@param array  $id  ������ ���������������
	*	���
	*	@param string $id  SQL-�������
	*
	* @return array
	*/
	function	get($id)
	{
		global	$Eresus;

		if	(is_array($id))	$what	=	"FIND_IN_SET(`id`,	'".implode(',',	$id)."')";
		elseif	(is_numeric($id))	$what	=	"`id`=$id";
		else	$what	=	$id;
		$result	=	$Eresus->db->select($this->table,	$what);
		if	($result)	for($i=0;	$i<count($result);	$i++)	$result[$i]['profile']	=	decodeOptions($result[$i]['profile']);
		if	(is_numeric($id)	&&	$result	&&	count($result))	$result	=	$result[0];
		return	$result;
	}
	//------------------------------------------------------------------------------
	function getByName($name)
	{
		return $this->get("`login` = '$name'");
	}
	//-----------------------------------------------------------------------------
	/**
	*	���������	�������	������
	*
	*	@access	public
	*
	*	@param	array	$item	�������	������
	*
	*	@return	mixed	��������	������	���	false	�	������	�������
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
	*	��������	�������	������
	*
	*	@access	public
	*
	*	@param	array	$item	�������	������
	*
	*	@return	mixed	��������	���������	������	���	false	�	������	�������
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
	*	�������	�������	������
	*
	*	@access	public
	*
	*	@param	int	$id	�������������	������
	*
	*	@return	bool	���������	��������
	*/
	function	delete($id)
	{
		global	$Eresus;

		$result	=	$Eresus->db->delete($this->table,	"`id`=$id");
		return	$result;
	}
	//------------------------------------------------------------------------------
}

/**
 * @deprecated since Eresus 2.11
 *
 * @package Eresus
 */
class	Accounts extends EresusAccounts {}
