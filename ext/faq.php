<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus™
# © 2006, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TFAQ extends TListContentPlugin {
  var
    $name = 'faq',
    $type = 'client,content',
    $title = 'FAQ',
    $version = '2.00a',
    $description = 'Частозадаваемые вопросы (FAQ)',
    $settings = array(
      'tmplPage' => '<ol>$(contents)</ol><hr />$(items)',
      'tmplContentsItem' => '<li><a href="#q$(id)">$(caption)</a></li>',
      'tmplBlockItem' => '<div><a id="q$(id)"></a><b>$(caption)</b><br />$(question)<br />$(answer)</div>',
    ),
    $table = array (
      'name' => 'faq',
      'key'=> 'id',
      'sortMode' => 'position',
      'sortDesc' => true,
      'columns' => array(
        array('name' => 'caption', 'caption' => 'Вопрос'),
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
         array('caption'=>'Добавить вопрос', 'name'=>'action', 'value'=>'create')
        ),
      ),
      'sql' => "(
        `id` int(10) unsigned NOT NULL auto_increment,
        `section` int(10) unsigned default NULL,
        `active` tinyint(1) unsigned NOT NULL default '1',
        `name` varchar(31) default '',
        `position` int(10) unsigned default NULL,
        `caption` varchar(100) NOT NULL default '',
        `question` text default NULL,
        `answer` text default NULL,
        PRIMARY KEY  (`id`),
        KEY `active` (`active`),
        KEY `section` (`section`),
        KEY `position` (`position`)
      ) TYPE=MyISAM COMMENT='FAQ';",
    );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Внутренние функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function insert()
  {
  global $db, $request, $page;

    $item = getArgs($db->fields($this->table['name']));
    $item['active'] = true;
    $item['position'] = $db->count($this->table['name'], "`section` = '".$item['section']."'");
    $db->insert($this->table['name'], $item);
    $item['id'] = $db->getInsertedID();
    sendNotify(admAdded.': <a href="'.httpRoot.'admin.php?mod=content&section='.$item['section'].'&id='.$item['id'].'">'.$item['caption'].'</a><br />'.$item['question'].'<hr>'.$item['answer']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['update']."'");
    $item = GetArgs($item, array('active', 'block'));
    $db->updateItem($this->table['name'], $item, "`id`='".$request['arg']['update']."'");
    sendNotify(admUpdated.': <a href="'.$page->url().'">'.$item['caption'].'</a><br />'.$item['question'].'<hr>'.$item['answer']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function replaceMacros($template, $item)
  {
  global $page;

    $result = str_replace(
      array(
        '$(question)',
        '$(link)',
      ),
      array(
        empty($item['question'])?'':'<p>'.str_replace("\n", '</p><p>', StripSlashes($item['question'])).'</p>',
        $page->clientURL($item['section']).$item['id'],
      ),
      $template
    );
    $result = parent::replaceMacros($result, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminAddItem()
  {
  global $page, $request;

    $form = array(
      'name' => 'newFAQ',
      'caption' => 'Добавить вопрос',
      'width' => '95%',
      'fields' => array (
        array ('type'=>'hidden','name'=>'action', 'value'=>'insert'),
        array ('type' => 'hidden', 'name' => 'section', 'value' => $request['arg']['section']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Заголовок', 'width' => '100%', 'maxlength' => '100', 'pattern' => '/.+/', 'errormsg' => 'Не указан заголовок'),
        array ('type' => 'html', 'name' => 'question', 'label' => 'Текст вопроса', 'height' => '200px'),
        array ('type' => 'html', 'name' => 'answer', 'label' => 'Текст ответа', 'height' => '200px'),
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
      'name' => 'editFAQ',
      'caption' => 'Изменить вопрос',
      'width' => '95%',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Заголовок', 'width' => '100%', 'maxlength' => '100', 'pattern' => '/.+/', 'errormsg' => 'Не указан заголовок'),
        array ('type' => 'memo', 'name' => 'question', 'label' => 'Текст вопроса', 'height' => '5'),
        array ('type' => 'html', 'name' => 'answer', 'label' => 'Текст ответа', 'height' => '300px'),
        array ('type' => ($this->settings['blockMode'] == _FAQ_BLOCK_MANUAL)?'checkbox':'hidden', 'name' => 'block', 'label' => 'Показывать в блоке'),
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
        array('type'=>'memo','name'=>'tmplPage','label'=>'Шаблон страницы', 'height'=>5),
        array('type'=>'text', 'value'=>
          "Макросы:<br />\n".
          "<b>$(contents)</b> - оглавление<br />\n".
          "<b>$(items)</b> - вопросы<br />\n"
       ),
        array('type'=>'memo','name'=>'tmplContentsItem','label'=>'Шаблон элемента оглавления','height'=>3),
        array('type'=>'memo','name'=>'tmplBlockItem','label'=>'Шаблон пары "Вопрос-ответ"','height'=>5),
        array('type'=>'text', 'value'=>
          "Для создания шаблонов можно использовать макросы:<br />\n".
          "<b>$(id)</b> - идентификатор вопроса<br />\n".
          "<b>$(caption)</b> - заголовок<br />\n".
          "<b>$(question)</b> - текст вопроса<br />\n".
          "<b>$(answer)</b> - текст ответа<br />\n"
       ),
    ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function renderFAQBlock()
  {
    global $db;
    
    $result = '';
    $items = $db->select($this->table['name'], "`active`='1'".($this->settings['blockMode']==_FAQ_BLOCK_MANUAL?" AND `block`='1'":''), $this->table['sortMode'], $this->table['sortDesc'], '', $this->settings['blockCount']);
    if (count($items)) foreach($items as $item) {
      $path = '/';
      $pg['owner'] = $item['section'];
      do {
        $pg = $db->selectItem('pages', "`id`='".$pg['owner']."'");
        $path = $pg['name'].$path;
      } while (isset($pg['section']) && $pg['section']);
      $path .= $item['id'];
      $result .= $this->replaceMacros($this->settings['tmplBlockItem'], $item, $this->settings['dateFormatPreview'], httpRoot.$path);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Пользовательские функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderListItem($item)
  {
    $this->contents .= $this->replaceMacros($this->settings['tmplContentsItem'], $item);
    $result = $this->replaceMacros($this->settings['tmplBlockItem'], $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderList()
  {
    $this->contents = '';
    $items = parent::clientRenderList();
    $result = str_replace(
      array(
        '$(contents)',
        '$(items)', 
      ), array(
        $this->contents,
        $items,
      ), $this->settings['tmplPage']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>