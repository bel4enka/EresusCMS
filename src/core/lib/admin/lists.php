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
 * ������
 *
 * @package Eresus
 */
class AdminList {
	var $columns = array();
	var $head = array();
	var $body = array();
	var $__controls = array(
		'add'           => array('image' => 'admin/themes/default/img/medium/item-add.png', 'title' => strAdd, 'alt' => '+'),
		'edit' 	        => array('image' => 'admin/themes/default/img/medium/item-edit.png', 'title' => strEdit, 'alt' => '&plusmn;'),
		'delete'        => array('image' => 'admin/themes/default/img/medium/item-delete.png', 'title' => strDelete, 'alt' => 'X', 'onclick' => 'return askdel(this)'),
		'setup'         => array('image' => 'admin/themes/default/img/medium/item-config.png', 'title' => strProperties, 'alt' => '*'),
		'move'          => array('image' => 'admin/themes/default/img/medium/item-move.png', 'title' => strMove, 'alt' => '-&gt;'),
		'on'            => array('image' => 'admin/themes/default/img/medium/item-inactive.png', 'title' => admActivate, 'alt' => '0'),
		'off'           => array('image' => 'admin/themes/default/img/medium/item-active.png', 'title' => admDeactivate, 'alt' => '1'),
		'position'      => array('image' => 'admin/themes/default/img/medium/move-up.png', 'title' => admUp, 'alt' => '&uarr;'),
		'position_down' => array('image' => 'admin/themes/default/img/medium/move-down.png', 'title' => admDown, 'alt' => '&darr;'),
		);
	/**
	 * ������������ ������� ����������
	 *
	 * @param string $type    ��� �� (delete,toggle,move,custom...)
	 * @param string $href    ������
	 * @param string $custom  �������������� ���������
	 * @return  string  ������������ ��
	 */
	function control($type, $href, $custom = array())
	{
		global $Eresus;

		if (isset($this->__controls[$type])) $control = $this->__controls[$type];
		switch($type) {
			case 'position':
				$s = array_pop($href);
				$href = $href[0];
			break;
		}
		foreach($custom as $key => $value) $control[$key] = $value;
		$result = '<a href="'.$href.'"'.(isset($control['onclick'])?' onclick="'.$control['onclick'].'"':'').'><img src="'.$Eresus->root.$control['image'].'" alt="'.$control['alt'].'" title="'.$control['title'].'" /></a>';
		if ($type == 'position') $result .= ' '.$this->control('position_down', $s, $custom);
		return $result;
	}
	//------------------------------------------------------------------------------
 /**
	* ������������� �������� � ��������� ��������
	*
	* @access public
	*
	* @param  string  $text  ��������� �������
	* ���
	* @param  array   $cell  �������� �������
	*/
	function setHead()
	{
		$this->head = array();
		$items = func_get_args();
		if (count($items)) foreach($items as $item) {
			if (is_string($item)) $this->head[] = array('text' => $item);
			elseif (is_array($item)) $this->head[] = $item;
		}
	}
	//------------------------------------------------------------------------------
	/**
	* ������������� ��������� �������
	*
	* ���������������� ������ ��������� ��������� � $params
	*
	* @access public
	*
	* @param  int    $index  ����� �������
	* @param  array  $params �������� �������
	*/
	function setColumn($index, $params)
	{
		if (isset($this->columns[$index])) $this->columns[$index] = array_merge($this->columns[$index], $params);
		else $this->columns[$index] = $params;
	}
	//------------------------------------------------------------------------------
	/**
	* ��������� ������ � �������
	*
	* @access public
	*
	* @param  array  $cells  ������ ������
	*/
	function addRow($cells)
	{
		for($i=0; $i < count($cells); $i++) {
			if (!is_array($cells[$i])) $cells[$i] = array('text' => $cells[$i]);
			if (!isset($cells[$i]['text']) && isset($cells[$i][0])) {
				$cells[$i]['text'] = $cells[$i][0];
				unset($cells[$i][0]);
			}
		}
		$this->body[] = $cells;
	}
	//------------------------------------------------------------------------------
	/**
	* ��������� ������ � �������
	*
	* @access public
	*
	* @param  array  $rows  ������
	*/
	function addRows($rows)
	{
		for($i=0; $i < count($rows); $i++) $this->addRow($rows[$i]);
	}
	//------------------------------------------------------------------------------
	/**
	* ������������ ������ �������
	*
	* @access  private
	*/
	function renderCell($tag, $cell)
	{
		$style = '';
		$text= isset($cell['text']) ? $cell['text'] : '';
		if (isset($cell['href'])) $text = '<a href="'.$cell['href'].'">'.$text.'</a>';
		if (isset($cell['align'])) $style .= 'text-align: '.$cell['align'].';';
		if (isset($cell['style'])) $style .= $cell['style'];
		$result = '<'.$tag.(empty($style)?'':" style=\"$style\"").'>'.$text.'</'.$tag.'>';
		return $result;
	}
	//------------------------------------------------------------------------------
	/**
	* ������������ �������
	*
	* @return  string  HTML-��� �������
	*/
	function render()
	{
		$thead = '';
		foreach($this->head as $cell) $thead .= $this->renderCell('th', $cell);
		$tbody = array();
		foreach($this->body as $row) {
			$cells = '<tr>';
			foreach($row as $cell) $cells .= $this->renderCell('td', $cell);
			$cells .= '</tr>';
			$tbody[] = $cells;
		}
		$table = '<table class="admList">';
		$table .= '<tr>'.$thead.'</tr>';
		$table .= implode("\n", $tbody);
		$table .= '</table>';
		return $table;
	}
	//------------------------------------------------------------------------------
}
