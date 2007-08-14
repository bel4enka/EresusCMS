<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.10
# © 2004-2007, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Управление оформлением
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TThemes {
  var $access = ADMIN;
  var $tabs = array(
    'width' => admThemesTabWidth,
    'items' => array(
      array('caption' => admThemesTemplates, 'name' => 'section', 'value' => 'templates'),
      array('caption' => admThemesStandard, 'name' => 'section', 'value' => 'std'),
      array('caption' => admThemesStyles, 'name' => 'section', 'value' => 'css'),
    ),
  );
  var $stdTemplates = array(
    'SectionListItem' => array('caption' => admTemplList, 'hint' => admTemplListItemLabel),
    '400' => array('caption' => 'HTTP 400 - Bad Request'),
    '401' => array('caption' => 'HTTP 401 - Unauthorized'),
    '402' => array('caption' => 'HTTP 402 - Payment Required'),
    '403' => array('caption' => 'HTTP 403 - Forbidden'),
    '404' => array('caption' => 'HTTP 404 - Not Found'),
    '405' => array('caption' => 'HTTP 405 - Method Not Allowed'),
    '406' => array('caption' => 'HTTP 406 - Not Acceptable'),
    '407' => array('caption' => 'HTTP 407 - Proxy Authentication Required'),
    '408' => array('caption' => 'HTTP 408 - Request Timeout'),
    '409' => array('caption' => 'HTTP 409 - Conflict'),
    '410' => array('caption' => 'HTTP 410 - Gone'),
    '411' => array('caption' => 'HTTP 411 - Length Required'),
    '412' => array('caption' => 'HTTP 412 - Precondition Failed'),
    '413' => array('caption' => 'HTTP 413 - Request Entity Too Large'),
    '414' => array('caption' => 'HTTP 414 - Request-URI Too Long'),
    '415' => array('caption' => 'HTTP 415 - Unsupported Media Type'),
    '416' => array('caption' => 'HTTP 416 - Requested Range Not Satisfiable'),
    '417' => array('caption' => 'HTTP 417 - Expectation Failed'),
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ШАБЛОНЫ / TEMPLATES
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionTemplatesInsert()
  {
    global $request;
    
    $file = "<!-- ".$request['arg']['description']." -->\r\n\r\n".$request['arg']['html'];
    $fp = fopen(filesRoot.'templates/'.$request['arg']['filename'].'.tmpl', 'w');
    fwrite($fp, $file);
    fclose($fp);
    SendNotify((isset($request['update'])?admUpdated:admAdded).': '.$request['arg']['filename'].'.tmpl');
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionTemplatesUpdate()
  {
    global $request;
    $request['update'] = true;
    $this->sectionTemplatesInsert();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionTemplatesDelete()
  {
    global $request, $page;
    
    $filename = filesRoot.'templates/'.$request['arg']['delete'];
    if (file_exists($filename)) unlink($filename);
    SendNotify(admDeleted.': '.$request['arg']['delete']);
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionTemplatesAdd()
  {
    global $page, $request;
    
    $form = array(
      'name' => 'addForm',
      'caption' => $page->title.admTDiv.admAdd,
      'width' => '100%',
      'fields' => array (
        array('type'=>'hidden','name'=>'action', 'value'=>'insert'),
        array('type'=>'hidden','name'=>'section', 'value'=>$request['arg']['section']),
        array('type'=>'edit','name'=>'filename','label'=>admThemesFilenameLabel, 'width'=>'200px', 'comment'=>'.tmpl'),
        array('type'=>'edit','name'=>'description','label'=>admThemesDescriptionLabel, 'width'=>'100%'),
        array('type'=>'memo','name'=>'html', 'height'=>'30', 'syntax' => 'html'),
      ),
      'buttons' => array('ok','cancel'),
    );
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionTemplatesEdit()
  {
    global $page, $request;
    
    $item['filename'] = $request['arg']['id'];
    $item['html'] = file_get_contents(filesRoot.'templates/'.$item['filename']);
    preg_match('/<!--(.*?)-->/', $item['html'], $item['description']);
    $item['description'] = trim($item['description'][1]);
    $item['filename'] = substr($item['filename'], 0, strrpos($item['filename'], '.'));
    $item['html'] = trim(substr($item['html'], strpos($item['html'], "\n")));
    $form = array(
      'name' => 'editForm',
      'caption' => $page->title.admTDiv.admEdit,
      'width' => '100%',
      'fields' => array (
        array('type'=>'hidden','name'=>'action', 'value'=>'update'),
        array('type'=>'hidden','name'=>'section', 'value'=>$request['arg']['section']),
        array('type'=>'hidden','name'=>'filename'),
        array('type'=>'edit','name'=>'_filename','label'=>admThemesFilenameLabel, 'width'=>'200px', 'comment'=>'.tmpl', 'disabled' => true, 'value' => $item['filename']),
        array('type'=>'edit','name'=>'description','label'=>admThemesDescriptionLabel, 'width'=>'100%'),
        array('type'=>'memo','name'=>'html', 'height'=>'30', 'syntax' => 'html'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionTemplatesList()
  {
    global $page;
    
    $table = array(
      'name' => 'templates',
      'key'=> 'filename',
      'sortMode' => 'filename',
      'sortDesc' => false,
      'columns' => array(
        array('name' => 'description', 'caption' => 'Описание'),
        array('name' => 'filename', 'caption' => 'Имя файла'),
      ),
      'controls' => array (
        'delete' => '',
        'edit' => '',
      ),
      'tabs' => array(
        'width'=>'120px',
        'items'=>array(
          array('caption'=>admAdd, 'name'=>'action', 'value'=>'add'),
        )
      ),
    );
    # Загружаем список шаблонов
    $dir = filesRoot.'templates/';
    $hnd = opendir($dir);
    while (($filename = readdir($hnd))!==false) if (preg_match('/.*\.tmpl$/', $filename)) {
      $description = file_get_contents($dir.$filename);
      preg_match('/<!--(.*?)-->/', $description, $description);
      $description = trim($description[1]);
      $items[] = array(
        'filename' => $filename,
        'description' => $description,
      );
    }
    closedir($hnd); 
    $result = $page->renderTable($table, $items);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionTemplates() 
  {
  global $page, $request;
  
    $page->title .= admTDiv.admThemesTemplates;

    if (!isset($request['arg']['action'])) $request['arg']['action'] = '';
    switch($request['arg']['action']) {
      case 'update': $result = $this->sectionTemplatesUpdate(); break;
      case 'insert': $result = $this->sectionTemplatesInsert(); break;
      case 'add': $result = $this->sectionTemplatesAdd(); break;
      default: 
        if (isset($request['arg']['delete'])) $result = $this->sectionTemplatesDelete();
        elseif (isset($request['arg']['id'])) $result = $this->sectionTemplatesEdit();
        else $result = $this->sectionTemplatesList();
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # СТАНДАРТНЫЕ ШАБЛОНЫ / STANDARD TEMPLATES
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStdInsert()
  {
    global $request;
    
    $file = "<!-- ".$this->stdTemplates[$request['arg']['name']]['caption']." -->\r\n\r\n".$request['arg']['html'];
    $fp = fopen(filesRoot.'templates/std/'.$request['arg']['name'].'.tmpl', 'w');
    fwrite($fp, $file);
    fclose($fp);
    SendNotify((isset($request['update'])?admUpdated:admAdded).': std/'.$request['arg']['name'].'.tmpl');
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStdUpdate()
  {
    global $request;
    $request['update'] = true;
     $this->sectionStdInsert();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStdDelete()
  {
    global $request, $page;
    
    $filename = filesRoot.'templates/std/'.$request['arg']['delete'];
    if (file_exists($filename)) unlink($filename);
    SendNotify(admDeleted.': std/'.$request['arg']['delete']);
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStdAdd()
  {
    global $page, $request;
    
    $values = array();
    $items = array();
    $jsArray = "var aTemplates = Array();\n";
    foreach($this->stdTemplates as $key => $item) {
      if (!isset($hint)) $hint = isset($item['hint'])?$item['hint']:'';
      $values[] = $key;
      $items[] = $item['caption'];
      $jsArray .= "aTemplates['".$key."'] = '".(isset($item['hint'])?$item['hint']:'')."'\n";
    }

    $page->scripts .= $jsArray."
      function onTemplateNameChange()
      {
        document.getElementById('templateHint').innerHTML = aTemplates[document.addForm.elements.namedItem('name').value];
      }
    ";
    $form = array(
      'name' => 'addForm',
      'caption' => $page->title.admTDiv.admAdd,
      'width' => '100%',
      'fields' => array (
        array('type'=>'hidden','name'=>'action', 'value'=>'insert'),
        array('type'=>'hidden','name'=>'section', 'value'=>$request['arg']['section']),
        array('type'=>'select','name'=>'name','label'=>admThemesTemplate, 'values'=>$values, 'items'=>$items, 'extra' => 'onChange="onTemplateNameChange()"'),
        array('type'=>'text','name'=>'hint', 'value' => $hint, 'extra' => 'id="templateHint"'),
        array('type'=>'memo','name'=>'html', 'height'=>'30', 'syntax' => 'html'),
      ),
      'buttons' => array('ok','cancel'),
    );
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStdEdit()
  {
    global $page, $request;
    
    $item['name'] = $request['arg']['id'];
    $item['html'] = trim(file_get_contents(filesRoot.'templates/std/'.$item['name']));
    $item['name'] = substr($item['name'], 0, strrpos($item['name'], '.'));
    $item['html'] = trim(substr($item['html'], strpos($item['html'], "\n")));
    $form = array(
      'name' => 'editForm',
      'caption' => $page->title.admTDiv.admEdit,
      'width' => '100%',
      'fields' => array (
        array('type'=>'hidden','name'=>'action', 'value'=>'update'),
        array('type'=>'hidden','name'=>'section', 'value'=>$request['arg']['section']),
        array('type'=>'hidden','name'=>'name'),
        array('type'=>'edit','name'=>'_name','label'=>admThemesFilenameLabel, 'width'=>'200px', 'comment'=>'.tmpl ('.$this->stdTemplates[$item['name']]['caption'].')', 'disabled' => true, 'value'=>$item['name']),
        array('type'=>'text','name'=>'hint', 'value' => isset($this->stdTemplates[$item['name']]['hint'])?$this->stdTemplates[$item['name']]['hint']:'', 'extra' => 'id="templateHint"'),
        array('type'=>'memo','name'=>'html', 'height'=>'30', 'syntax' => 'html'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStdList()
  {
    global $page;
    
    $table = array(
      'name' => 'templates',
      'key'=> 'filename',
      'sortMode' => 'filename',
      'sortDesc' => false,
      'columns' => array(
        array('name' => 'description', 'caption' => 'Описание'),
        array('name' => 'filename', 'caption' => 'Имя файла'),
      ),
      'controls' => array (
        'delete' => '',
        'edit' => '',
      ),
      'tabs' => array(
        'width'=>'120px',
        'items'=>array(
          array('caption'=>admAdd, 'name'=>'action', 'value'=>'add'),
        )
      ),
    );
    # Загружаем список шаблонов
    $dir = filesRoot.'templates/std/';
    $hnd = opendir($dir);
    while (($filename = readdir($hnd))!==false) if (preg_match('/.*\.tmpl$/', $filename)) {
      $description = file_get_contents($dir.$filename);
      preg_match('/<!--(.*?)-->/', $description, $description);
      $description = trim($description[1]);
      $items[] = array(
        'filename' => $filename,
        'description' => $description,
      );
    }
    closedir($hnd); 
    $result = $page->renderTable($table, $items);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStd() 
  {
  global $page, $request;
  
    $page->title .= admTDiv.admThemesStandard;

    if (!isset($request['arg']['action'])) $request['arg']['action'] = '';
    switch($request['arg']['action']) {
      case 'update': $result = $this->sectionStdUpdate(); break;
      case 'insert': $result = $this->sectionStdInsert(); break;
      case 'add': $result = $this->sectionStdAdd(); break;
      default: 
        if (isset($request['arg']['delete'])) $result = $this->sectionStdDelete();
        if (isset($request['arg']['id'])) $result = $this->sectionStdEdit();
        else $result = $this->sectionStdList();
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # СТИЛИ / CSS
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStylesInsert()
  {
    global $request;
    
    $file = "/* ".$request['arg']['description']." */\r\n\r\n".$request['arg']['html'];
    $fp = fopen(filesRoot.'style/'.$request['arg']['filename'].'.css', 'w');
    fwrite($fp, $file);
    fclose($fp);
    SendNotify((isset($request['update'])?admUpdated:admAdded).': '.$request['arg']['filename'].'.css');
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStylesUpdate()
  {
    global $request;
    $request['update'] = true;
    $this->sectionStylesInsert();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStylesDelete()
  {
    global $request, $page;
    
    $filename = filesRoot.'style/'.$request['arg']['delete'];
    if (file_exists($filename)) unlink($filename);
    SendNotify(admDeleted.': '.$request['arg']['delete']);
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStylesAdd()
  {
    global $page, $request;
    
    $form = array(
      'name' => 'addForm',
      'caption' => $page->title.admTDiv.admAdd,
      'width' => '100%',
      'fields' => array (
        array('type'=>'hidden','name'=>'action', 'value'=>'insert'),
        array('type'=>'hidden','name'=>'section', 'value'=>$request['arg']['section']),
        array('type'=>'edit','name'=>'filename','label'=>admThemesFilenameLabel, 'width'=>'200px', 'comment'=>'.css'),
        array('type'=>'edit','name'=>'description','label'=>admThemesDescriptionLabel, 'width'=>'100%'),
        array('type'=>'memo','name'=>'html', 'height'=>'30', 'syntax' => 'css'),
      ),
      'buttons' => array('ok','cancel'),
    );
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStylesEdit()
  {
    global $page, $request;
    
    $item['filename'] = $request['arg']['id'];
    $item['html'] = trim(file_get_contents(filesRoot.'style/'.$item['filename']));
    preg_match('|/\*(.*?)\*/|', $item['html'], $item['description']);
    $item['description'] = trim($item['description'][1]);
    $item['filename'] = substr($item['filename'], 0, strrpos($item['filename'], '.'));
    $item['html'] = trim(substr($item['html'], strpos($item['html'], "\n")));
    $form = array(
      'name' => 'editForm',
      'caption' => $page->title.admTDiv.admEdit,
      'width' => '100%',
      'fields' => array (
        array('type'=>'hidden','name'=>'action', 'value'=>'update'),
        array('type'=>'hidden','name'=>'section', 'value'=>$request['arg']['section']),
        array('type'=>'hidden','name'=>'filename'),
        array('type'=>'edit','name'=>'_filename','label'=>admThemesFilenameLabel, 'width'=>'200px', 'comment'=>'.css', 'disabled' => true, 'value' => $item['filename']),
        array('type'=>'edit','name'=>'description','label'=>admThemesDescriptionLabel, 'width'=>'100%'),
        array('type'=>'memo','name'=>'html', 'height'=>'30', 'syntax' => 'css'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStylesList()
  {
    global $page;
    
    $table = array(
      'name' => 'Styles',
      'key'=> 'filename',
      'sortMode' => 'filename',
      'sortDesc' => false,
      'columns' => array(
        array('name' => 'description', 'caption' => 'Описание'),
        array('name' => 'filename', 'caption' => 'Имя файла'),
      ),
      'controls' => array (
        'delete' => '',
        'edit' => '',
      ),
      'tabs' => array(
        'width'=>'120px',
        'items'=>array(
          array('caption'=>admAdd, 'name'=>'action', 'value'=>'add'),
        )
      ),
    );
    # Загружаем список шаблонов
    $dir = filesRoot.'style/';
    $hnd = opendir($dir);
    while (($filename = readdir($hnd))!==false) if (preg_match('/.*\.css$/', $filename)) {
      $description = file_get_contents($dir.$filename);
      preg_match('|/\*(.*?)\*/|', $description, $description);
      $description = trim($description[1]);
      $items[] = array(
        'filename' => $filename,
        'description' => $description,
      );
    }
    closedir($hnd); 
    $result = $page->renderTable($table, $items);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionStyles() 
  {
  global $page, $request;
  
    $page->title .= admTDiv.admThemesStyles;
    if (!isset($request['arg']['action'])) $request['arg']['action'] = '';
    switch($request['arg']['action']) {
      case 'update': $result = $this->sectionStylesUpdate(); break;
      case 'insert': $result = $this->sectionStylesInsert(); break;
      case 'add': $result = $this->sectionStylesAdd(); break;
      default: 
        if (isset($request['arg']['delete'])) $result = $this->sectionStylesDelete();
        elseif (isset($request['arg']['id'])) $result = $this->sectionStylesEdit();
        else $result = $this->sectionStylesList();
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRender()
  {
  global $page, $request;

    $result = '';
    if (UserRights($this->access)) {
      $result .= $page->renderTabs($this->tabs);
      if (!isset($request['arg']['section'])) $request['arg']['section'] = 'main';
      switch ($request['arg']['section']) {
        case 'css': $result .= $this->sectionStyles(); break;
        case 'std': $result .= $this->sectionStd(); break;
        case 'themes': default: $result .= $this->sectionTemplates(); break;
      }
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
?>