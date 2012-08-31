<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus�
# � 2005, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TNews extends TListContentPlugin {
  var
    $name = 'news',
    $type = 'client,content',
    $title = '�������',
    $version = '2.00a1',
    $description = '���������� ��������',
    $settings = array(
      'itemsPerPage' => 10,
      'tmplListItem' => '<div class="plgNewsListItem"><div class="cation">$(caption) ($(posted))</div>$(preview)<br><a href="$(link)">������ �����...</a>',
      'tmplItem' => '<h3>$(caption)</h3>$(posted)<br><br>$(text)',
      'tmplLastNews' => '<b>$(posted)</b><br><a href="$(link)">$(caption)</a><br>',
      'previewMaxSize' => 500,
      'previewSmartSplit' => true,
      'dateFormatPreview' => DATE_SHORT,
      'dateFormatFullText' => DATE_LONG,
      'lastNewsMode' => 0,
      'lastNewsCount' => 5,
    ),
    $table = array (
      'name' => 'news',
      'key'=> 'id',
      'sortMode' => 'posted',
      'sortDesc' => true,
      'columns' => array(
        array('name' => 'caption', 'caption' => '���������', 'wrap' => false),
        array('name' => 'posted', 'align'=>'center', 'value' => templPosted, 'macros' => true),
        array('name' => 'preview', 'caption' => '������'),
      ),
      'controls' => array (
        'delete' => '',
        'edit' => '',
        'toggle' => '',
      ),
      'tabs' => array(
        'width'=>'180px',
        'items'=>array(
         array('caption'=>'�������� �������', 'name'=>'action', 'value'=>'create')
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
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function TNews()
  # ���������� ����������� ������������ �������
  {
  global $plugins;

    parent::TListContentPlugin();
    switch ($this->settings['lastNewsMode']) {
      case 1: $plugins->events['clientOnPageRender'][] = $this->name; break;
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������� �������
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
  global $db, $request, $page, $session;

    $item = getArgs($db->fields($this->table['name']));
    $item['active'] = true;
    if (empty($item['preview'])) $item['preview'] = $this->createPreview($item['text']);
    $item['posted'] = gettime();
    $db->insert($this->table['name'], $item);
    $item['id'] = $db->getInsertedID();
    sendNotify(admAdded.': <a href="'.httpRoot.'admin.php?mod=content&section='.$item['section'].'&id='.$item['id'].'">'.$item['caption'].'</a><br>'.$item['text'], array('editors'=>defined('CLIENTUI_VERSION')));
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
    sendNotify(admUpdated.': <a href="'.$page->url().'">'.$item['caption'].'</a><br>'.$item['text']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function replaceMacros($template, $item, $dateFormat = null)
  {
  global $plugins, $page, $request;

    $result = str_replace(
      array(
        '$(caption)',
        '$(preview)',
        '$(text)',
        '$(posted)',
        '$(link)',
      ),
      array(
        $item['caption'],
        '<p>'.str_replace("\n", '</p><p>', $item['preview']).'</p>',
        $item['text'],
        FormatDate($item['posted'], $dateFormat),
        $page->clientURL($item['section']).$item['id'],
      ),
      $template
    );
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminAddItem()
  {
  global $page, $request;

    $form = array(
      'name' => 'newNews',
      'caption' => '�������� �������',
      'width' => '95%',
      'fields' => array (
        array ('type'=>'hidden','name'=>'action', 'value'=>'insert'),
        array ('type' => 'hidden', 'name' => 'section', 'value' => $request['arg']['section']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '���������', 'width' => '100%', 'maxlength' => '100'),
        array ('type' => 'html', 'name' => 'text', 'label' => '������ �����', 'height' => '200px'),
        array ('type' => 'memo', 'name' => 'preview', 'label' => '������� ��������', 'height' => '10'),
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
      'caption' => '�������� �������',
      'width' => '95%',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '���������', 'width' => '100%', 'maxlength' => '100'),
        array ('type' => 'html', 'name' => 'text', 'label' => '������ �����', 'height' => '200px'),
        array ('type' => 'memo', 'name' => 'preview', 'label' => '������� ��������', 'height' => '5'),
        array ('type' => 'divider'),
        array ('type' => 'edit', 'name' => 'section', 'label' => '������', 'access'=>ADMIN),
        array ('type' => 'edit', 'name'=>'posted', 'label'=>'��������'),
        array ('type' => 'checkbox', 'name'=>'active', 'label'=>'�������'),
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
        array('type'=>'edit','name'=>'itemsPerPage','label'=>'�������� �� ��������','width'=>'50px', 'maxlength'=>'2'),
        array('type'=>'memo','name'=>'tmplListItem','label'=>'������ �������� ������','height'=>'5'),
        array('type'=>'edit','name'=>'dateFormatPreview','label'=>'������ ����', 'width'=>'200px'),
        array('type'=>'edit','name'=>'previewMaxSize','label'=>'����. ������ ��������','width'=>'50px', 'maxlength'=>'4', 'comment'=>'��������'),
        array('type'=>'checkbox','name'=>'previewSmartSplit','label'=>'"�����" �������� ��������'),
        array('type'=>'divider'),
        array('type'=>'memo','name'=>'tmplItem','label'=>'������ ��������������� ���������','height'=>'5'),
        array('type'=>'edit','name'=>'dateFormatFullText','label'=>'������ ����', 'width'=>'200px'),
        array('type'=>'header', 'value' => '��������� �������'),
        array('type'=>'memo','name'=>'tmplLastNews','label'=>'������ ��������� ��������','height'=>'3'),
        array('type'=>'select','name'=>'lastNewsMode','label'=>'�����', 'items'=>array('���������', '�������� ������ $(plgNewsLast)')),
        array('type'=>'edit','name'=>'lastNewsCount','label'=>'���������� ��������', 'width'=>'100px'),
    ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function renderLastNews()
  {
    global $db;

    $items = $db->select($this->table['name'], "`active`='1'", 'posted', true, '', $this->settings['lastNewsCount']);
    if (count($items)) foreach($items as $item) $result .= $this->replaceMacros($this->settings['tmplLastNews'], $item, $this->settings['dateFormatPreview']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderListItem($item)
  {
    $result = $this->replaceMacros($this->settings['tmplListItem'], $item, $this->settings['dateFormatPreview']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderItem()
  {
    global $db, $page;

    $item = $db->selectItem($this->table['name'], "(`id`='".$page->topic."')AND(`active`='1')");
    if (is_null($item)) {
      $item = $page->Error404();
      $result = $item['content'];
    } else {
      $result = $this->replaceMacros($this->settings['tmplItem'], $item, $this->settings['dateFormatFullText']).$page->buttonBack();
    }
    $page->title[] = $item['caption'];
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
  global $page;

    $text= str_replace('$(plgNewsLast)', $this->renderLastNews(), $text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>