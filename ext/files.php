<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus™
# © 2005-2007, ProCreat Systems
# Web: http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TFiles extends TListContentPlugin {
  var
    $name = 'files',
    $type = 'client,content,ondemand',
    $title = 'Файлы',
    $version = '0.02',
    $description = 'Публикация файлов',
    $settings = array(
      'itemsPerPage' => 30,
      'tmplListItem' => '',
      'sortMode' => 'position',
      'sortDesc' => false,
    ),
    $table = array (
      'name' => 'files',
      'key'=> 'id',
      'sortMode' => 'position',
      'sortDesc' => false,
      'columns' => array(
        array('name' => 'caption', 'caption' => 'Название'),
        array('name' => 'posted', 'align'=>'center', 'value' => templPosted, 'macros' => true),
        array('name' => 'file', 'caption' => 'Имя файла'),
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
          array('caption'=>'Загрузить файл', 'name'=>'action', 'value'=>'create')
        ),
      ),
      'sql' => "(
        `id` int(10) unsigned NOT NULL auto_increment,
        `active` tinyint(1) unsigned NOT NULL default '1',
        `section` int(10) unsigned default NULL,
        `posted` datetime default NULL,
        `position` int(10) unsigned default NULL,
        `caption` varchar(255) NOT NULL default '',
        `filename` varchar(255) NOT NULL default '',
        `description` text NOT NULL default '',
        PRIMARY KEY  (`id`),
        KEY `active` (`active`),
        KEY `section` (`section`),
        KEY `position` (`position`),
        KEY `caption` (`caption`)
      ) TYPE=MyISAM COMMENT='Files';",
    );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Стандартные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function install()
  {
    parent::install();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Внутренние функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function createPreview($text)
  {
    $text = trim(preg_replace('/<.+>/Us',' ',$text));
    if ($this->settings['previewSmartSplit']) {
      preg_match("/\A.{1,".$this->settings['previewMaxSize']."}(\.\s|\.|\Z)/", $text, $result);
      $result = $result[0];
    } else {
      $result = substr($text, 1, $this->settings['previewMaxSize']);
      if (strlen($text)>$this->settings['previewMaxSize']) $result .= '...';
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function insert()
  {
  global $db, $request, $page;

    $item = getArgs($db->fields($this->table['name']));
    $item['active'] = true;
    if (empty($item['preview'])) $item['preview'] = $this->createPreview($item['text']);
    $item['posted'] = gettime();
    $db->insert($this->table['name'], $item);
    $item['id'] = $db->getInsertedID();
    sendNotify(admAdded.': <a href="'.httpRoot.'admin.php?mod=content&section='.$item['section'].'&id='.$item['id'].'">'.$item['caption'].'</a><br />'.$item['text'], array('editors'=>defined('CLIENTUI_VERSION')));
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['update']."'");
    $item = setArgs($item);
    if (!isset($request['arg']['active'])) $item['active'] = false;
    if (empty($item['preview']) || $request['arg']['updatePreview']) $item['preview'] = $this->createPreview($item['text']);
    $db->updateItem($this->table['name'], $item, "`id`='".$request['arg']['update']."'");
    sendNotify(admUpdated.': <a href="'.$page->url().'">'.$item['caption'].'</a><br />'.$item['text']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function replaceMacros($template, $item, $dateFormat)
  {
  global $page;

    $item['preview'] = '<p>'.str_replace("\n", "</p>\n<p>", $item['preview']).'</p>';
    $item['posted'] = FormatDate($item['posted'], $dateFormat);
    $item['link'] = $page->clientURL($item['section']).$item['id'];
    $result = parent::replaceMacros($template, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminAddItem()
  {
  global $page, $request;

    $form = array(
      'name' => 'newFiles',
      'caption' => 'Загрузить файл',
      'width' => '95%',
      'fields' => array (
        array ('type'=>'hidden','name'=>'action', 'value'=>'insert'),
        array ('type' => 'hidden', 'name' => 'section', 'value' => $request['arg']['section']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Заголовок', 'width' => '100%', 'maxlength' => '100'),
        array ('type' => 'html', 'name' => 'text', 'label' => 'Полный текст', 'height' => '200px'),
        array ('type' => 'memo', 'name' => 'preview', 'label' => 'Краткое описание', 'height' => '10'),
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
      'name' => 'editFiles',
      'caption' => 'Изменить новость',
      'width' => '95%',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Заголовок', 'width' => '100%', 'maxlength' => '100'),
        array ('type' => 'html', 'name' => 'text', 'label' => 'Полный текст', 'height' => '200px'),
        array ('type' => 'memo', 'name' => 'preview', 'label' => 'Краткое описание', 'height' => '5'),
        array ('type' => 'divider'),
        array ('type' => 'edit', 'name' => 'section', 'label' => 'Раздел', 'access'=>ADMIN),
        array ('type' => 'edit', 'name'=>'posted', 'label'=>'Написано'),
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
        array('type'=>'edit','name'=>'itemsPerPage','label'=>'Новостей на страницу','width'=>'50px', 'maxlength'=>'2'),
        array('type'=>'memo','name'=>'tmplListItem','label'=>'Шаблон краткого текста','height'=>'5'),
        array('type'=>'edit','name'=>'dateFormatPreview','label'=>'Формат даты', 'width'=>'200px'),
        array('type'=>'edit','name'=>'previewMaxSize','label'=>'Макс. размер описания','width'=>'50px', 'maxlength'=>'4', 'comment'=>'симовлов'),
        array('type'=>'checkbox','name'=>'previewSmartSplit','label'=>'"Умное" создание описания'),
        array('type'=>'divider'),
        array('type'=>'memo','name'=>'tmplItem','label'=>'Шаблон полнотекстового просмотра','height'=>'5'),
        array('type'=>'edit','name'=>'dateFormatFullText','label'=>'Формат даты', 'width'=>'200px'),
        array('type'=>'header', 'value' => 'Последние новости'),
        array('type'=>'memo','name'=>'tmplLastFiles','label'=>'Шаблон последних новостей','height'=>'3'),
        array('type'=>'select','name'=>'lastFilesMode','label'=>'Режим', 'items'=>array('отключить', 'Заменять макрос $(plgFilesLast)')),
        array('type'=>'edit','name'=>'lastFilesCount','label'=>'Показывать новостей', 'width'=>'100px'),
    ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function renderLastFiles()
  {
    global $db;
    
    $result = '';
    $items = $db->select($this->table['name'], "`active`='1'", 'posted', true, '', $this->settings['lastFilesCount']);
    if (count($items)) foreach($items as $item) $result .= $this->replaceMacros($this->settings['tmplLastFiles'], $item, $this->settings['dateFormatPreview']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Пользовательские функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderListItem($item)
  {
    $result = $this->replaceMacros($this->settings['tmplListItem'], $item, $this->settings['dateFormatPreview']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderList()
  {
    global $request;
    
    $result = '<table class="plgFiles"><tr><td id="plgFilesLast">'.$this->renderLastFiles().'</td><td>'.parent::clientRenderList().'</td></tr></table>';
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderItem()
  {
    global $db, $page;

    $item = $db->selectItem($this->table['name'], "(`id`='".$page->topic."')AND(`active`='1')");
    if (is_null($item)) $page->httpError('404');
    $result = $this->replaceMacros($this->settings['tmplItem'], $item, $this->settings['dateFormatFullText']).$page->buttonBack();
    $page->section[] = $item['caption'];
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Обработчики событий
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientOnPageRender($text)
  {
  global $page;
  
    $text = str_replace('$(plgFilesLast)', $this->renderLastFiles(), $text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>