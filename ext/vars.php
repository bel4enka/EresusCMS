<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Vars (CMS Eresus™ Plugin)
# © 2006, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TVars extends TListContentPlugin {
  var 
    $name = 'vars',
    $title = 'Vars',
    $type = 'client,admin',
    $version = '0.01',
    $description = 'Создание собственных текстовых переменных',
    $settings = array(
    );
  var $table = array (
    'name' => 'vars',
    'key'=> 'name',
    'sortMode' => 'name',
    'sortDesc' => false,
    'columns' => array(
      array('name' => 'name', 'caption' => 'Переменная'),
      array('name' => 'value', 'caption' => 'Значение', 'maxlength' => 100, 'striptags' => true),
    ),
    'controls' => array (
      'delete' => '',
      'edit' => '',
    ),
    'tabs' => array(
      'width'=>'180px',
      'items'=>array(
       array('caption'=>strAdd, 'name'=>'action', 'value'=>'create')
      ),
    ),
    'sql' => "(
      `name` varchar(31) NOT NULL,
      `value` text NOT NULL,
      PRIMARY KEY  (`name`)
    ) TYPE=MyISAM;",
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Стандартные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TVars()
  # производит регистрацию обработчиков событий
  {
  global $plugins;
  
    parent::TPlugin();
    $plugins->events['clientOnPageRender'][] = $this->name;
    $plugins->events['adminOnMenuRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Внутренние функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function insert()
  {
  global $db, $request;

    $item = getArgs($db->fields($this->table['name']));
    $db->insert($this->table['name'], $item);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function update()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`name`='".$request['arg']['update']."'");
    $item = setArgs($item);
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
      'caption' => 'Добавить переменную',
      'width'=>'500px',
      'fields' => array (
        array ('type' => 'hidden', 'name' => 'action', 'value' => 'insert'),
        array ('type' => 'edit', 'name' => 'name', 'label' => 'Переменная $(', 'width' => '300px', 'maxlength' => '31', 'comment' => ')'),
        array ('type' => 'memo', 'name' => 'value', 'label' => 'Значение', 'height' => '10'),
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

    $item = $db->selectItem($this->table['name'], "`name`='".$request['arg']['id']."'");
    $form = array(
      'name' => 'EditForm',
      'caption' => 'Изменить переменную',
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['name']),
        array ('type' => 'edit', 'name' => 'name', 'label' => 'Переменная $(', 'width' => '300px', 'maxlength' => '31', 'comment' => ')'),
        array ('type' => 'memo', 'name' => 'value', 'label' => 'Значение', 'height' => '10'),
      ),
      'buttons' => array('ok', 'cancel'),
    );
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
    global $db;
    
    $items = $db->select($this->table['name']);
    if (count($items)) foreach ($items as $item) {
      $text= str_replace('$('.$item['name'].')', $item['value'], $text);
    }
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminOnMenuRender()
  {
  global $page;
  
    $page->addMenuItem('Расширения', array ("access"  => EDITOR, "link"  => $this->name, "caption"  => 'Переменные', "hint"  => "Управление текстовыми переменными"));
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>