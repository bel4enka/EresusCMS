<?php
/**
 * Публикация файлов
 *
 * Eresus 2
 * 
 * Плагин обеспечивает публикуцию на сайте файлов, с разбивкой по категориям
 *
 * © 2007, Eresus Group, http://eresus.ru/
 *
 * @version: 1.00
 * @modified: 2007-09-21
 * 
 * @author: Mikhail Krasilnikov <mk@procreat.ru>
 */

class Files extends ContentPlugin {
  var $version = '1.00a';
  var $kernel = '2.10b2';
  var $title = 'Файлы';
  var $description = 'Публикация файлов';
  var $type = 'client,content,ondemand';
  var $settings = array(
  	'icons' => "catalog.gif=doc\nexcel.gif=xls",
  );
  /**
   * Инсталляция плагина
   */
  function install()
  {
    parent::install();
    $this->dbCreateTable("
			`id` int(10) unsigned NOT NULL auto_increment,
		  `section` int(10) unsigned default NULL,
		  `position` int(10) unsigned default 0,
		  `caption` varchar(127) default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `section` (`section`),
		  KEY `position` (`position`)
  	", 'sections');
    $this->dbCreateTable("
			`id` int(10) unsigned NOT NULL auto_increment,
		  `owner` int(10) unsigned default NULL,
		  `caption` varchar(255) default NULL,
		  `position` int(10) unsigned default 0,
		  `size` int(10) unsigned default 0,
		  `filename` varchar(255) default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `owner` (`owner`),
		  KEY `position` (`position`)
		", 'files');
    $this->mkdir();
  }
  //------------------------------------------------------------------------
  /**
   * Деинсталляция плагина
   *
   */
  function uninstall()
  {
  	$this->rmdir();
  	parent::uninstall();
  }
  //------------------------------------------------------------------------
  /**
   * Возвращает список разделов
   *
   * @param int $section Раздел сайта
   * 
   * @return array Список разделов
   */
  function sectionEnum($section)
  {
  	$result = $this->dbSelect('sections', "`section` = '$section'", 'position');
  	return $result;
  }
  //------------------------------------------------------------------------
  /**
   * Создаёт новый раздел
   *
   * @param int $section Раздел сайта
   * @param string $caption  Название раздела
   */
  function sectionCreate($section, $caption)
  {
  	$result = $this->dbSelect('sections', "`section` = '$section'", 'position');
  	$item = array(
  		'section' => $section,
  		'caption' => $caption,
  		'position' => count($result) ? $result[count($result)-1]['position'] + 1 : 0
  	);
  	$result = $this->dbInsert('sections', $item);
  	return $result;
  }
  //------------------------------------------------------------------------
  /**
   * Перемещает раздел вверх в списке
   *
   * @param int $id Идентификатор раздела
   */
  function sectionUp($id)
  {
  	$item = $this->dbItem('sections', $id);
  	if ($item['position']) {
  		$this->dbUpdate('sections', "`position` = `position` + 1", "`section`={$item['section']} AND `position` = ".($item['position']-1));
  		$item['position']--;
  		$this->dbUpdate('sections', $item);
  	}
  }
  //------------------------------------------------------------------------  
  /**
   * Перемещает раздел вниз в списке
   *
   * @param int $id Идентификатор раздела
   */
  function sectionDown($id)
  {
  	$item = $this->dbItem('sections', $id);
  	if ($item['position'] < $this->dbCount('sections', "`section`={$item['section']}") - 1) {
  		$this->dbUpdate('sections', "`position` = `position` - 1", "`section`={$item['section']} AND `position` = ".($item['position']+1));
  		$item['position']++;
  		$this->dbUpdate('sections', $item);
  	}
  }
  //------------------------------------------------------------------------  
  /**
   * Возвращает диалог добавления раздела
   *
   * @return string  Диалог добавления раздела
   */
  function sectionAddDialog()
  {
  	global $page;
  	
  	$form = array(
  		'name' => 'AddDialog',
  		'caption' => 'Новая категория',
  		'width' => '500px',
  		'fields' => array(
  			array('type' => 'hidden', 'name' => 'action', 'value' => 'section_insert'),
  			array('type' => 'edit', 'name' => 'caption', 'label' => 'Название', 'width' => '100%', 'maxlength' => 127),
  		),
  		'buttons' => array('ok', 'cancel'),
  	);
  	$result = $page->renderForm($form);
  	return $result;
  }
  //------------------------------------------------------------------------
  /**
   * Возвращает список файлов
   *
   * @param int $owner Идентификатор подраздела
   * 
   * @return array Список файлов
   */
  function filesEnum($owner)
  {
  	$result = $this->dbSelect('files', "`owner` = '$owner'", 'position');
  	return $result;
  }
  //------------------------------------------------------------------------  
  /**
   * Возвращает диалог добавления файла
   *
   * @param int $owner  Идентификатор родительского раздела
   * @return string  Диалог добавления файла
   */
  function fileAddDialog($owner)
  {
  	global $page;
  	
  	$form = array(
  		'name' => 'AddDialog',
  		'caption' => 'Новый файл',
  		'width' => '500px',
  		'fields' => array(
  			array('type' => 'hidden', 'name' => 'action', 'value' => 'file_insert'),
  			array('type' => 'hidden', 'name' => 'owner', 'value' => $owner),
  			array('type' => 'file', 'name' => 'file', 'label' => 'Файл', 'width' => 50, 'pattern' => '/.+/', 'errormsg' => 'Вы не выбрали файл!'),
  			array('type' => 'edit', 'name' => 'caption', 'label' => 'Название', 'width' => '100%', 'maxlength' => 255, 'pattern' => '/.+/', 'errormsg' => 'Название не может быть пустым!'),
  			),
  		'buttons' => array('ok', 'cancel'),
  	);
  	$result = $page->renderForm($form);
  	return $result;
  }
  //------------------------------------------------------------------------
  /**
   * Загружает новый файл
   */
  function fileAdd()
  {
  	$result = $this->dbSelect('files', "`owner` = '".arg('owner')."'", 'position');
  	$item = array(
  		'owner' => arg('owner'),
  		'position' => count($result) ? $result[count($result)-1]['position'] + 1 : 0,
  		'caption' => arg('caption'),
  		'size' => $_FILES['file']['size'],
  		'filename' => $_FILES['file']['name'],
  	);
  	$item['id'] = $this->dbInsert('files', $item);
  	if ($item['id']) {
  		if (upload('file', $this->dirData.$item['id'])) $result = true;
  		else {
  			$this->dbDelete('files', $item);
  			$result = false;
  		}
  	}
  	
  	return $result > 0;
  }
  //------------------------------------------------------------------------
  /**
   * Перемещает файл вверх в списке
   *
   * @param int $id Идентификатор файла
   */
  function fileUp($id)
  {
  	$item = $this->dbItem('files', $id);
  	if ($item['position']) {
  		$this->dbUpdate('files', "`position` = `position` + 1", "`owner`={$item['owner']} AND `position` = ".($item['position']-1));
  		$item['position']--;
  		$this->dbUpdate('files', $item);
  	}
  }
  //------------------------------------------------------------------------  
  /**
   * Перемещает файл вниз в списке
   *
   * @param int $id Идентификатор файла
   */
  function fileDown($id)
  {
  	$item = $this->dbItem('files', $id);
  	if ($item['position'] < $this->dbCount('files', "`owner`={$item['owner']}") - 1) {
  		$this->dbUpdate('files', "`position` = `position` - 1", "`owner`={$item['owner']} AND `position` = ".($item['position']+1));
  		$item['position']++;
  		$this->dbUpdate('files', $item);
  	}
  }
  //------------------------------------------------------------------------  
  /**
   * Удаляет файл
   *
   * @param int $id Идентификатор файла
   */
  function fileDelete($id)
  {
  	$item = $this->dbItem('files', $id);
  	filedelete($this->dirData.$item['id']);
  	$this->dbDelete('files', $item);
  	$this->dbUpdate('files', "`position` = `position` - 1", "`owner`={$item['owner']} AND `position` > ".($item['position']));
  }
  //------------------------------------------------------------------------  
  /**
   * Отрисовка списка файлов
   * 
   * @param int $section  Раздел сайта
   *
   * @return string Контент
   */
  function adminRenderList($section)
  {
  	global $page;
  	
		$result = '';
		$tabs = array(
			'items' => array(
				array('caption' => 'Добавить раздел', 'name' => 'action', 'value' => 'section_add'),
			),
		);
		$result .= $page->renderTabs($tabs);
		$table = array(
	    'key'=> 'id',
	    'sortMode' => 'position',
	    'sortDesc' => false,
	    'columns' => array(
	      array('name' => 'caption', 'caption' => 'Название'),
	      #array('name' => 'size', 'caption' => 'Размер', 'align' => 'right'),
	      array('name' => 'filename', 'caption' => 'Имя файла'),
	      ),
	    'controls' => array (
	      'delete' => '',
	      #'edit' => '',
	      'position' => '',
	    ),
		);
		$sections = $this->sectionEnum($section);
		for ($i=0; $i < count($sections); $i++) {
			$result .= "<br /><div><b>{$sections[$i]['caption']}</b>
				<a href=\"".$page->url(array('action' => 'file_add', 'owner' => $sections[$i]['id']))."\" title=\"Добавить файл в этот раздел\">[+]</a>
				<a href=\"".$page->url(array('action' => 'section_up', 'id' => $sections[$i]['id']))."\" title=\"Переместить выше\">[&uarr;]</a>
				<a href=\"".$page->url(array('action' => 'section_down', 'id' => $sections[$i]['id']))."\" title=\"Переместить ниже\">[&darr;]</a>
				<a href=\"".$page->url(array('action' => 'section_delete', 'id' => $sections[$i]['id']))."\" title=\"Удалить раздел и все его файлы\">[-]</a></div>";
			$files = $this->filesEnum($sections[$i]['id']);
			if (count($files)) $result .= $page->renderTable($table, $files, 'file_'); 
		}
		return $result;
  }
  //------------------------------------------------------------------------
  /**
	 * Отрисовка административной части
	 *
	 * @return  string  Контент
	 */
	function adminRenderContent()
	{
		global $page, $Eresus;

		$action = arg('action');
		if (arg('file_up')) $action = 'file_up';
		if (arg('file_down')) $action = 'file_down';
		if (arg('file_delete')) $action = 'file_delete';
		#if (arg('file_id')) $action = 'file_delete';
		
		switch ($action) {
			case 'section_add': $result = $this->sectionAddDialog(); break;
			case 'section_insert': $this->sectionCreate($page->id, arg('caption')); goto(arg('submitURL')); break;
			case 'section_up': $this->sectionUp(arg('id')); goto($Eresus->request['referer']); break;
			case 'section_down': $this->sectionDown(arg('id')); goto($Eresus->request['referer']); break;
			case 'file_add': $result = $this->fileAddDialog(arg('owner')); break;
			case 'file_insert': $this->fileAdd(); goto(arg('submitURL')); break;
			case 'file_up': $this->fileUp(arg('file_up')); goto($Eresus->request['referer']); break;
			case 'file_down': $this->fileDown(arg('file_down')); goto($Eresus->request['referer']); break;
			case 'file_delete': $this->fileDelete(arg('file_delete')); goto($Eresus->request['referer']); break;
			default:
				$result = $this->adminRenderList($page->id);
			break;
		}
		
		return $result;
	}
	//------------------------------------------------------------------------------
 /**
	 * Диалог настроек плагина
	 *
	 * @return string  Диалог настроек
	 */
	function settings()
	{
	  global $Eresus, $page;
	
	  $form = array(
	    'name'=>'SettingsForm',
	    'caption' => $this->title.' '.$this->version,
	    'width' => '500px',
	    'fields' => array (
	      array('type' => 'hidden', 'name' => 'update', 'value' => $this->name),
	      array('type' => 'memo', 'name' => 'icons', 'height' => '6', 'label' => 'Пиктограммы:'),
	      array('type' => 'text', 'value' => 'Каждая строка задаёт пиктограмму для списка расширений файлов.<br />Формат строки:<br /><b>&lt;файл пиктограммы&gt;=&lt;расширение 1&gt;,&lt;расширение N&gt;</b><br />Пример:<br /><b>image.png=png,jpeg,jpg,gif</b>'),
	      array('type' => 'text', 'value' => 'Пиктограммы должны находиться в директории <b>'.substr($this->dirStyle, strlen($Eresus->froot)-1).'</b>'),
	    ),
	    'buttons' => array('ok', 'apply', 'cancel'),
	  );
	  $result = $page->renderForm($form, $this->settings);
	  return $result;
	}	
	//------------------------------------------------------------------------------
	/**
	 * Отрисовка клиентского контента
	 *
	 * @return string  Отрисованный контент
	 */
	function clientRenderContent()
	{
		global $page;
  	
		$result = '';

		if ($page->topic) {
			$file = $this->dbItem('files', $page->topic);
			if ($file) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Length: '.$file['size']);
				header('Content-Disposition: attachment; filename="'.$file['filename'].'"');
				readfile($this->dirData.$file['id']);
				die;
			} else ErrorBox('Запрошенный файл не найден');
		}
		$items = explode("\n", $this->settings['icons']);
		$icons = array();
		for($i=0; $i<count($items); $i++) {
			$items[$i] = explode('=', trim($items[$i]));
			$items[$i][1] = explode(',', trim($items[$i][1]));
			foreach($items[$i][1] as $key) $icons[$key] = $items[$i][0];
		}
		$sections = $this->sectionEnum($page->id);
		for ($i=0; $i < count($sections); $i++) {
			$result .= "<h1>{$sections[$i]['caption']}</h1>\n";
			$files = $this->filesEnum($sections[$i]['id']);
			if (count($files)) {
				$result .= "<p>";
				for ($j = 0; $j < count($files); $j++) {
					$icon = strtolower(substr($files[$j]['filename'], strpos($files[$j]['filename'], '.')+1));
					$icon = isset($icons[$icon]) ? '<img src="'.$this->urlStyle.$icons[$icon].'" alt="" />' : ''; 
					$result .= '<a href="'.$files[$j]['id'].'/">'.$files[$j]['caption'].'</a> ('.FormatSize($files[$j]['size']).')'.$icon.'<br />';
				}
				$result .= "</p>";
			}
		}
		return $result;
	}
	//------------------------------------------------------------------------------
}

?>