<?php
/**
* News, Eresus™ 2 plugin. 
*
* © 2005-2007, ProCreat Systems
* http://procreat.ru/
*
* @author  Mikhail Krasilnikov <mk@procreat.ru>
* @version  2.07
* @modified  2007-07-27
*/

class TNews extends TListContentPlugin {
  var $name = 'news';
  var $type = 'client,content';
  var $title = 'Новости';
  var $version = '2.07';
  var $description = 'Публикация новостей';
  var $settings = array(
      'itemsPerPage' => 10,
      'tmplListItem' => '<div><div><b>$(caption)</b> ($(posted))</div><div>$(preview)</div><a href="$(url)">Полный текст...</a></div><br />',
      'tmplItem' => '<h3>$(caption)</h3>$(posted)<br /><br />$(text)<br /><br />',
      'previewMaxSize' => 500,
      'previewSmartSplit' => true,
      'dateFormatPreview' => DATE_SHORT,
      'dateFormatFullText' => DATE_LONG,
      'virt_ext' => 'html',
  );
  var $table = array (
      'name' => 'news',
      'key'=> 'id',
      'sortMode' => 'posted',
      'sortDesc' => true,
      'columns' => array(
        array('name' => 'caption', 'caption' => 'Заголовок'),
        array('name' => 'posted', 'align'=>'center', 'value' => templPosted, 'macros' => true),
        array('name' => 'preview', 'caption' => 'Кратко'),
      ),
      'controls' => array (
        'delete' => '',
        'edit' => '',
        'toggle' => '',
      ),
      'tabs' => array(
        'width'=>'180px',
        'items'=>array(
         array('caption'=>'Добавить новость', 'name'=>'action', 'value'=>'create')
        ),
      ),
      'sql' => "(
        `id` int(10) unsigned NOT NULL auto_increment,
        `section` int(10) unsigned default NULL,
        `posted` datetime default NULL,
        `caption` varchar(100) NOT NULL default '',
        `active` tinyint(1) unsigned NOT NULL default '1',
        `preview` text NOT NULL,
        `text` longtext NOT NULL,
        PRIMARY KEY  (`id`),
        KEY `section` (`section`),
        KEY `posted` (`posted`)
      ) TYPE=MyISAM COMMENT='News';",
    );
  /**
  * Создаеёт аннотацию к новости
  *
  * @access  private
  *
  * @param  string  $text  Исходный текст новости
  *
  * @return  string  Аннотация
  */
  function createPreview($text)
  {
    $text = trim(preg_replace('/<[^>]+?>/Us',' ',$text));
    if ($this->settings['previewSmartSplit']) {
      if (preg_match("/\A.{1,".$this->settings['previewMaxSize']."}([\.;]|$)/s", $text, $result)) $result = str_replace(array("\n","\r"),' ',$result[0]);
      else {
        $this->settings['previewSmartSplit'] = false;
        $result = $this->createPreview($text);
      }
    } else {
      $result = substr($text, 0, $this->settings['previewMaxSize']);
      if (strlen($text)>$this->settings['previewMaxSize']) $result .= '...';
    }
    return $result;
  }
  //------------------------------------------------------------------------------
  function insert()
  {
    global $db, $request, $page;

    $item = getArgs($db->fields($this->table['name']));
    $item['active'] = true;
    if (empty($item['preview'])) $item['preview'] = $this->createPreview($item['text']);
    if (empty($item['posted'])) $item['posted'] = gettime();
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
  /**
  * Замена макросов
  *
  * @access  private
  *
  * @param  string  $template    Шаблон
  * @param  array   $item        Обрабатываемый элемент
  * @param  string  $dateFormat  Шаблон даты
  *
  * @return  string  Результат
  */
  function replaceMacros($template, $item, $dateFormat)
  {
    global $page;

    $item['preview'] = '<p>'.str_replace("\n", "</p>\n<p>", $item['preview']).'</p>';
    $item['posted'] = FormatDate($item['posted'], $dateFormat);
    # @todo  Remove 'link'
    $item['link'] = $item['url'] = $page->clientURL($item['section']).$item['id'].'.'.$this->settings['virt_ext'];
    $result = parent::replaceMacros($template, $item);
    return $result;
  }
  //------------------------------------------------------------------------------
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminAddItem()
  {
    global $page, $request;

    $form = array(
      'name' => 'newNews',
      'caption' => 'Добавить новость',
      'width' => '95%',
      'fields' => array (
        array ('type'=>'hidden','name'=>'action', 'value'=>'insert'),
        array ('type' => 'hidden', 'name' => 'section', 'value' => $request['arg']['section']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Заголовок', 'width' => '100%', 'maxlength' => '100'),
        array ('type' => 'html', 'name' => 'text', 'label' => 'Полный текст', 'height' => '200px'),
        array ('type' => 'memo', 'name' => 'preview', 'label' => 'Краткое описание', 'height' => '10'),
        array ('type' => 'edit', 'name'=>'posted', 'label'=>'Написано'),
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
      'name' => 'editNews',
      'caption' => 'Изменить новость',
      'width' => '95%',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Заголовок', 'width' => '100%', 'maxlength' => '100'),
        array ('type' => 'html', 'name' => 'text', 'label' => 'Полный текст', 'height' => '200px'),
        array ('type' => 'memo', 'name' => 'preview', 'label' => 'Краткое описание', 'height' => '5'),
        array ('type' => 'checkbox', 'name'=>'updatePreview', 'label'=>'Обновить краткое описание автоматически'),
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
        array('type'=>'edit','name'=>'previewMaxSize','label'=>'Макс. размер описания','width'=>'50px', 'maxlength'=>'4', 'comment'=>'символов'),
        array('type'=>'checkbox','name'=>'previewSmartSplit','label'=>'"Умное" создание описания'),
        array('type'=>'divider'),
        array('type'=>'memo','name'=>'tmplItem','label'=>'Шаблон полнотекстового просмотра','height'=>'5'),
        array('type'=>'edit','name'=>'dateFormatFullText','label'=>'Формат даты', 'width'=>'200px'),
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
    $result = $this->replaceMacros($this->settings['tmplListItem'], $item, $this->settings['dateFormatPreview']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientGetItems()
  {
    global $Eresus, $page;
    
    $year = next($Eresus->request['params']);
    $month = next($Eresus->request['params']);
    $day = next($Eresus->request['params']);
    if ($year) {
      $sql = "(`section`='".$page->id."')".(strpos($this->table['sql'], '`active`')!==false?"AND(`active`='1')":'');
      $sql .= " AND (`posted` BETWEEN '$year-".($month?$month:'01')."-".($day?$day:'01')."' AND '$year-".($month?$month:'12')."-".($day?$day:'31')."')";
      $result = $Eresus->db->select(
        $this->table['name'],
        $sql,
        $this->table['sortMode'],
        $this->table['sortDesc']
      );
    } else $result = parent::clientGetItems();
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderItem()
  {
    global $db, $page, $Eresus;

    if (strpos($page->topic, $this->settings['virt_ext']) !== false) {
      $page->topic = substr($page->topic, 0, -strlen($this->settings['virt_ext']));
      $item = $db->selectItem($this->table['name'], "(`id`='".$page->topic."')AND(`active`='1')");
      if (is_null($item)) $page->httpError('404');
      $result = $this->replaceMacros($this->settings['tmplItem'], $item, $this->settings['dateFormatFullText']).$page->buttonBack();
      $page->section[] = $item['caption'];
    } else {
      prev($Eresus->request['params']);
      $result = $this->clientRenderList();
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>

