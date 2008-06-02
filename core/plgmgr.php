<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.05
# © 2004-2006, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Управление модулями расширения
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TPlgMgr {
  var $access = ADMIN;
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function toggle()
  {
  global $db, $page, $request;

    $item = $db->selectItem('plugins', "`name`='".$request['arg']['toggle']."'");
    $item['active'] = !$item['active'];
    $db->updateItem('plugins', $item, "`name`='".$request['arg']['toggle']."'");
    SendNotify(($item['active']?admActivated:admDeactivated).': '.$item['title']);
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function delete()
  {
  global $plugins, $page, $request;

    $plugins->load($request['arg']['delete']);
    $plugins->uninstall($request['arg']['delete']);
    SendNotify(admDeleted.': '.$plugins->list[$request['arg']['delete']]['title']);
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function edit()
  {
  global $plugins, $page, $request;

    $plugins->load($request['arg']['id']);
    if (method_exists($plugins->items[$request['arg']['id']], 'settings')) {
      $result = $plugins->items[$request['arg']['id']]->settings();
    } else {
      $form = array(
        'name' => 'InfoWindow',
        'caption' => $page->title,
        'width' => '300px',
        'fields' => array (
          array('type'=>'text','value'=>'<div align="center"><strong>Этот плагин не имеет настроек</strong></div>'),
        ),
        'buttons' => array('cancel'),
      );
      $result = $page->renderForm($form);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $plugins, $page, $request;
  
    $plugins->load($request['arg']['update']);
    $plugins->items[$request['arg']['update']]->updateSettings();
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function insert()
  {
  global $plugins, $page, $request;

    if (count($request['arg']['files'])) foreach ($request['arg']['files'] as $name) {
      $plugins->install($name);
      SendNotify(admPluginsAdded.': '.$name, '', false, '', $page->url(array('action'=>'')));
    }
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function add()
  {
  global $db, $page;
    
    $items = $db->select('`plugins`', '', "`name`");
  
    $hnd=opendir(filesRoot.'ext/');
    $i = 0;
    while (($name = readdir($hnd))!==false) if (preg_match("/\.php$/i", $name)) $files[] = substr($name, 0, strlen($name)-4);
    closedir($hnd); 
    if (count($files) && count($items)) foreach($items as $item) for($i = 0; $i < count($files); $i++) {
      if (strtolower($files[$i]) == strtolower($item['name'])) {
        array_splice($files, $i, 1);
        break;
      }
    }
    $form = array(
      'name' => 'FoundPlugins',
      'caption' => admPluginsFound,
      'buttons' => array('ok','cancel'),
      'fields' => array(
        array('type'=>'hidden','name'=>'action','value'=>'insert'),
      ),
    );
    if (count($files)) foreach($files as $file) {
      $hnd = fopen(filesRoot.'ext/'.$file.'.php','r');
      $s = '';
      while (!feof($hnd)) $s .= StripSlashes(fgets($hnd, 1024));
      fclose($hnd);
      $valid = preg_match('/var.*\$title\s*=\s*\'(.+)\'.*\$version\s*=\s*\'(.+)\'.*\$description\s*=\s*\'(.+)\'/Usi',$s, $match);
      if (!$valid) {
        $match[1] = '<span class="admError">';
        $match[2] = $file.'.php';
        $match[3] = admPluginsInvalidFile.'</span>';
      }
      $form['fields'][] = array('type'=>'checkbox','name'=>'files[]','label'=>$match[1].' '.$match[2].' - '.$match[3], 'value'=>$file, 'disabled'=>!$valid);
    }
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function up()
  {
  global $plugins, $page, $db, $request;
  
    $item = $db->selectItem('plugins', "`name`='".$request['arg']['up']."'");
    dbReorderItems('plugins','','name');
    if ($item['position'] > 0) {
      $temp = $db->selectItem('plugins',"`position`='".($item['position']-1)."'");
      $item['position']--;
      $temp['position']++;
      $db->updateItem('plugins', $item, "`name`='".$item['name']."'");
      $db->updateItem('plugins', $temp, "`name`='".$temp['name']."'");
    }
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function down()
  {
  global $plugins, $page, $db, $request;
  
    $item = $db->selectItem('plugins', "`name`='".$request['arg']['down']."'");
    if ($item['position'] < $db->count('plugins')-1) {
      dbReorderItems('plugins','','name');
      $temp = $db->selectItem('plugins',"`position`='".($item['position']+1)."'");
      $item['position']++;
      $temp['position']--;
      $db->updateItem('plugins', $item, "`name`='".$item['name']."'");
      $db->updateItem('plugins', $temp, "`name`='".$temp['name']."'");
    }
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRender()
  {
  global $db, $page, $request;

    if (UserRights($this->access)) {
      $page->title = admPlugins;
      if (isset($request['arg']['update'])) $this->update();
      elseif (isset($request['arg']['toggle'])) $this->toggle();
      elseif (isset($request['arg']['delete'])) $this->delete();
      elseif (isset($request['arg']['id'])) $result = $this->edit();
      elseif (isset($request['arg']['up'])) $this->up();
      elseif (isset($request['arg']['down'])) $this->down();
      elseif (isset($request['arg']['action'])) switch($request['arg']['action']) {
        case 'add': $result = $this->add(); break;
        case 'insert': $this->insert(); break;
      } else {
        $table = array (
          'name' => 'plugins',
          'key' => 'name',
          'sortMode' => 'position',
          'columns' => array(
            array('name' => 'title', 'caption' => admPlugin, 'width' => '90px', 'wrap'=>false),
            array('name' => 'description', 'caption' => admDescription),
            array('name' => 'version', 'caption' => admVersion, 'width'=>'70px','align'=>'center'),
            array('name' => 'type', 'caption' => admType, 'align' => 'center', 'width'=>'80px'),
          ),
          'controls' => array (
            'delete' => '',
            'edit' => '',
            'toggle' => '',
            'position' => ''
          ),
          'tabs' => array(
            'width'=>'180px',
            'items'=>array(
              array('caption'=>admPluginsAdd, 'name'=>'action', 'value'=>'add')
            )
          )
        );
        $result = $page->renderTable($table);
      }
      return $result;
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
?>