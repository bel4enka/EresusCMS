<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus™
# © 2005, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class ThMenu extends TPlugin {
  var 
    $name = 'hmenu',
    $title = 'hMenu',
    $type = 'client',
    $version = '1.01',
    $description = 'Горизонтальное меню',
    $settings = array(
      'itemTemplate' => '<a href="$(link)">$(caption)</a>',
      'divider' => '',
      'currentTemplate' => '$(caption)',
      'rootHighlight' => false,
      'sections' => array('all'),
    );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Стандартные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function ThMenu()
  # производит регистрацию обработчиков событий
  {
  global $plugins;
  
    parent::TPlugin();
    $plugins->events['clientOnPageRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function replaceMacros($template, $item)
  {
    $item['link'] = str_replace('$(httpRoot)', httpRoot, $item['link']);
    $result = parent::replaceMacros($template, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function menuBrunch($owner = 0, $level = 0)
  {
  global $db;
    $result = array(array(), array());
    $items = $db->select('`pages`', "(`access`>='".USER."')AND(`owner`='".$owner."') AND (`active`='1')", "`position`", false, "`id`,`caption`");
    if (count($items)) foreach($items as $item) {
      $result[0][] = str_repeat('- ', $level).$item['caption'];
      $result[1][] = $item['id'];
      $sub = $this->menuBrunch($item['id'], $level+1);
      if (count($sub[0])) {
        $result[0] = array_merge($result[0], $sub[0]);
        $result[1] = array_merge($result[1], $sub[1]);
      }
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function settings()
  {
    global $page;
  
    $sections = array(array(), array());
    $sections = $this->menuBrunch();
    array_unshift($sections[0], 'ВСЕ РАЗДЕЛЫ');
    array_unshift($sections[1], 'all');
    $form = array(
      'name'=>'SettingsForm',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'edit','name'=>'itemTemplate','label'=>'Шаблон пункта меню','width'=>'100%'),
        array('type'=>'edit','name'=>'currentTemplate','label'=>'Шаблон выбранного пункта','width'=>'100%'),
        array('type'=>'checkbox','name'=>'rootHighlight','label'=>'Подсвечивать раздел если выбран его подраздел'),
        array('type'=>'edit','name'=>'divider','label'=>'Разделитель','width'=>'30px'),
        array('type'=>'listbox','name' =>'sections','label'=>'Разделы','height'=>5,'items'=>$sections[0],'values'=>$sections[1]),
        array('type'=>'divider'),
        array('type'=>'text','value'=>'Для вставки меню в шаблон используйте макрос <b>$(hMenu)</b>'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Обработчики событий
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientOnPageRender($text)
  {
    global $db, $user, $page, $request;

    $menu = '';
    if (count($this->settings['sections'])) {
      $current = substr($request['path'], strlen(httpRoot));
      if (($i = strpos($current, '/')) !== false) $current = substr($current, 0, $i);
      $menu = array();
      $sections = $this->settings['sections'][0] == 'all'?'AND (`owner`=\'0\')':'AND `id` IN ('.implode(',', $this->settings['sections']).')';
      $items = $db->select('`pages`', "(`access`>='".($user['auth']?$user['access']:GUEST)."') AND (`active`='1') AND (`visible` = '1')".$sections, "`position`");
      if (count($items)) foreach($items as $item) {
        $item['options'] = decodeOptions($item['options']);
        if (isset($item['options']['hMenu']) && ($item['options']['hMenu'] == 'hide')) continue;
        if ($item['type'] == 'url') {
          $item['link'] = $item['content'];
        } else $item['link'] = $page->clientURL($item['id']);
        if (empty($current)) $current = 'main';
        $menu[] = $this->replaceMacros($item['name'] == $current?$this->settings['currentTemplate']:$this->settings['itemTemplate'], $item);
      }
      $menu = implode($this->settings['divider'], $menu);
    }
    $text = str_replace('$(hMenu)', $menu, $text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>