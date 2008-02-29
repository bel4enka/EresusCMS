<?php
/**
 * Eresus 2.11
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
  	global $page, $Eresus;

    $item = $Eresus->db->selectItem('plugins', "`name`='".$Eresus->request['arg']['toggle']."'");
    $item['active'] = !$item['active'];
    $Eresus->db->updateItem('plugins', $item, "`name`='".$Eresus->request['arg']['toggle']."'");
    SendNotify(($item['active']?admActivated:admDeactivated).': '.$item['title']);
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function delete()
  {
  global $page, $Eresus;

    $Eresus->plugins->load($Eresus->request['arg']['delete']);
    $Eresus->plugins->uninstall($Eresus->request['arg']['delete']);
    SendNotify(admDeleted.': '.$Eresus->plugins->list[$Eresus->request['arg']['delete']]['title']);
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function edit()
  {
  global $page, $Eresus;

    $Eresus->plugins->load($Eresus->request['arg']['id']);
    if (method_exists($Eresus->plugins->items[$Eresus->request['arg']['id']], 'settings')) {
      $result = $Eresus->plugins->items[arg('id', 'word')]->settings();
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
  global $page, $Eresus;

    $Eresus->plugins->load($Eresus->request['arg']['update']);
    $Eresus->plugins->items[$Eresus->request['arg']['update']]->updateSettings();
    goto($Eresus->request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function insert()
  {
  global $page, $Eresus;
//    unset($Eresus->session['addplugins']);
//var_dump($Eresus->session['addplugins']);
    if (!isset($Eresus->session['addplugins']) || (count($Eresus->session['addplugins']) == 0))
    {
      $Eresus->session['addplugins'] = array_keys($Eresus->request['arg']['files']);
    }
//var_dump($Eresus->session['addplugins']);

//    $files = array_keys($Eresus->request['arg']['files']);

    if (count($Eresus->session['addplugins']))
      foreach ($Eresus->session['addplugins'] as $k => $name)
//        if (isset($Eresus->request['arg']['files']) && isset($Eresus->request['arg']['files'][$name]))
        if (isset($Eresus->session['addplugins'][$k]))
        {
          $Eresus->plugins->install($name);
          unset($Eresus->session['addplugins'][$k]);
          SendNotify(admPluginsAdded.': '.$name, array('url' => $page->url(array('action'=>''))));
        }

    goto($Eresus->request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function add()
  {
  	global $page, $Eresus;

    $items = $Eresus->db->select('`plugins`', '', "`name`");
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
      'width' => '600px',
      'buttons' => array('ok','cancel'=>array('label' => 'Отмена', 'url' => 'http://san-dis.ru/admin.php?mod=plgmgr')),
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
      	# PHP < 5.3 does not understatnd lowercase 'rc' but other letters must be only lowercase
      	if (isset($kernel[2])) $v_plugin =  str_replace('rc','RC', $kernel[2]);
      	else $v_plugin =  str_replace('rc','RC', $kernel);

      	$v_kernel =  str_replace('rc','RC', CMSVERSION);
      	if (count($kernel) && version_compare($v_plugin, $v_kernel, '>')) $invalid = sprintf(admPluginsInvalidVersion, $kernel[2]);
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
  global $page, $Eresus;

    dbReorderItems('plugins','','name');
  	$item = $Eresus->db->selectItem('plugins', "`name`='".$Eresus->request['arg']['up']."'");
    if ($item['position'] > 0) {
      $temp = $Eresus->db->selectItem('plugins',"`position`='".($item['position']-1)."'");
      $item['position']--;
      $temp['position']++;
      $Eresus->db->updateItem('plugins', $item, "`name`='".$item['name']."'");
      $Eresus->db->updateItem('plugins', $temp, "`name`='".$temp['name']."'");
    }
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function down()
  {
  global $page, $Eresus;

    dbReorderItems('plugins','','name');
  	$item = $Eresus->db->selectItem('plugins', "`name`='".$Eresus->request['arg']['down']."'");
    if ($item['position'] < $Eresus->db->count('plugins')-1) {
      $temp = $Eresus->db->selectItem('plugins',"`position`='".($item['position']+1)."'");
      $item['position']++;
      $temp['position']--;
      $Eresus->db->updateItem('plugins', $item, "`name`='".$item['name']."'");
      $Eresus->db->updateItem('plugins', $temp, "`name`='".$temp['name']."'");
    }
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRender()
  {
  global $page, $Eresus;

    if (UserRights($this->access)) {
      $result = '';
      $page->title = admPlugins;
      if (isset($Eresus->request['arg']['update'])) $this->update();
      elseif (isset($Eresus->request['arg']['toggle'])) $this->toggle();
      elseif (isset($Eresus->request['arg']['delete'])) $this->delete();
      elseif (isset($Eresus->request['arg']['id'])) $result = $this->edit();
      elseif (isset($Eresus->request['arg']['up'])) $this->up();
      elseif (isset($Eresus->request['arg']['down'])) $this->down();
      elseif (isset($Eresus->request['arg']['action'])) switch($Eresus->request['arg']['action']) {
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