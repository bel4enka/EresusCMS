<?php
/**
 * Menus
 *
 * Eresus 2
 *
 * Управление несколькими меню
 *
 * @version 2.00
 *
 * @copyright   2007, ProCreat Systems, http://procreat.ru/
 * @copyright   2007, Eresus Group, http://eresus.ru/
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

class Menus extends Plugin {
  var $version = '2.00a';
  var $kernel = '2.10rc';
  var $title = 'Управление меню';
  var $description = 'Менеджер меню';
  var $type = 'client,admin';
 /**
  * @var array
  */
  var $menu = null;
 /**
  * Путь по страницым
  * @var array
  */
  var $pages = array();
 /**
  * Путь по страницым (только идентификаторы)
  * @var array
  */
  var $ids = array();
 /**
  * Конструктор
  * @return Menus
  */
  function Menus()
  {
    parent::Plugin();
    $this->listenEvents('clientOnURLSplit', 'clientOnPageRender', 'adminOnMenuRender');
  }
  //-----------------------------------------------------------------------------
 /**
  * Создание таблиц
  */
  function install()
  {
  	parent::install();
  	$this->dbCreateTable("
      `id` int(10) unsigned NOT NULL auto_increment,
      `name` varchar(31) default NULL,
      `caption` varchar(255) default NULL,
      `active` tinyint(1) unsigned default NULL,
      `root` int(10) default NULL,
      `rootLevel` int(10) unsigned default 0,
      `expandLevelAuto` int(10) unsigned default 0,
      `expandLevelMax` int(10) unsigned default 0,
      `glue` varchar(63) default '',
      `tmplList` text,
      `tmplItem` text,
      `tmplSpecial` text,
      `specialMode` tinyint(3) unsigned default 0,
      `invisible` tinyint(1) unsigned default 0,
      PRIMARY KEY  (`id`),
      KEY `name` (`name`),
      KEY `active` (`active`)
  	");
  }
  //-----------------------------------------------------------------------------
 /**
  * Удаление таблиц
  */
  function uninstall()
  {
  	$this->dbDropTable();
  	parent::uninstall();
  }
  //-----------------------------------------------------------------------------
 /**
  * Добавляет меню в БД
  *
  * @param array $item  Описание меню
  */
  function insert($item)
  {
    $this->dbInsert('', $item);
    sendNotify('Добавлено меню: '.$item['caption']);
  }
  //-----------------------------------------------------------------------------
 /**
  * Изменяет меню в БД
  *
  * @param array $item  Описание меню
  */
  function update($item)
  {
    $this->dbUpdate('', $item);
    sendNotify('Изменено меню: '.$item['caption']);
  }
  //-----------------------------------------------------------------------------
 /**
  * Замена макросов
  *
  * @param string $template
  * @param array $item
  * @return string
  */
  function replaceMacros($template, $item)
  {
    preg_match_all('|{%selected\?(.*?):(.*?)}|i', $template, $matches);
    for($i = 0; $i < count($matches[0]); $i++)
      $template = str_replace($matches[0][$i], $item['is-selected']?$matches[1][$i]:$matches[2][$i], $template);
    preg_match_all('|{%parent\?(.*?):(.*?)}|i', $template, $matches);
    for($i = 0; $i < count($matches[0]); $i++)
      $template = str_replace($matches[0][$i], $item['is-parent']?$matches[1][$i]:$matches[2][$i], $template);
    $template = parent::replaceMacros($template, $item);
    return $template;
  }
  //-----------------------------------------------------------------------------
 /**
  * Построение дерева разделов
  *
  * @param int $owner  ID корневого раздела
  * @param int $level  Текущий уровень вложенности
  * @return array
  */
  function pagesBranch($owner = 0, $level = 0)
  {
    global $Eresus;

    $result = array(array(), array());
    $items = $Eresus->sections->children($owner, GUEST, SECTIONS_ACTIVE);
    if (count($items)) foreach($items as $item) {
      $result[0][] = str_repeat('- ', $level).$item['caption'];
      $result[1][] = $item['id'];
      $sub = $this->pagesBranch($item['id'], $level+1);
      if (count($sub[0])) {
        $result[0] = array_merge($result[0], $sub[0]);
        $result[1] = array_merge($result[1], $sub[1]);
      }
    }
    return $result;
  }
	//-----------------------------------------------------------------------------
  /**
  * Добавляет новое меню в БД
  */
  function adminInsert()
  {
    $item['name'] = arg('name', 'word');
		$item['caption'] = arg('caption', 'dbsafe');
    $item['active'] = true;
    $item['root'] = arg('root', 'int');
    $item['rootLevel'] = arg('rootLevel', 'int');
    $item['expandLevelAuto'] = arg('expandLevelAuto', 'int');
    $item['expandLevelMax'] = arg('expandLevelMax', 'int');
    $item['glue'] = arg('glue', 'dbsafe');
    $item['tmplList'] = arg('tmplList', 'dbsafe');
    $item['tmplItem'] = arg('tmplItem', 'dbsafe');
    $item['tmplSpecial'] = arg('tmplSpecial', 'dbsafe');
    $item['specialMode'] = arg('specialMode', 'int');
    $item['invisible'] = arg('invisible', 'int');
    if (empty($item['name']) || empty($item['caption'])) {
    	saveRequest();
    	ErrorMessage('Заполнены не все обязательные поля!');
    	goto($GLOBALS['Eresus']->request['referer']);
    }
    $this->insert($item);
    goto(arg('submitURL'));
  }
  //-----------------------------------------------------------------------------
 /**
  * Добавляет новое меню в БД
  */
  function adminUpdate()
  {
		$item = $this->dbItem('', arg('update', 'int'));
    $item['name'] = arg('name', 'word');
		$item['caption'] = arg('caption', 'dbsafe');
    $item['root'] = arg('root', 'int');
    $item['rootLevel'] = arg('rootLevel', 'int');
    $item['expandLevelAuto'] = arg('expandLevelAuto', 'int');
    $item['expandLevelMax'] = arg('expandLevelMax', 'int');
    $item['glue'] = arg('glue', 'dbsafe');
    $item['tmplList'] = arg('tmplList', 'dbsafe');
    $item['tmplItem'] = arg('tmplItem', 'dbsafe');
    $item['tmplSpecial'] = arg('tmplSpecial', 'dbsafe');
    $item['specialMode'] = arg('specialMode', 'int');
    $item['invisible'] = arg('invisible', 'int');
    /*if (empty($item['name']) || empty($item['caption'])) {
    	saveRequest();
    	ErrorMessage('Заполнены не все обязательные поля!');
    	goto($GLOBALS['Eresus']->request['referer']);
    }*/
    $this->update($item);
    goto(arg('submitURL'));
  }
  //-----------------------------------------------------------------------------
 /**
  * Изменяет активность меню
  */
  function adminToggle()
  {
  	global $Eresus;
		$item = $this->dbItem('', arg('toggle', 'int'));
		$item['active'] = !$item['active'];
    $this->dbUpdate('', $item);
    goto($Eresus->request['referer']);
  }
  //-----------------------------------------------------------------------------
  /**
  * Создаёт базовую форму диалога создания/изменения меню
  *
  * @return array
  */
  function adminDialogTemplate()
  {
    $sections = $this->pagesBranch();
    array_unshift($sections[0], 'ТЕКУЩИЙ РАЗДЕЛ');
    array_unshift($sections[1], -1);
    array_unshift($sections[0], 'КОРЕНЬ');
    array_unshift($sections[1], 0);
    $form = array(
      'name' => 'FormCreate',
      'width' => '500px',
      'fields' => array (
        array('type'=>'edit','name'=>'name','label'=>'<b>Имя</b>', 'width' => '100px', 'comment' => 'для использования в макросах', 'pattern'=>'/[a-z]\w*/i', 'errormsg'=>'Имя должно начинаться с буквы и может содержать только латинские буквы и цифры'),
        array('type'=>'edit','name'=>'caption','label'=>'<b>Название</b>', 'width' => '100%', 'hint' => 'Для внутреннего использования', 'pattern'=>'/.+/i', 'errormsg'=>'Название не может быть пустым'),
        array('type'=>'select','name'=>'root','label'=>'Корневой раздел', 'values'=>$sections[1], 'items'=>$sections[0], 'extra' =>'onchange="this.form.rootLevel.disabled = this.value != -1"'),
        array('type'=>'edit','name'=>'rootLevel','label'=>'Фикс. уровень', 'width' => '20px', 'comment' => '(0 - текущий уровень)', 'default' => 0, 'disabled' => true),
        array('type'=>'checkbox','name'=>'invisible','label'=>'Показывать скрытые разделы'),
        array('type'=>'header', 'value'=>'Уровни меню'),
        array('type'=>'edit','name'=>'expandLevelAuto','label'=>'Всегда показывать', 'width' => '20px', 'comment' => 'уровней (0 - развернуть все)', 'default' => 0),
        array('type'=>'edit','name'=>'expandLevelMax','label'=>'Разворачивать максимум', 'width' => '20px', 'comment' => 'уровней (0 - без ограничений)', 'default' => 0),
        array('type'=>'header', 'value'=>'Шаблоны'),
        array('type'=>'memo','name'=>'tmplList','label'=>'Шаблон блока одного уровня меню', 'height' => '3'),
        array('type'=>'text', 'value' => 'Макросы:<ul><li><b>$(level)</b> - номер текущего уровня</li><li><b>$(items)</b> - пункты меню</li></ul>'),
        array('type'=>'edit','name'=>'glue','label'=>'Разделитель пунктов', 'width' => '100%', 'maxlength' => 63),
        array('type'=>'memo','name'=>'tmplItem','label'=>'Шаблон пункта меню', 'height' => '3'),
        array('type'=>'memo','name'=>'tmplSpecial','label'=>'Специальный шаблон пункта меню', 'height' => '3'),
        array('type'=>'text', 'value' => 'Использовать специальный шаблон'),
        array('type'=>'select','name'=>'specialMode','items'=>array(
          'нет',
          'только для выбранного пункта',
          'для выбранного пункта если выбран его подпункт',
          'для пунктов, имеющих подпункты'
          )
        ),
        array('type'=>'divider'),
        array('type'=>'text', 'value' =>
          'Макросы:<ul>'.
          '<li><b>Все свойста раздела</b> - $(id), $(title), $(caption), $(hint), $(description), $(keywords) и т.д.</li>'.
        	'<li><b>$(href)</b> - ссылка</li>'.
          '<li><b>$(num)</b> - порядковый номер раздела в текущем уровне</li>'.
        	'<li><b>$(level)</b> - номер текущего уровня</li><li>'.
          '<li><b>$(submenu)</b> - место для вставки подменю</li>'.
          '<li><b>{%selected?строка1:строка2}</b> - если элемент выбран, вставить строка1, иначе строка2</li>'.
          '<li><b>{%parent?строка1:строка2}</b> - если элемент находится среди родительских разделов выбранного элемента, вставить строка1, иначе строка2</li>'.
          '</ul>'),
        array('type'=>'divider'),
        array('type'=>'text', 'value' => 'Для вставки меню используйте макрос <b>$(Menus:имя_меню)</b>'),
      ),
    );
  	return $form;
  }
  //-----------------------------------------------------------------------------
 /**
  * Диалог создания меню
  *
  * @return string
  */
  function adminCreateDialog()
  {
    global $Eresus, $page;

    $form = $this->adminDialogTemplate();
    $form['caption']  = 'Создать меню';
    $form['fields'][] = array('type'=>'hidden','name'=>'action', 'value'=>'insert');
    $form['buttons'] = array('ok', 'cancel');
    restoreRequest();
    $result = $page->renderForm($form, $Eresus->request['arg']);
    return $result;
  }
  //-----------------------------------------------------------------------------
 /**
  * Диалог изменения меню
  *
  * @return string
  */
  function adminEditDialog()
  {
    global $Eresus, $page;

    $item = $this->dbItem('', arg('id', 'int'));
    $form = $this->adminDialogTemplate();
    $form['caption']  = 'Изменение меню';
    $form['fields'][] = array('type'=>'hidden','name'=>'update', 'value'=>$item['id']);
    $result = $page->renderForm($form, $item);
    return $result;
  }
 /**
  * Сохранение текущего пути по разделам
  *
  * @param array $item Поисание раздела
  * @param string $url URI раздела
  */
  function clientOnURLSplit($item, $url)
  {
    $this->pages[] = $item;
    $this->ids[] = $item['id'];
  }
  //-----------------------------------------------------------------------------
 /**
  * Cтроит ветку меню начиная от элемента с id = $owner
  *
  * @param int $owner    id корневого предка
  * @param string $path  виртуальный путь к страницам
  * @param int $level		 уровень вложенности
  * @return string
  */
  function menuBranch($owner = 0, $path = '', $level = 1)
  {
    global $Eresus, $page;

    $result = '';
    if (strpos($path, httpRoot) !== false) $path = substr($path, strlen(httpRoot));
    if ($owner == -1) $owner = $page->id;
    $items = $Eresus->sections->children($owner, $Eresus->user['auth'] ? $Eresus->user['access'] : GUEST, SECTIONS_ACTIVE | ($this->menu['invisible']? 0 : SECTIONS_VISIBLE));
    if (count($items)) {
      $result = array();
      for($i = 0; $i < count($items); $i++) {
        $template = $this->menu['tmplItem'];
        if ($items[$i]['type'] == 'url') {
          $items[$i] = $Eresus->sections->get($items[$i]['id']);
          $items[$i]['url'] = $items[$i]['href'] = $page->replaceMacros($items[$i]['content']); #FIXME: Убрать 'url' в последующих версиях (обратная совместимость)
        } else $items[$i]['url'] = $items[$i]['href'] = httpRoot.$path.($items[$i]['name']=='main'?'':$items[$i]['name'].'/'); #FIXME: Убрать 'url' в последующих версиях (обратная совместимость)
				$items[$i]['num'] = $i+1;
        $items[$i]['level'] = $level;
        $items[$i]['is-selected'] = $items[$i]['id'] == $page->id;
        $items[$i]['is-parent'] = !$items[$i]['is-selected'] && in_array($items[$i]['id'], $this->ids);
        if ((!$this->menu['expandLevelAuto'] || ($level < $this->menu['expandLevelAuto'])) || (($items[$i]['is-parent'] || $items[$i]['is-selected']) && (!$this->menu['expandLevelMax'] || $level < $this->menu['expandLevelMax']))) {
          $items[$i]['submenu'] = $this->menuBranch($items[$i]['id'], $path.$items[$i]['name'].'/', $level+1);
        }
        switch ($this->menu['specialMode']) {
          case 0: # нет
          break;
          case 1: # только для выбранного пункта
            if ($items[$i]['is-selected']) $template = $this->menu['tmplSpecial'];
          break;
          case 2: # для выбранного пункта если выбран его подпункт
            if ((strpos($Eresus->request['path'], $items[$i]['href']) === 0) && $items[$i]['name'] != 'main') $template = $this->menu['tmplSpecial'];
          break;
          case 3: # для пунктов, имеющих подпункты
            if (!empty($items[$i]['submenu'])) $template = $this->menu['tmplSpecial'];
          break;
        }
        $result[] = $this->replaceMacros($template, $items[$i]);
      }
      $result = implode($this->menu['glue'], $result);
      $result = array('level'=>($level), 'items'=>$result);
      $result = $this->replaceMacros($this->menu['tmplList'], $result);
    }
    return $result;
  }
	//-----------------------------------------------------------------------------
 /**
  * Поиск и отрисовка меню
  *
  * @param string $text
  * @return string
  */
  function clientOnPageRender($text)
  {
    global $Eresus, $page;

    preg_match_all('/\$\(menus:(.+?)\)/si', $text, $menus, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
    $delta = 0;
    for($i = 0; $i < count($menus); $i++) {
      $this->menu = $this->dbItem('', $menus[$i][1][0], 'name');
      if (!is_null($this->menu) && $this->menu['active']) {
        if ($this->menu['root'] == -1 && $this->menu['rootLevel']) {
          $parents = $Eresus->sections->parents($page->id);
          $level = count($parents);
          if ($level == $this->menu['rootLevel']) $this->menu['root'] = -1;
          elseif ($level > $this->menu['rootLevel']) $this->menu['root'] = $this->menu['root'] = $parents[$this->menu['rootLevel']];
          else $this->menu['root'] = -2;
        }
        $path = $this->menu['root'] > -1 ? $page->clientURL($this->menu['root']) : $Eresus->request['path'];
        $menu = $this->menuBranch($this->menu['root'], $path);
        $text = substr_replace($text, $menu, $menus[$i][0][1]+$delta, strlen($menus[$i][0][0]));
        $delta += strlen($menu) - strlen($menus[$i][0][0]);
      }
    }
    return $text;
  }
  //-----------------------------------------------------------------------------
 /**
  * Отрисовка списка меню
  * @return string
  */
  function adminRenderList()
  {
  	global $Eresus, $page;

  	$result = '';
		$tabs = array(
      'width'=>'180px',
      'items'=>array(
       array('caption'=>'Создать меню', 'name'=>'action', 'value'=>'create')
      ),
    );
    $result .= $page->renderTabs($tabs);

    # Отрисовка списка
    $root = $Eresus->root.'admin.php?mod=pages&amp;';
    $items = $this->dbSelect('', '', 'caption');
    useLib('admin/lists');
    $list = new AdminList();
    $list->setHead('', 'Название', 'Имя');
    for($i=0; $i<count($items); $i++) {
      $row = array();
      $row[] =
      	$list->control('delete', $page->url(array('delete' => $items[$i]['id']))).'&nbsp;'.
      	$list->control($items[$i]['active']? 'off' : 'on', $page->url(array('toggle' => $items[$i]['id']))).'&nbsp;'.
      	$list->control('edit', $page->url(array('id' => $items[$i]['id'])));
      $row[] = $items[$i]['caption'];
      $row[] = $items[$i]['name'];
     	$list->addRow($row);
    }
    $result .= $list->render();
    return $result;
  }
  //-----------------------------------------------------------------------------
 /**
  * Отрисовка контента АИ
  * @return string
  */
  function adminRender()
  {
  	$result = '';
  	switch (arg('action')) {
  		case 'create': $result = $this->adminCreateDialog(); break;
  		case 'insert': $this->adminInsert(); break;
  		default: switch (true) {
  			case arg('update'): $this->adminUpdate(); break;
  			case arg('toggle'): $this->adminToggle(); break;
  			case arg('id'): $result = $this->adminEditDialog(); break;
  			default: $result = $this->adminRenderList();
  		}
  	}
  	return $result;
  }
  //-----------------------------------------------------------------------------
 /**
  * Добавление пункта в меню "Расширения"
  */
  function adminOnMenuRender()
  {
  	global $page;
    $page->addMenuItem(admExtensions, array ('access'  => ADMIN, 'link'  => $this->name, 'caption'  => $this->title, 'hint'  => $this->description));
  }
  //-----------------------------------------------------------------------------
}
?>