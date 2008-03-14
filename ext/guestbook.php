<?php
/**
 * Гостевая
 *
 * Eresus 2
 *
 * Вызов других плагинов посредством макросов.
 *
 * @version 1.06
 *
 * @copyright   2005-2007, ProCreat Systems, http://procreat.ru/
 * @copyright   2007-2008, Eresus Group, http://eresus.ru/
 * @license     http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @maintainer  Mikhail Krasilnikov <mk@procreat.ru>
 * @author      Mikhail Krasilnikov <mk@procreat.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо по вашему выбору с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 */
class TGuestbook extends TListContentPlugin {
  var $name = 'guestbook';
  var $type = 'client,content,ondemand';
  var $title = 'Гостевая';
  var $version = '1.06';
  var $description = 'Гостевая книга';
  var $settings = array(
  	'caption' => 'Добавить свой отзыв',
    'itemsPerPage' => 20,
    'template' => '<div><b>$(name)</b> :: $(posted)<br>$(text)</div>',
    'moderation' => false,
  	'blacklist' => '',
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
 /**
  * Добавление записи
  *
  */
  function insert()
  {
    global $Eresus, $page;

    if ($Eresus->request['method'] == 'POST') {
      $item = GetArgs($Eresus->db->fields($this->table['name']));
      if (empty($item['name']) && empty($item['mail'])) {
        $item['name'] = arg('_name');
        if (!preg_match("/(^|\s){$item['name']}($|\s|,)/i", $this->settings['blacklist'])) {
	        $item['mail'] = arg('_mail');
	        $item['posted'] = gettime();
	        $item['active'] = $this->settings['moderation'] ? defined('ADMINUI') : true;
	        if (!empty($item['name']) && !empty($item['text'])) {
	          $Eresus->db->insert('guestbook', $item);
	          if ($this->settings['moderation']) InfoMessage('Сообщение отправлено редакторам сайта');
	          $item['id'] = $Eresus->db->getInsertedID();
	          sendNotify("<b>".$item['name']."</b> (".$item['mail'].") пишет:\n".$item['text']);
	        }
        } else ErrorMessage('Имя "'.$item['name'].'" запрещено к использованию администраторами сайта!');
      }
    }
  }
  //-----------------------------------------------------------------------------
  function update()
  {
  	global $Eresus;

    $item = getArgs($Eresus->db->selectItem('guestbook', "`id`='".arg('update', 'int')."'"));
    $Eresus->db->updateItem('guestbook', $item, "`id`='".$item['id']."'");
    sendNotify(admUpdated."\nАвтор:  <strong>".$item['name']."</strong>\n".$item['text']);
    goto(arg('submitURL'));
  }
  //-----------------------------------------------------------------------------
  function delete()
  {
  	global $Eresus, $page;

    $item = $Eresus->db->selectItem('guestbook', "`id`='".arg('delete')."'");
    $Eresus->db->delete('guestbook', "`id`='".$item['id']."'");
    sendNotify(admDeleted."\nАвтор:  <strong>".$item['name']."</strong>\n".$item['text']);
    goto($page->url());
  }
  //-----------------------------------------------------------------------------
  function adminEditItem()
  {
  	global $Eresus, $page;

    $item = $Eresus->db->selectItem('guestbook', "`id`='".arg('id', 'int')."'");
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
  //-----------------------------------------------------------------------------
  function settings()
  {
  	global $page;

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
        array('type'=>'memo','name'=>'blacklist','label'=>'Запрещённые имена (перечислите через запятую)','height'=>'5'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  //-----------------------------------------------------------------------------
  function clientRenderListItem($item)
  {
    $item['posted'] = FormatDate($item['posted'], DATETIME_LONG);
    $item['text'] = nl2br($item['text']);
    $result = $this->replaceMacros($this->settings['template'], $item);
    return $result;
  }
  //-----------------------------------------------------------------------------
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
        array ('type' => 'edit', 'name' => '_name', 'label' => 'Ваше имя', 'width' => '100%', 'maxlength' => '63', 'pattern'=>'/.+/', 'errormsg'=>'Имя не может быть пустым.'),
        array ('type' => 'edit', 'name' => '_mail', 'label' => 'E-mail', 'width' => '100%', 'maxlength' => '63'),
        array ('type' => 'memo', 'name' => 'text', 'label' => 'Сообщение', 'height' => '5', 'pattern'=>'/.+/', 'errormsg'=>'Сообщение не может быть пустым.'),
        array ('type' => 'text', 'value' => '<div style="display: none;"><input type="text" name="name" /><input type="text" name="mail" /></div>'),
      ),
      'buttons' => array('ok'),
    );
    $result = parent::clientRenderList();
    $result .= $page->renderForm($form);
    return $result;
  }
  //-----------------------------------------------------------------------------
  function clientRenderItem()
  {
    global $Eresus, $page;

    $item = $Eresus->db->selectItem($this->table['name'], "`id`='".$page->topic."'");
    $item['posted'] = FormatDate($item['posted'], DATETIME_LONG);
    $item['text'] = nl2br($item['text']);
    $result = $this->replaceMacros($this->settings['template'], $item);
    return $result;
  }
  //-----------------------------------------------------------------------------
  function clientRenderContent()
  {
    global $Eresus;

    if (arg('action') == 'insert') {
      $this->insert();
      goto($Eresus->request['referer']);
    }
    $result = parent::clientRenderContent();
    return $result;
  }
  //-----------------------------------------------------------------------------
}

?>