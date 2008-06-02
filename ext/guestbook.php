<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus™
# © 2005-2006, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TGuestbook extends TListContentPlugin {
  var 
    $name = 'guestbook',
    $type = 'client,content,ondemand',
    $title = 'Гостевая',
    $version = '1.04',
    $description = 'Гостевая книга',
    $settings = array(
      'caption' => 'Добавить свой отзыв',
      'itemsPerPage' => 20,
      'template' => '<div><b>$(name)</b> :: $(posted)<br>$(text)</div>',
      'moderation' => false,
    ),
    $table = array (
      'name' => 'guestbook',
      'key'=> 'id',
      'sortMode' => 'posted',
      'sortDesc' => true,
      'columns' => array(
        array('name' => 'name', 'caption' => 'Автор'),
        array('name' => 'text', 'caption' => 'Текст', 'maxlength'=>200, 'striptags'=>true),
        array('name' => 'posted', 'caption' => 'Дата'),
      ),
      'controls' => array (
        'delete' => '',
        'edit' => '',
        'toggle' => '',
      ),
      'sql' => "(
        `id` int(10) unsigned NOT NULL auto_increment,
        `section` int(10) unsigned NOT NULL default '0',
        `active` tinyint(1) unsigned NOT NULL default '0',
        `name` varchar(63) NOT NULL default '',
        `mail` varchar(63) NOT NULL default '',
        `posted` datetime default NULL,
        `text` text NOT NULL,
        PRIMARY KEY  (`id`),
        KEY `section` (`section`),
        KEY `active` (`active`)
        ) TYPE=MyISAM COMMENT='Guestbook'
       ",
    );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Внутренние функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function insert()
  {
  global $db, $page, $session, $request;

    $item = GetArgs($db->fields($this->table['name']));
    $item['posted'] = gettime();
    $item['active'] = $this->settings['moderation']?defined('ADMINUI'):true;
    if (!empty($item['name']) && !empty($item['text'])) {
      $db->insert('guestbook', $item);
      if ($this->settings['moderation']) $session['message'] = 'Сообщение отправлено редакторам сайта';
      $item['id'] = $db->getInsertedID();
      sendNotify("<b>".$item['name']."</b> (".$item['mail'].") пишет:\n".$item['text']);
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $request;

    $item = setArgs($db->selectItem('guestbook', "`id`='".$request['arg']['update']."'"));
    $db->updateItem('guestbook', $item, "`id`='".$item['id']."'");
    sendNotify(admUpdated."\nАвтор:  <strong>".$item['name']."</strong>\n".$item['text']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function delete()
  {
  global $db, $page, $request;

    $item = $db->selectItem('guestbook', "`id`='".$request['arg']['delete']."'");
    $db->delete('guestbook', "`id`='".$item['id']."'");
    sendNotify(admDeleted."\nАвтор:  <strong>".$item['name']."</strong>\n".$item['text']);
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminEditItem()
  {
  global $db, $page, $request;

    $item = $db->selectItem('guestbook', "`id`='".$request['arg']['id']."'");
    $form = array(
      'name'=>'EditForm',
      'caption' => 'Изменить сообщение',
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
        array ('type' => 'edit', 'name' => 'name', 'label' => 'Имя', 'width' => '100%', 'maxlength' => '63'),
        array ('type' => 'edit', 'name' => 'mail', 'label' => 'E-mail', 'width' => '100%', 'maxlength' => '636'),
        array ('type' => 'memo', 'name' => 'text', 'label' => 'Текст', 'height' => '10'),
        array ('type' => 'divider'),
        array ('type' => 'edit', 'name' => 'section', 'label' => 'Раздел', 'access'=>ADMIN),
        array ('type' => 'edit', 'name'=>'posted', 'label'=>'Написано'),
      ),
      'buttons' => array('ok', 'cancel', 'apply'),
    );
    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function settings()
  {
  global $page, $db, $plugins;
  
    $form = array(
      'name' => 'SettingsForm',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'edit','name'=>'caption','label'=>'Заголовок','width'=>'100%', 'maxlength'=>'127'),
        array('type'=>'edit','name'=>'itemsPerPage','label'=>'','width'=>'30px', 'maxlength'=>'2', 'comment' => 'отзывов на страницу'),
        array('type'=>'checkbox', 'name' => 'moderation', 'label' => 'Предмодерация'),
        array('type'=>'memo','name'=>'template','label'=>'Шаблон сообщения','height'=>'5'),
        array('type'=>'text', 'value'=>'Макросы:<br /><b>$(name)</b> - имя<br /><b>$(mail)</b> - e-mail<br /><b>$(posted)</b> - время сообщения<br /><b>$(text)</b> - текст сооббщения'),
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
    $item['posted'] = FormatDate($item['posted'], DATETIME_LONG);
    $item['text'] = nl2br($item['text']);
    $result = $this->replaceMacros($this->settings['template'], $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientRenderList()
  {
  global $page;
  
    $form = array (
      'name' => 'FormAdd',
      'caption' => $this->settings['caption'],
      'width' => '400px',
      'fields' => array (
        array ('type' => 'hidden', 'name' => 'action', 'value' => 'insert'),
        array ('type' => 'hidden', 'name' => 'section', 'value' => $page->id),
        array ('type' => 'edit', 'name' => 'name', 'label' => 'Ваше имя', 'width' => '100%', 'maxlength' => '63', 'pattern'=>'/.+/', 'errormsg'=>'Имя не может быть пустым.'),
        array ('type' => 'edit', 'name' => 'mail', 'label' => 'E-mail', 'width' => '100%', 'maxlength' => '63'),
        array ('type' => 'memo', 'name' => 'text', 'label' => 'Сообщение', 'height' => '5', 'pattern'=>'/.+/', 'errormsg'=>'Сообщение не может быть пустым.'),
      ),
      'buttons' => array('ok'),
    );
    $result = parent::clientRenderList();
    $result .= $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientRenderItem()
  {
    global $db, $page;
    
    $item = $db->selectItem($this->table['name'], "`id`='".$page->topic."'");
    $item['posted'] = FormatDate($item['posted'], DATETIME_LONG);
    $item['text'] = nl2br($item['text']);
    $result = $this->replaceMacros($this->settings['template'], $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientRenderContent()
  {
    global $request;

    if (arg('action') == 'insert') {
      $this->insert(); 
      goto($request['referer']);
    }
    $result = parent::clientRenderContent();
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>