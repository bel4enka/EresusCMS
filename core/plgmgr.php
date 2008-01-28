<?php
/**
 * Eresus 2.10
 *
 * Управление модулями расширения
 *
 * Система управления контентом Eresus™
 * © 2004-2007, ProCreat Systems, http://procreat.ru/
 * © 2007, Eresus Group, http://eresus.ru/
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 * @author БерсЪ (fanta@steeka.com)
 */

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
      $result = $plugins->items[arg('id', 'word')]->settings();
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

    $files = array_keys($request['arg']['files']);
    if (count($files)) foreach ($files as $name) if ($request['arg']['files'][$name]) {
      $plugins->install($name);
      SendNotify(admPluginsAdded.': '.$name, array('url' => $page->url(array('action'=>''))));
    }
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function add()
  {
  	global $db, $page;

    $items = $db->select('`plugins`', '', "`name`");
		$installed = array();
		for ($i = 0; $i < count($items); $i++) $installed[] = filesRoot.'ext/'.$items[$i]['name'].'.php';

    $files = glob(filesRoot.'ext/*.php');
    $files = array_diff($files, $installed);

    $page->scripts .= '
      function checkboxes(type)
      {
        var temp = document.forms.FoundPlugins;
        var inp = temp.getElementsByTagName("input");
        var i = 0;
        while (i < inp.length)
        {
          if (inp.item(i).type == "checkbox")
          {
            if (type)
              inp.item(i).checked = true;
            else
              inp.item(i).checked = false;
          }
          i++;
        }
        return false;
      }
    ';

    $form = array(
      'name' => 'FoundPlugins',
      'caption' => admPluginsFound,
      'buttons' => array('ok','cancel'),
      'fields' => array(
        array('type'=>'hidden','name'=>'action','value'=>'insert'),
        array('type'=>'text','value'=>'Выбрать: [<a href="#" onclick="return checkboxes(true);">Все</a>]  [<a href="#" onclick="return checkboxes(false);">Ни одного</a>]'),
      ),
    );
    if (count($files)) foreach($files as $file) {
      $s = file_get_contents($file);
      $name = basename($file, '.php'); # Имя плагина
      $invalid = !preg_match('/class\s+T?'.$name.'\s.*?{(.*?)({|})/is', $s, $s);
      if (!$invalid) {
      	$s = $s[1];
      	preg_match('/\$kernel\s*=\s*(\'|")(.+)\1/', $s, $kernel);
      	preg_match('/\$version\s*=\s*(\'|")(.+)\1/', $s, $version);
      	preg_match('/\$title\s*=\s*(\'|")(.+)\1/', $s, $title);
      	preg_match('/\$description\s*=\s*(\'|")(.+)\1/', $s, $description);
      	#FIXME: Совместимость с версиями до 2.10b2. Надо проверять и наличие $kernel
      	if (count($version) && count($title) && count($description)) {
      		$caption = "{$title[2]} {$version[2]} - {$description[2]}";
      	} else $invalid = admPluginsNotRequiredFields;
      	# PHP < 5.3 have an error treating lowercase 'rc'
      	if (count($kernel) && version_compare(strtoupper($kernel[2]), strtoupper(CMSVERSION), '>')) $invalid = sprintf(admPluginsInvalidVersion, $kernel[2]);
      } else $invalid = admPluginsInvalidFile;
      if ($invalid) $caption = '<span class="admError">'.$name.' - '.$invalid.'</span>';
      $form['fields'][] = array('type'=>'checkbox','name'=>'files['.$name.']','label'=>$caption, 'value'=>true, 'disabled'=>$invalid);
    }
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function up()
  {
  global $plugins, $page, $db, $request;

    dbReorderItems('plugins','','name');
  	$item = $db->selectItem('plugins', "`name`='".$request['arg']['up']."'");
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

    dbReorderItems('plugins','','name');
  	$item = $db->selectItem('plugins', "`name`='".$request['arg']['down']."'");
    if ($item['position'] < $db->count('plugins')-1) {
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
      $result = '';
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