<?php
/**
 * Blocks
 *
 * Eresus 2, PHP 4.3.0
 *
 * Система управления текстовыми блоками
 *
 * @version 2.04
 *
 * @copyright   2005-2006, ProCreat Systems, http://procreat.ru/
 * @copyright   2007-2008, Eresus Group, http://eresus.ru/
 * @license     http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @maintainer  Mikhail Krasilnikov <mk@procreat.ru>
 * @author      Mikhail Krasilnikov <mk@procreat.ru>
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
 */

useClass('TListContentPlugin');

class TBlocks extends TListContentPlugin {
	var $name = 'blocks';
	var $version = '2.04b';
	var $kernel = '2.10rc';
	var $title = 'Блоки';
	var $description = 'Система управления текстовыми блоками';
	var $type = 'client,content,admin';
	var $table = array (
		'name' => 'blocks',
		'key'=> 'id',
		'sortMode' => 'id',
		'sortDesc' => false,
		'columns' => array(
			array('name' => 'caption', 'caption' => 'Название'),
			array('name' => 'block', 'caption' => 'Блок', 'align'=> 'right'),
			array('name' => 'priority', 'caption' => '<span title="Приоритет" style="cursor: default;">&nbsp;&nbsp;*</span>', 'align'=>'center'),
		),
		'controls' => array (
			'delete' => '',
			'edit' => '',
			'toggle' => '',
		),
		'tabs' => array(
			'width'=>'180px',
			'items'=>array(
			 array('caption'=>'Добавить блок', 'name'=>'action', 'value'=>'create')
			),
		),
		'sql' => "(
			`id` int(10) unsigned NOT NULL auto_increment,
			`caption` varchar(255) default NULL,
			`active` tinyint(1) unsigned default NULL,
			`section` varchar(255) default NULL,
			`priority` int(10) unsigned default NULL,
			`block` varchar(31) default NULL,
			`target` varchar(63) default NULL,
			`content` text,
			PRIMARY KEY  (`id`),
			KEY `active` (`active`),
			KEY `section` (`section`),
			KEY `block` (`block`),
			KEY `target` (`target`)
		) TYPE=MyISAM COMMENT='Content blocks';",
	);
 /**
	* Конструктор
	*
	* @return TBlocks
	*/
	function TBlocks()
	{
		global $Eresus;

		parent::TListContentPlugin();
		if (defined('CLIENTUI')) {
			$Eresus->plugins->events['clientOnContentRender'][] = $this->name;
			$Eresus->plugins->events['clientOnPageRender'][] = $this->name;
		} else $Eresus->plugins->events['adminOnMenuRender'][] = $this->name;
	}
	//-----------------------------------------------------------------------------
	function menuBranch($owner = 0, $level = 0)
	{
		global $Eresus;

		$result = array(array(), array());
		$items = $Eresus->sections->children($owner, $Eresus->user['auth'] ? $Eresus->user['access'] : GUEST);
		if (count($items)) foreach($items as $item) {
			$result[0][] = str_repeat('- ', $level).$item['caption'];
			$result[1][] = $item['id'];
			$sub = $this->menuBranch($item['id'], $level+1);
			if (count($sub[0])) {
				$result[0] = array_merge($result[0], $sub[0]);
				$result[1] = array_merge($result[1], $sub[1]);
			}
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
	function insert()
	{
		global $Eresus;

		$item = GetArgs($Eresus->db->fields($this->table['name']));
		if (isset($item['section'])) $item['section'] = ($item['section'] != 'all')?':'.implode(':', arg('section')).':':'all';
		$item['active'] = true;
		$Eresus->db->insert($this->table['name'], $item);
		sendNotify('Добавлен блок: '.$item['caption']);
		goto(arg('submitURL'));
	}
	//-----------------------------------------------------------------------------
	function update()
	{
		global $Eresus;

		$item = $Eresus->db->selectItem($this->table['name'], "`id`='".arg('update', 'int')."'");
		$item = GetArgs($item);
		$item['section'] = ($item['section'] != 'all')?':'.implode(':', arg('section')).':':'all';
		$db->updateItem($this->table['name'], $item, "`id`='".arg('update', 'int')."'");
		$item = $db->selectItem($this->table['name'], "`id`='".arg('update', 'int')."'");
		sendNotify('Изменен блок: '.$item['caption']);
		goto(arg('submitURL'));
	}
	//-----------------------------------------------------------------------------
	function toggle($id)
	{
		global $Eresus, $page;

		$item = $Eresus->db->selectItem($this->table['name'], "`id`='".$id."'");
		$item['active'] = !$item['active'];
		$Eresus->db->updateItem($this->table['name'], $item, "`id`='".$id."'");
		sendNotify(($item['active']?admActivated:admDeactivated).': '.'<a href="'.str_replace('toggle','id',$Eresus->request['url']).'">'.$item['caption'].'</a>', array('title'=>$this->title));
		goto($page->url());
	}
	//-----------------------------------------------------------------------------
	function create()
	{
		global $page;

		$sections = array(array(), array());
		$sections = $this->menuBranch();
		array_unshift($sections[0], 'ВСЕ РАЗДЕЛЫ');
		array_unshift($sections[1], 'all');
		$form = array(
			'name' => 'formCreate',
			'caption' => 'Добавить блок',
			'width' => '95%',
			'fields' => array (
				array ('type'=>'hidden','name'=>'action', 'value'=>'insert'),
				array ('type' => 'edit', 'name' => 'caption', 'label' => 'Заголовок', 'width' => '100%', 'maxlength' => '255', 'pattern'=>'/.+/', 'errormsg'=>'Заголовок не может быть пустым!'),
				array ('type' => 'listbox', 'name' => 'section', 'label' => 'Разделы', 'height'=> 5,'items'=>$sections[0], 'values'=>$sections[1]),
				array ('type' => 'edit', 'name' => 'priority', 'label' => 'Приоритет', 'width' => '20px', 'comment' => 'Большие значения - больший приоритет', 'value'=>0, 'pattern'=>'/\d+/', 'errormsg'=>'Приоритет задается только цифрами!'),
				array ('type' => 'edit', 'name' => 'block', 'label' => 'Блок', 'width' => '100px', 'maxlength' => 31),
				array ('type' => 'select', 'name' => 'target', 'label' => 'Область', 'items' => array('Отрисованная страница','Шаблон страницы'), 'values' => array('page','template')),
				array ('type' => 'html', 'name' => 'content', 'label' => 'Содержимое', 'height' => '300px'),
			),
			'buttons' => array('ok', 'cancel'),
		);

		$result = $page->renderForm($form);
		return $result;
	}
	//-----------------------------------------------------------------------------
	function edit()
	{
		global $Eresus, $page;

		$item = $Eresus->db->selectItem($this->table['name'], "`id`='".arg('id','int')."'");
		$item['section'] = explode(':', $item['section']);
		$sections = array(array(), array());
		$sections = $this->menuBranch();
		array_unshift($sections[0], 'ВСЕ РАЗДЕЛЫ');
		array_unshift($sections[1], 'all');
		$form = array(
			'name' => 'formEdit',
			'caption' => 'Изменить блок',
			'width' => '95%',
			'fields' => array (
				array ('type' => 'hidden','name'=>'update', 'value'=>$item['id']),
				array ('type' => 'edit', 'name' => 'caption', 'label' => 'Заголовок', 'width' => '100%', 'maxlength' => '255', 'pattern'=>'/.+/', 'errormsg'=>'Заголовок не может быть пустым!'),
				array ('type' => 'listbox', 'name' => 'section', 'label' => 'Разделы', 'height'=> 5,'items'=>$sections[0], 'values'=>$sections[1]),
				array ('type' => 'edit', 'name' => 'priority', 'label' => 'Приоритет', 'width' => '20px', 'comment' => 'Большие значения - больший приоритет', 'default'=>0, 'pattern'=>'/\d+/', 'errormsg'=>'Приоритет задается только цифрами!'),
				array ('type' => 'edit', 'name' => 'block', 'label' => 'Блок', 'width' => '100px', 'maxlength' => 31),
				array ('type' => 'select', 'name' => 'target', 'label' => 'Область', 'items' => array('Отрисованная страница','Шаблон страницы'), 'values' => array('page','template')),
				array ('type' => 'html', 'name' => 'content', 'label' => 'Содержимое', 'height' => '300px'),
				array ('type' => 'checkbox', 'name' => 'active', 'label' => 'Активировать'),
			),
			'buttons' => array('ok', 'apply', 'cancel'),
		);

		$result = $page->renderForm($form, $item);
		return $result;
	}
	//-----------------------------------------------------------------------------
	function adminRender()
	{
		global $Eresus, $page;

		$result = '';
		if (arg('id')) {
			$item = $Eresus->db->selectItem($this->table['name'], "`".$this->table['key']."` = '".arg('id', 'int')."'");
			$page->title .= empty($item['caption'])?'':' - '.$item['caption'];
		}
		switch (true) {
			case arg('update') && isset($this->table['controls']['edit']): $result = $this->update(); break;
			case arg('toggle') && isset($this->table['controls']['toggle']): $result = $this->toggle(arg('toggle', 'int'));	break;
			case arg('delete') && isset($this->table['controls']['delete']): $result = $this->delete(arg('delete')); break;
			case arg('id') && isset($this->table['controls']['edit']): $result = $this->edit();	break;
			case arg('action'):
				switch (arg('action')) {
					case 'create': $result = $this->create(); break;
					case 'insert': $result = $this->insert();	break;
				}
			default: $result = $page->renderTable($this->table);
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
	function renderBlocks($source, $target)
	{
		global $Eresus, $page;

		preg_match_all('/\$\(Blocks:([^\)]+)\)/', $source, $blocks);
		foreach($blocks[1] as $block) {
			$sql = "(`active`=1) AND (`section` LIKE '%:".$page->id.":%' OR `section` = ':all:') AND (`block`='".$block."') AND (`target` = '".$target."')";
			$item = $Eresus->db->select($this->name, $sql, '`priority`', true);
			if (count($item)) $source = str_replace('$(Blocks:'.$block.')', trim($item[0]['content']), $source);
		}
		return $source;
	}
	//-----------------------------------------------------------------------------
	function adminOnMenuRender()
	{
		global $page;

		$page->addMenuItem(admExtensions, array ('access'  => EDITOR, 'link'  => $this->name, 'caption'  => $this->title, 'hint'  => $this->description));
	}
	//-----------------------------------------------------------------------------
	function clientOnContentRender($text)
	{
		global $page;
		$page->template = $this->renderBlocks($page->template, 'template');
		return $text;
	}
	//-----------------------------------------------------------------------------
	function clientOnPageRender($text)
	{
		$text = $this->renderBlocks($text, 'page');
		return $text;
	}
	//-----------------------------------------------------------------------------
}
?>