<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# © 2005, 2007, ProCreat Systems
# Web: http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TLinks extends TListContentPlugin {
  var $name = 'links';
  var $type = 'user,content,ondemand';
  var $title = 'Ссылки';
  var $version = '1.01';
  var $description = 'Публикация ссылок';
  var $settings = array(
      'itemsPerPage' => 20,
    );
  var $table = array (
    'name' => 'links',
    'key' => 'id',
    'sortMode' => 'position',
    'sortDesc' => false,
    'columns' => array(
      array('name' => 'caption', 'caption' => 'Заголовок'),
      array('name' => 'url', 'caption' => 'Адрес'),
      array('name' => 'description', 'caption' => 'Описание', 'striptags'=>true, 'maxlength'=>200),
    ),
    'controls' => array (
      'delete' => '',
      'edit' => '',
      'toggle' => '',
      'position' => '',
    ),
    'tabs' => array(
      'width'=>'180px',
      'items'=>array(
       array('caption'=>'Добавить ссылку', 'name'=>'action', 'value'=>'create')
      ),
    ),
    'sql' => "(
      `id` int(10) unsigned NOT NULL auto_increment,
      `section` int(10) unsigned NOT NULL default '0',
      `position` int(10) unsigned NOT NULL default '0',
      `caption` varchar(100) NOT NULL default '',
      `active` tinyint(1) unsigned NOT NULL default '1',
      `description` text NOT NULL,
      `url` varchar(255) NOT NULL default '',
      `banner` varchar(255) NOT NULL default '',
      PRIMARY KEY  (`id`),
      KEY `name` (`section`),
      KEY `section` (`section`),
      KEY `position` (`position`),
      KEY `active` (`active`)
    ) TYPE=MyISAM COMMENT='Links';",
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Внтуренние функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function insert()
  {
    global $db, $request;

    $item = getArgs($db->fields($this->table['name']));
    $item['active'] = UserRights(EDITOR);
    $item['url'] = strtolower($item['url']);
    if (strpos($item['url'],'http://') !== 0) $item['url'] = 'http://'.$item['url'];
    $item['position'] = 0;
    dbShiftItems($this->table['name'], "`section`='".$item['section']."'", +1);
    $db->insert($this->table['name'], $item);
    $item = $db->select($this->table['name'], '', 'id', true, '', true); $item = $item[0];
    sendNotify(admAdded.': <a href="'.httpRoot.'admin.php?mod=content&section='.$item['section'].'&id='.$item['id'].'">'.$item['caption'].'</a><br>'.$item['url']."\n".$item['description'], array('editors'=>defined('FRONTEND_VERSION')));
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['update']."'");
    $item = setArgs($item);
    $item['url'] = strtolower($item['url']);
    if (strpos($item['url'],'http://') !== 0) $item['url'] = 'http://'.$item['url'];
    if (!isset($request['arg']['active'])) $item['active'] = false;
    $db->updateItem($this->table['name'], $item, "`id`='".$request['arg']['update']."'");
    sendNotify(admUpdated.': <a href="'.$page->url().'">'.$item['caption']."</a>\n".$item['url']."\n".$item['description']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminAddItem()
  {
  global $page, $request;

    $form = array(
      'name' => 'AddForm',
      'caption' => 'Добавить ссылку',
      'width' => '100%',
      'fields' => array (
        array ('type'=>'hidden','name'=>'action','value'=>'insert'),
        array ('type' => 'hidden', 'name' => 'section', 'value' => $request['arg']['section']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Название ресурса', 'width' => '100%', 'maxlength' => '100'),
        array ('type' => 'edit', 'name' => 'url', 'label' => 'Адрес', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'edit', 'name' => 'banner', 'label' => 'Баннер', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'html', 'name' => 'description', 'label' => 'Описание', 'height' => '100px'),
      ),
      'buttons' => array('ok', 'cancel'),
    );
    
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminEditItem()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['id']."'");
    $form = array(
      'name' => 'EditForm',
      'caption' => 'Изменить ссылку',
      'width' => '100%',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Название ресурса', 'width' => '100%', 'maxlength' => '100'),
        array ('type' => 'edit', 'name' => 'url', 'label' => 'Адрес', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'edit', 'name' => 'banner', 'label' => 'Баннер', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'html', 'name' => 'description', 'label' => 'Описание', 'height' => '100px'),
        array ('type' => 'divider'),
        array ('type' => 'edit', 'name' => 'section', 'label' => 'Раздел', 'access'=>ADMIN),
        array ('type' => 'checkbox', 'name'=>'active', 'label'=>'Активно'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function settings()
  {
  global $page;
  
    $form = array(
      'name' => 'settings',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'edit','name'=>'itemsPerPage','label'=>'Эелементов на страницу','width'=>'50px'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Пользовательские функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientRenderListItem($item)
  {
    global $page, $plugins;
    
    $result = 
      '<table width="100%" style="margin-bottom: 15px;">'."\n".
      '<tr><td>'.img('style/links/icon.gif', '', '', 16, 8).' <strong><a href="'.$item['url'].'">'.$item['caption']."</a></strong></td></tr>\n".
      '<tr><td>Адрес: <a href="'.$item['url'].'">'.$item['url']."</a></td></tr>\n".
      '<tr><td>'.(!empty($item['banner'])?img($item['banner'], $item['caption'], $item['caption'], 0, 0, 'float: left; margin: 0px 5px 2px 0px;', 0):'').$item['description']."</td></tr>\n".
      "</table>\n";
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>