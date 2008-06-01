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
  var $version = '2.00b4';
  var $description = 'Главное меню сайта';
  var $settings = array(
    'root' => 0, # ID корневого раздела
    'expandLevelAuto' => 0, # Автоматически разворачивать подменю до этого уровня
    'expandLevelMax' => 0, # Максимальный уровень подменю который можно разворачивать
    'tmplList' => '<table class="level$(level)">$(items)</table>',
    'tmplItem' => '<tr><td><a href="$(url)" title="$(hint)">$(caption)</a>$(submenu)</td></tr>',
  );
  var $pages = array(); # Путь по страницым
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
      $template = str_replace($matches[0][$i], $item['selected']?$matches[1][$i]:$matches[2][$i], $template);
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
  function menuBrunch($owner = 0, $path = '', $level = 0)
  # Функция строит ветку меню начиная от элемента с id = $owner
  #   $owner - id корневого предка
  #   $path - виртуальный путь к страницам
  #   $level - уровень вложенности
  {
  global $db, $user, $page;
    $result = '';
    if (strpos($path, httpRoot) !== false) $path = substr($path, strlen(httpRoot));
    $items = $db->select('`pages`', "(`access`>='".($user['auth']?$user['access']:GUEST)."')AND (`owner`='".$owner."') AND (`active`='1') AND (`visible` = '1')", "`position`");
    if (count($items)) {
      foreach($items as $item) {
        if ($item['type'] == 'url') {
          $item['options'] = decodeOptions($item['options']);
          $item['url'] = $item['content'];
        } else $item['url'] = httpRoot.$path.($item['name']=='main'?'':$item['name'].'/');
        $item['level'] = $level+1;
        $item['selected'] = $item['id'] == $page->id;
        if ((!$this->settings['expandLevelAuto'] || ($this->settings['expandLevelAuto'] <= $level)) || (count($this->pages) && ($this->pages[$level] == $item['name']))) {
          $item['submenu'] = $this->menuBrunch($item['id'], $path.$item['name'].'/', $level+1);
        }
        $result .= $this->replaceMacros($this->settings['tmplItem'], $item);
      }
      $result = array('level'=>($level+1), 'items'=>$result);
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
        array('type'=>'text', 'value' => 'Макросы:<ul><li><b>Все элементы страницы</b></li><li><b>$(level)</b> - номер текущего уровня</li><li><b>$(url)</b> - ссылка</li><li><b>$(submenu)</b> - место для вставки подменю</li><li><b>{%selected?строка1:строка2}</b> - если элемент выбран, вставить строка1, иначе строка2</li></ul>'),
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
    $this->pages[] = $item['name'];
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientOnPageRender($text)
  {
    global $page;
    $menu = $this->menuBrunch($this->settings['root'], $page->clientURL($this->settings['root']));
    $text = str_replace('$(plgMainMenu)', $menu, $text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>