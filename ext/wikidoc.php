<?php
/**
* WikiDoc - документация, редактируемая в стили wiki
*
* Плагин Eresus 2 (http://eresus.ru/)
*
* PHP 4.3.3
*
* @author: Mikhail Krasilnikov <mk@procreat.ru>
* @version: 1.03
* @modified: 2007-09-20
*/

class TWikidoc extends TListContentPlugin {
  var $name = 'wikidoc';
  var $type = 'client,content,ondemand';
  var $title = 'Wiki-документация';
  var $version = '1.03';
  var $description = 'Документация в стиле Wiki';
  var $settings = array(
  );
  var $table = array (
    'name' => 'wikidoc',
    'key'=> 'id',
    'sql' => "(
      `section` int(10) unsigned default NULL,
      `name` varchar(255) NOT NULL default '',
      `keyword` varchar(255) NOT NULL default '',
      `caption` varchar(255) NOT NULL default '',
      `text` text NOT NULL,
      `user` int(10) unsigned default NULL,
      PRIMARY KEY  (`section`, `name`),
      KEY `keyword` (`keyword`)
    ) TYPE=MyISAM;",
  );
	/**
  * Основная разметка
	*		
	*   Переводы строк -> <br />
	*   **bold**
 	*   //italic//
	*   __underline__
	*   ++striked++
	*   ~отступ
  */
  function parse_basics($text)
  {
		$text = preg_replace(
			array('/\*\*([^<>\n\*]*?)\*\*/s', '#//([^<>/\n]*?)//#s', '/\+\+([^<>\n\+]*?)\+\+/s', '/__([^<>_\n]*?)__/s', '/^~(.*)$/m'),
			array('<b>$1</b>', '<em>$1</em>', '<span class="striked">$1</span>', '<span class="underline">$1</span>', '<div class="indent">$1</div>'),
			$text
		);
		return $text;
  }
  //------------------------------------------------------------------------------
	/**
  * Заголовки
	*		
	*   == Заголовок 1 ==
	*   === Заголовок 2 ===
 	*   ==== Заголовок 3 ====
  */
  function parse_headings($text)
  {
		$text = preg_replace(
			array('/====(.*?)====/s', '/===(.*?)===/s', '/==(.*?)==/s'),
			array('<h3>$1</h3>', '<h2>$1</h2>', '<h1>$1</h1>'),
			$text
		);
		return $text;
  }
  //------------------------------------------------------------------------------
	/**
  * Внешние ссылки
	*		
	*   [[http://site.tld/link]]
	*   [[http://site.tld/link | текст для отображения]]
  */
  function parse_external_links($text)
  {
		preg_match_all('/\[\[((https?|ftp):\/\/.+?)(\s*\|\s*(.+?))?\]\]/s', $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		$delta = 0;
		foreach($matches as $match) {
			$href = trim($match[1][0]);
			$caption = trim((count($match) == 5 ? $match[4][0] : $match[1][0]));
			$href = '<a href="'.$href.'" class="external '.$match[2][0].'">'.$caption.'</a>';
			$text = substr_replace($text, $href, $match[0][1]+$delta, strlen($match[0][0]));
			$delta += strlen($href) - strlen($match[0][0]);
		}
		return $text;
  }
  //------------------------------------------------------------------------------
	/**
  * Внутренние ссылки
	*		
	*   [[Имя/Страницы]]
	*   [[Имя/Страницы | текст для отображения]]
  */
  function parse_local_links($text)
  {
		global $Eresus;
		
		preg_match_all('/\[\[(.+?)(\|(.+?))?\]\]/s', $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		$delta = 0;
		foreach($matches as $match) {
			$href = $Eresus->request['path'].trim($match[1][0]);
			$name = $this->findPageName($match[1][0]);
			if (!$name) $href .= '/edit" class="create';
			$caption = trim((count($match) == 4 ? $match[3][0] : $match[1][0]));
			if (strpos($caption, '/') !== false) $caption = substr($caption, strrpos($caption, '/')+1);
			$href = '<a href="'.$href.'">'.$caption.'</a>';
			$text = substr_replace($text, $href, $match[0][1]+$delta, strlen($match[0][0]));
			$delta += strlen($href) - strlen($match[0][0]);
		}
		return $text;
  }
  //------------------------------------------------------------------------------
	/**
  * Обработка списков
  *
	* * Пункт 1
	* * Пункт 2
	* # Пункт 1
	* # Пункт 2
	*
  */
  function parse_lists($text)
  {
		$it = 0;
		while (preg_match('/^(\*|#)(.+)$/m', $text, $match, PREG_OFFSET_CAPTURE)) {
			$delta = 0;
			$type = $match[1][0];
			array_splice($match, 1, 1);
			$list = array($match);
			$i = $match[0][1] + strlen($match[0][0])+1;
			while ($i < strlen($text) && $text{$i} == $type) {
				preg_match('/'.($type=='*'?'\\':'').$type.'([^\n]+)/s', $text, $match, PREG_OFFSET_CAPTURE, $i);
				$list[] = $match;
				$i += strlen($match[0][0])+1;
			}
			$type = $type == '*' ? 'u' : 'o';
			$replace = '<'.$type.'l>';
			for($i=0; $i<count($list); $i++) $replace .= '<li>'.trim($list[$i][1][0]).'</li>';
			$replace .= '</'.$type.'l>';
			$length = $list[count($list)-1][0][1] - $list[0][0][1] + strlen($list[count($list)-1][0][0]);
			$text = substr_replace($text, $replace, $list[0][0][1]+$delta, $length);
			$delta += strlen($replace) - $length;
		}
		return $text;
  }
  //------------------------------------------------------------------------------
	/**
  * Обработка листингов
  *
	* %% (lang)
	*  ...
	* %%
  */
  function parse_code($text)
  {
		if (preg_match_all('/%%(\w+)?(.*?)%%\n?/s', $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
  		$delta = 0;
			include_once(filesRoot.'ext/geshi/geshi.php');
			foreach($matches as $match) {
				$geshi = new GeSHi(trim($match[2][0]), $match[1][0]);
				$geshi->set_header_type(GESHI_HEADER_DIV);
				$replace = trim($geshi->parse_code());
				$replace = str_replace('<br />', "", $replace);
				$text = substr_replace($text, $replace, $match[0][1]+$delta, strlen($match[0][0]));
				$delta += strlen($replace) - strlen($match[0][0]);
			}
		}
  	return $text;
  }
  //------------------------------------------------------------------------------
	/**
  * Синтаксическое преобразование текста
  */
  function parse_content($text)
  {
	  $text = str_replace("\r", '', $text);
		$text = $this->parse_basics($text);
		$text = $this->parse_lists($text);
		$text = $this->parse_external_links($text);
		$text = $this->parse_local_links($text);
		$text = $this->parse_headings($text);
		$text = $this->parse_code($text);

		$text = preg_replace('!\n*(</(div|h\d)>)\n?!', '$1', $text);
		$text = nl2br(rtrim($text));

		return $text;
  }
  //------------------------------------------------------------------------------
	/**
  * Отрисовка административной части
  */
	function adminRenderContent()
	{
		global $page;
		goto($page->clientURL(arg('section')));
	}
  //------------------------------------------------------------------------------
	/**
  * Поиск страницы в БД по имени
  *
  * @param  string  $name  Имя страницы
  *
  * @return  mixed  Полное имя страницы или false если она не найдена
  */
  function findPageName($name)
  {
		global $Eresus, $page;
		
		$result = $Eresus->db->selectItem($this->table['name'], "`section` = '".$page->id."' AND `name` = '".mysql_real_escape_string($name)."'");
		if (!$result) {
			$result = $Eresus->db->select($this->table['name'], "`section` = '".$page->id."' AND `name` LIKE '%/".mysql_real_escape_string($name)."'");
			if ($result) $result = $result[0];
		}
		if ($result) $result = $result['name'];
		return $result;
  }
  //------------------------------------------------------------------------------
	/**
  * Чтение страницы из БД
  *
  * @return  array  Страница
  */
  function readPage()
  {
		global $Eresus, $page;
		
		$result = $Eresus->db->selectItem($this->table['name'], "`section` = '".$page->id."' AND `name` = '".mysql_real_escape_string($this->page)."'");
		return $result;
  }
  //------------------------------------------------------------------------------
	/**
  * Запись страницы в БД
  *
  * @param  array   $item    Страница
  */
  function writePage($item)
  {
		global $Eresus, $page;
		
		if ($this->readPage())
			$Eresus->db->updateItem($this->table['name'], $item, "`section` = '".$page->id."' AND `name` = '".mysql_real_escape_string($this->page)."'");
		else
			$Eresus->db->insert($this->table['name'], $item);
  }
  //------------------------------------------------------------------------------
	/**
  * Обновление страницы
  */
	function updatePage()
	{
		global $Eresus, $page;
		
		$item = $Eresus->db->selectItem($this->table['name'], "`section` = '".$page->id."' AND `name` = '".mysql_real_escape_string($this->page)."'");
		if (!$item) $item = array(
			'section' => $page->id,
			'name' => $this->page,
			'keyword' => $this->page,
			'caption' => $this->page,
			'text' => '',
			'user' => $Eresus->user['id'],
		);
		$item['keyword'] = arg('keyword') ? arg('keyword') : $this->page;
		$item['caption'] = arg('caption') ? arg('caption') : $this->page;
		$item['text'] = arg('text');
		$this->writePage($item);
		goto($Eresus->request['path'].$this->page);
	}
  //------------------------------------------------------------------------------
	/**
  * Форма редактирования страницы
  *
  * @param  string  $name  description
  *
  * @return  type  description
  */
  function editPage()
  {
		global $Eresus, $page;
		
		$item = $this->readPage();
		$result = '';
		if (UserRights(USER)) {
			$form = array(
	      'name' => 'EditPage',
				'action' => $Eresus->request['path'].$this->page.'/update',
	      'caption' => 'Изменение страницы "'.$this->page.'"',
	      'width' => '100%',
	      'fields' => array (
	        array('type'=>'hidden','name'=>'action', 'value'=>'update'),
	        array('type'=>'edit','name'=>'caption','label'=>'Заголовок', 'width' => '100%'),
	        array('type'=>'edit','name'=>'keyword','label'=>'Индекс', 'width' => '300px'),
	        array('type'=>'memo','name'=>'text', 'height' => '25', 'width' => '100%'),
	      ),
	      'buttons' => array('ok', 'cancel'),
	    );
	    $result = $page->renderForm($form, $item);
		}
		return $result;
  }
  //------------------------------------------------------------------------------
	/**
  * Показывает страницу
  *
  * @param  string  $name  Имя страницы
  *
  */
  function showPage()
  {
		global $Eresus, $page;
		
		$item = $this->readPage();
		if ($item) {
			$page->section[] = $page->title = $item['caption'];
			$result = '<div class="WikiDoc">'.$this->parse_content($item['text']).'</div>';
			$result .= 
				'<div class="WikiDocControls">'.
				'[ <a href="'.$Eresus->request['path'].$this->page.'/edit">Редактировать</a> ] '.
				'[ <a href="'.$Eresus->request['path'].$this->page.'/delete">Удалить</a> ]'.
				'</div>';
		} else $result = $this->editPage();
		return $result;
  }
  //------------------------------------------------------------------------------
	/**
  * Отрисовка клиентской части
  *
  * @param  type  $arg  description
  *
  * @return  type  description
  */
  function clientRenderContent()
  {
		global $Eresus, $page;

		$result = '';
		$this->page = substr($Eresus->request['url'], strlen($Eresus->request['path']));
		$action = end($Eresus->request['params']);
		if (in_array($action, array('edit', 'update', 'delete'))) $this->page = substr($this->page, 0, -strlen($action)-1);
		if (substr($this->page, -1) == '/') $this->page = substr($this->page, 0, -1);
		switch($action) {
			case 'edit': $result = $this->editPage(); break;
			case 'update': $result = $this->updatePage(); break;
			default: $result = $this->showPage();
		}
		return $result;
  }
  //------------------------------------------------------------------------------
}
?>