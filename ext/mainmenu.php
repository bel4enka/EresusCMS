<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus™ 2.00+
# © 2005-2006, ProCreat Systems
# http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TMainMenu extends TPlugin {
  var $name = 'mainmenu';
  var $title = 'MainMenu';
  var $type = 'client';
  var $version = '2.02';
  var $description = 'Главное меню сайта';
  var $settings = array(
    'root' => 0, # ID корневого раздела
    'expandLevelAuto' => 0, # Автоматически разворачивать подменю до этого уровня
    'expandLevelMax' => 0, # Максимальный уровень подменю который можно разворачивать
    'tmplList' => '<table class="level$(level)">$(items)</table>',
    'tmplItem' => '<tr><td><a href="$(url)" title="$(hint)">$(caption)</a>$(submenu)</td></tr>',
    'tmplSpecial' => '',
    'specialMode' => 0,
    'rootHighlight' => false,
  );
  var $pages = array(); # Путь по страницым
  var $ids = array(); # Путь по страницым (только идентификаторы)
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Стандартные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function TMainMenu()
  # производит регистрацию обработчиков событий
  {
  global $plugins;

    parent::TPlugin();
    $plugins->events['clientOnURLSplit'][] = $this->name;
    $plugins->events['clientOnPageRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Внутренние функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
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
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function pagesBrunch($owner = 0, $level = 0)
  {
  global $db;
    $result = array(array(), array());
    $items = $db->select('`pages`', "(`owner`='".$owner."') AND (`active`='1')", "`position`", false, "`id`,`caption`");
    if (count($items)) foreach($items as $item) {
      $result[0][] = str_repeat('- ', $level).$item['caption'];
      $result[1][] = $item['id'];
      $sub = $this->pagesBrunch($item['id'], $level+1);
      if (count($sub[0])) {
        $result[0] = array_merge($result[0], $sub[0]);
        $result[1] = array_merge($result[1], $sub[1]);
      }
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function menuBrunch($owner = 0, $path = '', $level = 1)
  # Функция строит ветку меню начиная от элемента с id = $owner
  #   $owner - id корневого предка
  #   $path - виртуальный путь к страницам
  #   $level - уровень вложенности
  {
  global $db, $user, $page, $request;
    $result = '';
    if (strpos($path, httpRoot) !== false) $path = substr($path, strlen(httpRoot));
    $items = $db->select('`pages`', "(`access`>='".($user['auth']?$user['access']:GUEST)."')AND (`owner`='".$owner."') AND (`active`='1') AND (`visible` = '1')", "`position`");
    if (count($items)) {
      foreach($items as $item) {
        $template = $this->settings['tmplItem'];
        if ($item['type'] == 'url') {
          $item['options'] = decodeOptions($item['options']);
          $item['url'] = $item['content'];
        } else $item['url'] = httpRoot.$path.($item['name']=='main'?'':$item['name'].'/');
        $item['level'] = $level;
        $item['is-selected'] = $item['id'] == $page->id;
        $item['is-parent'] = !$item['is-selected'] && in_array($item['id'], $this->ids);
        if ((!$this->settings['expandLevelAuto'] || ($level < $this->settings['expandLevelAuto'])) || (($item['is-parent'] || $item['is-selected']) && (!$this->settings['expandLevelMax'] || $level < $this->settings['expandLevelMax']))) {
          $item['submenu'] = $this->menuBrunch($item['id'], $path.$item['name'].'/', $level+1);
        }
        if ($this->settings['rootHighlight'] && in_array($item['id'], $this->ids) && $item['name'] != 'main') $item['is-selected'] = true;
        if ($this->settings['specialMode'] == 1 && $item['is-selected']) $template = $this->settings['tmplSpecial'];
        if ($this->settings['specialMode'] == 2 && !empty($item['submenu'])) $template = $this->settings['tmplSpecial'];
        $result .= $this->replaceMacros($template, $item);
      }
      $result = array('level'=>($level), 'items'=>$result);
      $result = $this->replaceMacros($this->settings['tmplList'], $result);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function settings()
  {
  global $page, $db;

    $sections = $this->pagesBrunch();
    array_unshift($sections[0], 'ТЕКУЩИЙ РАЗДЕЛ');
    array_unshift($sections[1], -1);
    array_unshift($sections[0], 'КОРЕНЬ');
    array_unshift($sections[1], 0);
    $form = array(
      'name'=>'SettingsForm',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'select','name'=>'root','label'=>'Корневой раздел', 'values'=>$sections[1], 'items'=>$sections[0]),
        array('type'=>'header', 'value'=>'Уровни меню'),
        array('type'=>'edit','name'=>'expandLevelAuto','label'=>'Всегда показывать', 'width' => '20px', 'comment' => 'уровней (0 - развернуть все)'),
        array('type'=>'edit','name'=>'expandLevelMax','label'=>'Разворачивать максимум', 'width' => '20px', 'comment' => 'уровней (0 - без ограничений)'),
        array('type'=>'header', 'value'=>'Шаблоны'),
        array('type'=>'memo','name'=>'tmplList','label'=>'Шаблон блока одного уровня меню', 'height' => '3'),
        array('type'=>'text', 'value' => 'Макросы:<ul><li><b><li><b>$(level)</b> - номер текущего уровня</li><li><b>$(items)</b> - пункты меню</li></ul>'),
        array('type'=>'memo','name'=>'tmplItem','label'=>'Пункта меню', 'height' => '3'),
        array('type'=>'memo','name'=>'tmplSpecial','label'=>'Специальный шаблон', 'height' => '3'),
        array('type'=>'text', 'value' => 'Использовать специальный шаблон для'),
        array('type'=>'select','name'=>'specialMode','items'=>array('не использовать','для выбранного пункта','для пунктов, имеющих подпункты')),
        array('type'=>'checkbox','name'=>'rootHighlight','label'=>'Подсвечивать раздел если выбран его подраздел'),
        array('type'=>'divider'),
        array('type'=>'text', 'value' =>
          'Макросы:<ul>'.
          '<li><b>Все элементы страницы</b></li>'.
          '<li><b>$(level)</b> - номер текущего уровня</li><li><b>$(url)</b> - ссылка</li>'.
          '<li><b>$(submenu)</b> - место для вставки подменю</li>'.
          '<li><b>{%selected?строка1:строка2}</b> - если элемент выбран, вставить строка1, иначе строка2</li>'.
          '<li><b>{%parent?строка1:строка2}</b> - если элемент находится среди родительских разделов выбранного элемента, вставить строка1, иначе строка2</li>'.
          '</ul>'),
        array('type'=>'divider'),
        array('type'=>'text', 'value' =>
          'Для вставки меню используйте макрос <b>$(MainMenu)</b>, или <b>$(MainMenu:ROOT)</b> где "ROOT" - задает корневой раздел меню. Раздел может указываться несколькими способами:<ul>'.
          '<li><b>число</b> - идентификатор раздела</li>'.
          '<li><b>$parentN</b> - родительский раздел уровня N</li>'.
          '</ul>'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Обработчики событий
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnURLSplit($item, $url)
  {
    $this->pages[] = $item;
    $this->ids[] = $item['id'];
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
    global $page, $request;

    preg_match_all('/\$\(MainMenu(:(.+))?\)/Us', $text, $menus, PREG_SET_ORDER);
    $path = '';
    for($i = 0; $i < count($menus); $i++) {
      $root =  isset($menus[$i][2])?$menus[$i][2]:$this->settings['root'];
      if (is_numeric($root)) {
        $path = $root > -1 ? $page->clientURL($root) : $request['path'];
      } else {
        if (substr($root, 0, 7) == '$parent') {
          $root = substr($root, 7) - 1;
          if (!isset($this->pages[$root])) continue;
          $path = httpRoot;
          for($j=0; $j <= $root; $j++) $path .= $this->pages[$j]['name'].'/';
          $root = $this->pages[$root]['id'];
        }
      }
      $menu = $this->menuBrunch($root, $path);
      $text = str_replace($menus[$i][0], $menu, $text);
    }
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>