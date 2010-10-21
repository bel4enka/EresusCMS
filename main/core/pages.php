<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * @package EresusCMS
 *
 * $Id$
 */

/**
 * ���������� ��������� �����
 *
 * @package EresusCMS
 */
class TPages
{
	/**
	 * ������� ������� � ����� ������
	 * @var int
	 */
	public $access = ADMIN;

	/**
	 * ???
	 * @var unknown_type
	 */
	public $cache;

	/**
	 * ???
	 * @param unknown_type $new
	 * @param unknown_type $old
	 * @return unknown_type
	 */
	function notifyMessage($new, $old=null)
	{
		$result = '';
		if (is_null($old)) {
			$result .= admPagesName.": ".$new['name']."\n";
			$result .= admPagesTitle.": ".$new['title']."\n";
			$result .= admPagesCaption.": ".$new['caption']."\n";
			$result .= admPagesHint.": ".$new['hint']."\n";
			$result .= admPagesDescription.": ".$new['description']."\n";
			$result .= admPagesKeywords.": ".$new['keywords']."\n";
			$result .= admPagesActive.": ".(isset($new['active'])&&$new['active']?strYes:strNo)."\n";
			$result .= admPagesVisible.": ".(isset($new['visible'])&&$new['visible']?strYes:strNo)."\n";
			$result .= admAccessLevel.": ".option('ACCESSLEVEL'.$new['access'])."\n";
			$result .= admPagesTemplate.": ".$new['template']."\n";
			$result .= admPagesContentType.": ".$new['type']."\n";
			$result .= admPagesOptions.": ".$new['options']."\n";
		} else {
			$result = "ID ".$new['id']." - <strong>".$old['caption']."</strong>\n".admChanges.":\n";
			if ($new['name'] != $old['name']) $result .= admPagesName.": ".$old['name']." &rarr; ".$new['name']."\n";
			if ($new['title'] != $old['title']) $result .= admPagesTitle.": ".$old['title']." &rarr; ".$new['title']."\n";
			if ($new['caption'] != $old['caption']) $result .= admPagesCaption.": ".$old['caption']." &rarr; ".$new['caption']."\n";
			if ($new['hint'] != $old['hint']) $result .= admPagesHint.": ".$old['hint']." &rarr; ".$new['hint']."\n";
			if ($new['description'] != $old['description']) $result .= admPagesDescription.": ".$old['description']." &rarr; ".$new['description']."\n";
			if ($new['keywords'] != $old['keywords']) $result .= admPagesKeywords.": ".$old['keywords']." &rarr; ".$new['keywords']."\n";
			if ($new['active'] != $old['active']) $result .= admPagesActive.": ".($old['active']?strYes:strNo)." &rarr; ".($new['active']?strYes:strNo)."\n";
			if ($new['visible'] != $old['visible']) $result .= admPagesVisible.": ".($old['visible']?strYes:strNo)." &rarr; ".($new['visible']?strYes:strNo)."\n";
			if ($new['access'] != $old['access']) $result .= admAccessLevel.": ".option('ACCESSLEVEL'.$old['access'])." &rarr; ".option('ACCESSLEVEL'.$new['access'])."\n";
			if ($new['template'] != $old['template']) $result .= admPagesTemplate.": ".$old['template']." &rarr; ".$new['template']."\n";
			if ($new['type'] != $old['type']) $result .= admPagesContentType.": ".$old['type']." &rarr; ".$new['type']."\n";
			if ($new['content'] != $old['content']) $result .= admPagesContent.": ".$old['content']." &rarr; ".$new['content']."\n";
			if ($new['created'] != $old['created']) $result .= admPagesCreated.": ".$old['created']." &rarr; ".$new['created']."\n";
			if ($new['updated'] != $old['updated']) $result .= admPagesUpdated.": ".$old['updated']." &rarr; ".$new['updated']."\n";
			if ($new['options'] != $old['options']) $result .= admPagesOptions.": ".$old['options']." &rarr; ".$new['options']."\n";
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������ ����� �������� � ��
	 * @return unknown_type
	 */
	function insert()
	{
		global $Eresus, $page;

		$item = array();
		$item['owner'] = arg('owner', 'int');
		$item['name'] = arg('name', '/[^a-z0-9_]/i');
		$item['title'] = arg('title', 'dbsafe');
		$item['caption'] = arg('caption', 'dbsafe');
		$item['description'] = arg('description', 'dbsafe');
		$item['hint'] = arg('hint', 'dbsafe');
		$item['keywords'] = arg('keywords', 'dbsafe');
		$item['template'] = arg('template', 'dbsafe');
		$item['type'] = arg('type', 'dbsafe');
		$item['active'] = arg('active', 'int');
		$item['visible'] = arg('visible', 'int');
		$item['access'] = arg('access', 'int');
		$item['position'] = arg('position', 'int');
		$item['options'] = arg('options');
		$item['created'] = $item['updated'] = gettime('Y-m-d H:i:s');

		$temp = $Eresus->sections->get("(`name`='" . $item['name'] . "') AND (`owner`='" .
			$item['owner'] . "')");
		if (count($temp) == 0)
		{
			$item = $Eresus->sections->add($item);
			dbReorderItems('pages', "`owner`='".arg('owner', 'int')."'");
			HTTP::redirect($page->url(array('id'=>'')));
		}
		else
		{
			ErrorMessage(sprintf(errItemWithSameName, $item['name']));
			saveRequest();
			HTTP::redirect($Eresus->request['referer']);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return unknown_type
	 */
	function update()
	{
		global $Eresus, $page;

		$old = $Eresus->sections->get(arg('update', 'int'));
		$item = $old;

		if (arg('id'))
		{
			$item['id'] = arg('id', 'int');
		}

		$item['name'] = arg('name', '/[^a-z0-9_]/i');
		$item['title'] = arg('title', 'dbsafe');
		$item['caption'] = arg('caption', 'dbsafe');
		$item['description'] = arg('description', 'dbsafe');
		$item['hint'] = arg('hint', 'dbsafe');
		$item['keywords'] = arg('keywords', 'dbsafe');
		$item['template'] = arg('template', 'dbsafe');
		$item['type'] = arg('type', 'dbsafe');
		$item['active'] = arg('active', 'int');
		$item['visible'] = arg('visible', 'int');
		$item['access'] = arg('access', 'int');
		$item['position'] = arg('position', 'int');
		$item['options'] = text2array(arg('options'), true);
		if (arg('created'))
		{
			$item['created'] = arg('created', 'dbsafe');
		}
		$item['updated'] = arg('updated', 'dbsafe');
		if (arg('updatedAuto'))
		{
			$item['updated'] = gettime('Y-m-d H:i:s');
		}

		$Eresus->sections->update($item);

		HTTP::redirect(arg('submitURL'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @param $skip
	 * @param $owner
	 * @param $level
	 * @return unknown_type
	 */
	function selectList($skip=0, $owner = 0, $level = 0)
	{
		global $Eresus;

		$items = $Eresus->sections->children($owner, $Eresus->user['access']);
		$result = array(array(), array());
		foreach ($items as $item)
		{
			if ($item['id'] != $skip)
			{
				$item['caption'] = trim($item['caption']);
				if (empty($item['caption']))
				{
					$item['caption'] = admNA;
				}
				$result[0][] = $item['id'];
				$result[1][] = str_repeat('&nbsp;', $level*2).$item['caption'];
				$children = $this->selectList($skip, $item['id'], $level+1);
				$result[0] = array_merge($result[0], $children[0]);
				$result[1] = array_merge($result[1], $children[1]);
			}
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������� ���������� �������� ����� � ������
	 * @return unknown_type
	 */
	function moveUp()
	{
		global $Eresus, $page;

		$item = $Eresus->sections->get(arg('id', 'int'));
		dbReorderItems('pages', "`owner`='".$item['owner']."'");
		$item = $Eresus->sections->get(arg('id', 'int'));
		if ($item['position'] > 0)
		{
			$temp = $Eresus->sections->get("(`owner`='".$item['owner']."') AND (`position`='".
				($item['position']-1)."')");
			if (count($temp))
			{
				$temp = $temp[0];
				$item['position']--;
				$temp['position']++;
				$Eresus->sections->update($item);
				$Eresus->sections->update($temp);
			}
		}
		HTTP::redirect($page->url(array('id'=>'')));
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������� ���������� �������� ���� � ������
	 * @return unknown_type
	 */
	function moveDown()
	{
		global $Eresus, $page;

		$item = $Eresus->sections->get(arg('id', 'int'));
		dbReorderItems('pages', "`owner`='".$item['owner']."'");
		$item = $Eresus->sections->get(arg('id', 'int'));
		if ($item['position'] < count($Eresus->sections->children($item['owner'])))
		{
			$temp = $Eresus->sections->get("(`owner`='".$item['owner']."') AND (`position`='".
				($item['position']+1)."')");
			if ($temp)
			{
				$temp = $temp[0];
				$item['position']++;
				$temp['position']--;
				$Eresus->sections->update($item);
				$Eresus->sections->update($temp);
			}
		}
		HTTP::redirect($page->url(array('id'=>'')));
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� �������� �� ����� ����� � ������
	 * @return unknown_type
	 */
	function move()
	{
		global $Eresus, $page;

		$item = $Eresus->sections->get(arg('id', 'int'));
		if (!is_null(arg('to')))
		{
			dbReorderItems('pages', "`owner`='".$item['owner']."'");
			$item['owner'] = arg('to', 'int');
			$item['position'] = count($Eresus->sections->children($item['owner']));
			$Eresus->sections->update($item);
			HTTP::redirect($page->url(array('id'=>'')));
		}
		else
		{
			$select = $this->selectList($item['id']);
			array_unshift($select[0], 0);
			array_unshift($select[1], admPagesRoot);
			$form = array(
				'name' => 'MoveForm',
				'caption' => admPagesMove,
				'fields' => array(
					array('type'=>'hidden', 'name'=>'mod', 'value' => 'pages'),
					array('type'=>'hidden', 'name'=>'action', 'value' => 'move'),
					array('type'=>'hidden', 'name'=>'id', 'value' => $item['id']),
					array('type'=>'select', 'label'=>strMove.' "<b>'.$item['caption'].'</b>" �',
						'name'=>'to', 'items'=>$select[1], 'values'=>$select[0], 'value' => $item['owner']),
				),
				'buttons' => array('ok', 'cancel'),
			);
			$result = $page->renderForm($form);
			return $result;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @param unknown_type $id
	 * @return unknown_type
	 */
	function deleteBranch($id)
	{
		global $Eresus;

		$item = $Eresus->db->selectItem('pages', "`id`='".$id."'");
		if ($Eresus->plugins->load($item['type']))
		{
			if (isset($Eresus->plugins->items[$item['type']]->table))
			{
				$fields = $Eresus->db->fields($Eresus->plugins->items[$item['type']]->table['name']);
				if (in_array('section', $fields))
				{
					$Eresus->db->delete($Eresus->plugins->items[$item['type']]->table['name'],
						"`section`='".$item['id']."'");
				}
			}
		}
		$items = $Eresus->db->select('`pages`', "`owner`='".$id."'", '', '`id`');
		if (count($items))
		{
			foreach ($items as $item)
			{
				$this->deleteBranch($item['id']);
			}
		}
		$Eresus->db->delete('pages', "`id`='".$id."'");
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������� ��������
	 * @return unknown_type
	 */
	function delete()
	{
		global $Eresus, $page;

		$item = $Eresus->sections->get(arg('id', 'int'));
		$Eresus->sections->delete(arg('id', 'int'));
		dbReorderItems('pages', "`owner`='".$item['owner']."'");
		HTTP::redirect($page->url(array('id'=>'')));
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������ ����� �������� � ����, ��������� ��� ���������� ����������� ������
	 *
	 * @return array
	 */
	private function loadContentTypes()
	{
		global $Eresus;

		$result[0] = array();
		$result[1] = array();

		/*
		 * ����������� ���� ��������
		 */
		$result[0] []= admPagesContentDefault;
		$result[1] []= 'default';

		$result[0] []= admPagesContentList;
		$result[1] []= 'list';

		$result[0] []= admPagesContentURL;
		$result[1] []= 'url';

		/*
		 * ���� ��������� �� ��������
		 */
		if (count($Eresus->plugins->items))
		{
			foreach ($Eresus->plugins->items as $plugin)
			{
				if (
					$plugin instanceof ContentPlugin ||
					$plugin instanceof TContentPlugin
				)
				{
					$result[0][] = $plugin->title;
					$result[1][] = $plugin->name;
				}
			}
		}

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return unknown_type
	 */
	function loadTemplates()
	{
		$result[0] = array();
		$result[1] = array();
		useLib('templates');
		$templates = new Templates();
		$list = $templates->enum();
		$result[0]= array_values($list);
		$result[1]= array_keys($list);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������� ������� ����� ��� ���������� ����� ��������
	 * @return unknown_type
	 */
	function create()
	{
		global $Eresus, $page;

		$content = $this->loadContentTypes();
		$templates = $this->loadTemplates();
		restoreRequest();
		$form = array (
			'name' => 'createPage',
			'caption' => strAdd,
			'width' => '600px',
			'fields' => array (
				array ('type' => 'hidden','name'=>'owner','value'=>arg('owner', 'int')),
				array ('type' => 'hidden','name'=>'action', 'value'=>'insert'),
				array ('type' => 'edit','name' => 'name','label' => admPagesName,'width' => '150px',
					'maxlength' => '32', 'pattern'=>'/^[a-z0-9_]+$/i', 'errormsg'=>admPagesNameInvalid),
				array ('type' => 'edit','name' => 'title','label' => admPagesTitle,'width' => '100%',
					'pattern'=>'/.+/', 'errormsg'=>admPagesTitleInvalid),
				array ('type' => 'edit','name' => 'caption','label' => admPagesCaption,'width' => '100%',
					'maxlength' => '64', 'pattern'=>'/.+/', 'errormsg'=>admPagesCaptionInvalid),
				array ('type' => 'edit','name' => 'hint','label' => admPagesHint,'width' => '100%'),
				array ('type' => 'edit','name' => 'description','label' => admPagesDescription,
					'width' => '100%'),
				array ('type' => 'edit','name' => 'keywords','label' => admPagesKeywords,'width' => '100%'),
				array ('type' => 'select','name' => 'template','label' => admPagesTemplate,
					'items' => $templates[0], 'values' => $templates[1], 'default'=>pageTemplateDefault),
				array ('type' => 'select','name' => 'type','label' => admPagesContentType,
					'items' => $content[0], 'values' => $content[1], 'default'=>contentTypeDefault),
				array ('type' => 'checkbox','name' => 'active','label' => admPagesActive, 'default'=>true),
				array ('type' => 'checkbox','name' => 'visible','label' => admPagesVisible,
					'default'=>true),
				array ('type' => 'select','name' => 'access','label' => admAccessLevel,'access' => ADMIN,
					'values'=>array(ADMIN,EDITOR,USER,GUEST),
					'items' => array (ACCESSLEVEL2,ACCESSLEVEL3,ACCESSLEVEL4,ACCESSLEVEL5),
					'default' => GUEST),
				array ('type' => 'edit','name' => 'position','label' => admPosition,'access' => ADMIN,
					'width' => '4em','maxlength' => '5'),
				array ('type' => 'memo','name' => 'options','label' => admPagesOptions,'height' => '5')
			),
			'buttons' => array('ok', 'cancel'),
		);

		$result = $page->renderForm($form, $Eresus->request['arg']);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������ ��������� ������� �������
	 *
	 * @param int $id
	 * @return string  HTML
	 */
	private function edit($id)
	{
		global $Eresus, $page;

		$item = $Eresus->sections->get($id);
		$content = $this->loadContentTypes();
		$templates = $this->loadTemplates();
		$item['options'] = array2text($item['options'], true);
		$form['caption'] = $item['caption'];
		# ��������� ����� ��������
		$urlAbs = $page->clientURL($item['id']);

		$form = array(
			'name' => 'PageForm',
			'caption' => $item['caption'].' ('.$item['name'].')',
			'width' => '700px',
			'fields' => array (
				array ('type' => 'hidden','name' => 'update', 'value'=>$item['id']),
				array ('type' => 'edit','name' => 'id','label' => admPagesID,'width' => '50px',
					'maxlength' => '5', 'access'=>ROOT),
				array ('type' => 'edit','name' => 'name','label' => admPagesName,'width' => '150px',
					'maxlength' => '32', 'pattern'=>'/[a-z0-9_]+/i', 'errormsg'=>admPagesNameInvalid),
				array ('type' => 'edit','name' => 'title','label' => admPagesTitle,'width' => '100%',
					'pattern'=>'/.+/', 'errormsg'=>admPagesTitleInvalid),
				array ('type' => 'edit','name' => 'caption','label' => admPagesCaption,
					'width' => '100%','maxlength' => '64', 'pattern'=>'/.+/',
					'errormsg'=>admPagesCaptionInvalid),
				array ('type' => 'edit','name' => 'hint','label' => admPagesHint,'width' => '100%'),
				array ('type' => 'edit','name' => 'description','label' => admPagesDescription,
					'width' => '100%'),
				array ('type' => 'edit','name' => 'keywords','label' => admPagesKeywords,'width' => '100%'),
				array ('type' => 'select','name' => 'template','label' => admPagesTemplate,
					'items' => $templates[0], 'values' => $templates[1]),
				array ('type' => 'select','name' => 'type','label' => admPagesContentType,
					'items' => $content[0], 'values' => $content[1]),
				array ('type' => 'checkbox','name' => 'active','label' => admPagesActive),
				array ('type' => 'checkbox','name' => 'visible','label' => admPagesVisible),
				array ('type' => 'select','name' => 'access','label' => admAccessLevel,'access' => ADMIN,
					'values'=>array(ADMIN,EDITOR,USER,GUEST),
					'items' => array (ACCESSLEVEL2,ACCESSLEVEL3,ACCESSLEVEL4,ACCESSLEVEL5)),
				array ('type' => 'edit','name' => 'position','label' => admPosition,'access' => ADMIN,
					'width' => '4em','maxlength' => '5'),
				array ('type' => 'memo','name' => 'options','label' => admPagesOptions,'height' => '5'),
				array ('type' => 'edit','name' => 'created','label' => admPagesCreated,'access' => ADMIN,
					'width' => '10em','maxlength' => '19'),
				array ('type' => 'edit','name' => 'updated','label' => admPagesUpdated,'access' => ADMIN,
					'width' => '10em','maxlength' => '19'),
				array ('type' => 'checkbox','name' => 'updatedAuto','label' => admPagesUpdatedAuto,
					'default' => true),
				array ('type' => 'text',
					'value'=>admPagesThisURL.': <a href="'.$urlAbs.'">'.$urlAbs.'</a>'),
			),
			'buttons' => array('ok', 'apply', 'cancel'),
		);
		$result = $page->renderForm($form, $item);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������������ ��������� �������
	 *
	 * @param  int  $owner  ������������ ������
	 * @param  int  $level  ������� �����������
	 *
	 * @return  string  ������������ ����� �������
	 */
	function sectionIndexBranch($owner=0, $level=0)
	{
		global $Eresus;

		$result = array();
		$items = $Eresus->sections->children($owner,
			$Eresus->user['auth'] ? $Eresus->user['access'] : GUEST);
		for ($i=0; $i<count($items); $i++)
		{
			$content_type = isset($this->cache['content_types'][$items[$i]['type']]) ?
				$this->cache['content_types'][$items[$i]['type']] :
				'<span class="admError">'.sprintf(errContentType, $items[$i]['type']).'</span>';
			$row = array();
			$row[] = array('text' => $items[$i]['caption'], 'style'=>"padding-left: {$level}em;",
				'href'=>$Eresus->root.'admin.php?mod=content&amp;section='.$items[$i]['id']);
			$row[] = $items[$i]['name'];
			$row[] = array('text' => $content_type, 'align' => 'center');
			$row[] = array('text' => constant('ACCESSLEVEL'.$items[$i]['access']), 'align' => 'center');
			$row[] = sprintf($this->cache['index_controls'], $items[$i]['id'], $items[$i]['id'],
				$items[$i]['id'], $items[$i]['id'], $items[$i]['id'], $items[$i]['id']);
			$result[] = $row;
			$children = $this->sectionIndexBranch($items[$i]['id'], $level+1);
			if (count($children))
			{
				$result = array_merge($result, $children);
			}
		}
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ???
	 * @return unknown_type
	 */
	function sectionIndex()
	{
		global $Eresus, $page;

		$root = $Eresus->root.'admin.php?mod=pages&amp;';
		$this->cache['index_controls'] =
			$page->control('setup', $root.'id=%d').' '.
			$page->control('position', array($root.'action=up&amp;id=%d',$root.'action=down&amp;id=%d')).
			' '.
			$page->control('add', $root.'action=create&amp;owner=%d').' '.
			$page->control('move', $root.'action=move&amp;id=%d').' '.
			$page->control('delete', $root.'action=delete&amp;id=%d');
		$types = $this->loadContentTypes();
		for ($i=0; $i<count($types[0]); $i++)
		{
			$this->cache['content_types'][$types[1][$i]] = $types[0][$i];
		}
		useLib('admin/lists');
		$table = new AdminList;
		$table->setHead(array('text'=>'������', 'align'=>'left'), '���', '���', '������', '');
		$table->addRow(array(admPagesRoot, '', '', '',
			array($page->control('add', $root.'action=create&amp;owner=0'), 'align' => 'center')));
		$table->addRows($this->sectionIndexBranch(0, 1));
		$result = $table->render();
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return unknown_type
	 */
	function adminRender()
	{
		global $Eresus, $page;

		if (UserRights($this->access))
		{
			$result = '';
			if (arg('update'))
			{
				$this->update();
			}
			elseif (arg('action'))
			{
				switch(arg('action')) {
					case 'up':
						$this->moveUp();
					break;
					case 'down':
						$this->moveDown();
					break;
					case 'create':
						$result = $this->create();
					break;
					case 'insert':
						$this->insert();
					break;
					case 'move':
						$result = $this->move();
					break;
					case 'delete':
						$this->delete();
					break;
				}
			}
			elseif (isset($Eresus->request['arg']['id']))
			{
				$result = $this->edit(arg('id', 'int'));
			}
			else
			{
				$result = $this->sectionIndex();
			}
			return $result;
		}
	}
	//-----------------------------------------------------------------------------
}