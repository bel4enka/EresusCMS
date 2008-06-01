<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus™ 2.00
# © 2005-2006, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TForms extends TListContentPlugin {
  var $name = 'forms';
  var $type = 'client,admin';
  var $title = 'Формы ввода';
  var $version = '1.00b';
  var $description = 'Создание собственных форм ввода';
  var $table = array (
    'name' => 'forms',
    'key'=> 'id',
    'sortMode' => 'caption',
    'sortDesc' => false,
    'columns' => array(
      array('name' => 'name', 'caption' => 'Имя'),
      array('name' => 'caption', 'caption' => 'Название'),
    ),
    'controls' => array (
      'delete' => '',
      'edit' => '',
      'toggle' => '',
    ),
    'tabs' => array(
      'width'=>'180px',
      'items'=>array(
       array('caption'=>'Новая форма', 'name'=>'action', 'value'=>'create')
      ),
    ),
    'sql' => "(
      `id` int(10) unsigned NOT NULL auto_increment,
      `active` tinyint(1) unsigned NOT NULL default '1',
      `name` varchar(63) NOT NULL default '',
      `caption` varchar(100) NOT NULL default '',
      `actionMode` varchar(15) default NULL,
      `actionValue` varchar(255) default NULL,
      `form` text default NULL,
      PRIMARY KEY  (`id`),
      KEY `active` (`active`),
      KEY `name` (`name`),
      KEY `caption` (`caption`)
    ) TYPE=MyISAM COMMENT='Articles';",
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Стандартные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TForms()
  # производит регистрацию обработчиков событий
  {
  global $plugins, $request;
  
    parent::TListContentPlugin();
    if (isset($request['arg']['plgFormsActionMailto'])) $this->sendMail($request['arg']['plgFormsActionMailto']);
    $plugins->events['clientOnPageRender'][] = $this->name;
    $plugins->events['adminOnMenuRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Внутренние функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function sendMail($form)
  {
    global $request, $db, $plugins;

    $item = $db->selectItem($this->table['name'], "`name`='".$form."' AND `active`='1'");
    if (!is_null($item)) {
      $form = '$form = array('.$item['form'].');';
      eval($form);

      $letter = '';
      $subject = $item['caption'];
      if (count($form['fields'])) foreach($form['fields'] as $field) {
        switch ($field['type']) {
          case 'text':; case 'header':; case 'hidden': break;
          case 'checkbox': if (isset($request['arg'][$field['name']])) $letter .= $field['label']."\n"; break;
          default:
            $letter .= $field['label'].': '.$request['arg'][$field['name']]."\n";
          break;
        }
      }
      if (isset($plugins->items['vistat'])) $letter .= "\n\n--\n".$plugins->items['vistat']->dumpStats();
      sendMail($item['actionValue'], $subject, $letter);
    }
    InfoMessage(isset($form['message'])?$form['message']:'Данные формы отправлены.');
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminInstallScripts()
  {
    global $page;
    $page->scripts .= "
      function ".$this->name."ActionCnange(oSender)
      {
        //var oForm = document.getElementById(sFormName);
        var Row = oSender.parentNode.offsetParent.rows[oSender.parentNode.parentNode.rowIndex+1];
        oSender.form.actionValue.disabled = oSender.value == 'none';
        switch (oSender.value) {
          case 'none': Row.cells[0].innerHTML = ''; break;
          case 'action': Row.cells[0].innerHTML = 'URL'; break;
          case 'mailto': Row.cells[0].innerHTML = 'E-mail'; break;
        }
      }
    ";
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function insert()
  {
  global $db, $request;

    $item = getArgs($db->fields($this->table['name']));
    $item['form'] = AddSlashes($item['form']);
    $db->insert($this->table['name'], $item);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function update()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`name`='".$request['arg']['update']."' AND `active`='1'");
    $item = setArgs($item);
    $item['form'] = str_replace('\\', '\\\\', $item['form']);
    $db->updateItem($this->table['name'], $item, "`name`='".$request['arg']['update']."'");
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
      'caption' => 'Новая форма',
      'width'=>'100%',
      'fields' => array (
        array ('type' => 'hidden', 'name' => 'action', 'value' => 'insert'),
        array ('type' => 'edit', 'name' => 'name', 'label' => 'Имя', 'width' => '200px', 'maxlength' => '63', 'comment' => '(латинские буквы, цифры, "_")'),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Название', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'select', 'name' => 'actionMode', 'label' => 'Тип действия', 'items'=>array('Не выполнять действий','Вызвать скрипт','Отправить письмо'), 'values'=>array('none','action','mailto'), 'extra'=>'onchange="'.$this->name.'ActionCnange(this)"'),
        array ('type' => 'edit', 'name' => 'actionValue', 'label' => '', 'width' => '100%', 'maxlength' => 255, 'disabled'=>true),
        array ('type' => 'memo', 'name' => 'form', 'label' => 'Форма', 'height' => '20'),
      ),
      'buttons' => array('ok', 'cancel'),
    );
    $this->adminInstallScripts();
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminEditItem()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`".$this->table['key']."`='".$request['arg']['id']."'");
    switch($item['actionMode']) {
      case 'action': $actionLabel = 'URL'; break;
      case 'mailto': $actionLabel = 'E-mail'; break;
      default: $actionLabel = '';
    }
    $form = array(
      'name' => 'EditForm',
      'caption' => 'Изменить форму',
      'width' => '100%',
      'fields' => array (
        array ('type' => 'hidden','name' => 'update', 'value'=>$item['name']),
        array ('type' => 'edit', 'name' => 'name', 'label' => 'Имя', 'width' => '200px', 'maxlength' => '63', 'comment' => '(латинские буквы, цифры, "_")'),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Название', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'select', 'name' => 'actionMode', 'label' => 'Тип действия', 'items'=>array('Не выполнять действий','Вызвать скрипт','Отправить письмо'), 'values'=>array('none','action','mailto'), 'extra'=>'onchange="'.$this->name.'ActionCnange(this)"'),
        array ('type' => 'edit', 'name' => 'actionValue', 'label' => $actionLabel, 'width' => '100%', 'maxlength' => 255, 'disabled'=>$item['actionMode'] == 'none'),
        array ('type' => 'memo', 'name' => 'form', 'label' => 'Форма', 'height' => '20'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $this->adminInstallScripts();
    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRender()
  {
    return $this->adminRenderContent();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Обработчики событий
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientOnPageRender($text)
  {
    global $db, $page;
    
    preg_match_all('/(\$\(|{%)plgForms:([\d\w_]+)(\)|})/', $text, $matches);
    for($i = 0; $i < count($matches[0]); $i++) {
      $item = $db->selectItem($this->table['name'], "`name`='".$matches[2][$i]."' AND `active`='1'");
      if (!is_null($item)) {
        $form = '$form = array('.$item['form'].');';
        eval($form);
        switch ($item['actionMode']) {
          case 'action': $form['action'] = $item['actionValue']; break;
          case 'mailto': array_unshift($form['fields'], array('type'=>'hidden', 'name'=>'plgFormsActionMailto', 'value'=>$item['name'])); break;
        }
        $text = str_replace($matches[0][$i], $page->renderForm($form), $text);
      }
    }
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminOnMenuRender()
  {
  global $page;
  
    $page->addMenuItem(admExtensions, array ('access'  => ADMIN, 'link'  => $this->name, 'caption'  => $this->title, 'hint'  => $this->description));
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>