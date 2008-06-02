<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus™
# © 2005, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TCatalog extends TListContentPlugin {
  var
    $name = 'catalog',
    $type = 'client,content,ondemand',
    $title = 'Каталог продукции',
    $version = '1.01',
    $description = 'Каталог товаров',
    $settings = array(
      # Просмотр описания
      'tmplItem' => '',
      # Список
      'tmplList' => '',
      'tmplListItem' => '',
      'itemsPerPage' => 10,
      'counter' => 0,
      # Картинки
      'imageCount' => 1,
      'previewWidth' => 120,
      'previewHeight' => 90,
      'previewBG' => '',
      'imageWidth' => 800,
      'imageHeight' => 600,
      'imageBG' => '',
      'logo' => false,
    ),
    $table = array (
      'name' => 'catalog',
      'key'=> 'id',
      'sortMode' => 'position',
      'sortDesc' => true,
      'columns' => array(
        array('name' => 'caption', 'caption' => 'Заголовок', 'wrap' => false),
        array('name' => 'cost', 'caption' => 'Цена', 'align'=>'right'),
        array('name' => 'block', 'caption' => 'Блок', 'align'=>'center'),
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
         array('caption'=>'Добавить позицию', 'name'=>'action', 'value'=>'create'),
         array('caption'=>'Список позиций'),
         array('caption'=>'Текст на странице', 'name'=>'action', 'value'=>'text'),
        ),
      ),
      'sql' => "(
        `id` int(10) unsigned NOT NULL auto_increment,
        `active` tinyint(1) unsigned default '1',
        `section` int(10) unsigned default NULL,
        `position` int(10) unsigned default NULL,
        `caption` varchar(127) default NULL,
        `cost` varchar(31) default NULL,
        `block` tinyint(1) unsigned default '0',
        `description` text,
        PRIMARY KEY  (`id`),
        KEY `section` (`section`),
        KEY `active` (`active`),
        KEY `position` (`position`)
      ) TYPE=MyISAM;",
    );
  var $counter = 1;
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Стандартные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function install()
  {
    parent::install();
    umask(0000);
    if (!file_exists(dataFiles.$this->name)) mkdir(dataFiles.$this->name, 0777);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Внутренние функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function replaceMacros($template, $item)
  {
    $result = parent::replaceMacros($template, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function resizeImage($filename, $newname, $width, $height, $bg='') 
  {
    $src = imageCreateFromJPEG($filename);
    $sW = imageSX($src);
    $sH = imageSY($src);
    $resizer = (($sW / $width) >= ($sH / $height)) ? $sW / $width : $sH / $height;
    $dW = floor($sW / $resizer);
    $dH = floor($sH / $resizer);
    if (empty($bg)) {
      $dst = imageCreateTrueColor($dW, $dH);
      imageCopyResampled($dst, $src, 0, 0, 0, 0, $dW, $dH, $sW, $sH);
    } else {
      $R = hexdec(substr($bg, 0, 2));
      $G = hexdec(substr($bg, 2, 2));
      $B = hexdec(substr($bg, 4, 2));
      $dst = imageCreateTrueColor($width, $height);
      imageFill($dst, 0, 0, imageColorAllocate($dst, $R, $G, $B));
      imageCopyResampled($dst, $src, round(($width-$dW)/2), round(($height-$dH)/2), 0, 0, $dW, $dH, $sW, $sH);
    }
    if (file_exists($newname)) unlink($newname);
    ImageJPEG($dst, $newname);
    ImageDestroy($src);
    ImageDestroy($dst);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function setLogo($filename, $logofile) 
  {
    $src = imageCreateFromJPEG($filename);
    $sh = imageSY($src);
    $logo = imageCreateFromGIF(filesRoot.$logofile);
    $lw = imageSX($logo);
    $lh = imageSY($logo);
    imageCopyMerge($src, $logo, 10, $sh-$lh-5, 0, 0, $lw, $lh, 70);
    if (file_exists($filename)) unlink($filename);
    ImageJPEG($src, $filename);
    imageDestroy($src);
    imageDestroy($logo);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function insert()
  {
  global $db, $request, $page, $session;

    $item = getArgs($db->fields($this->table['name']));
    $item['active'] = true;
    if ($this->table['sortDesc']) $item['position'] = $db->count($this->table['name'], "`section`='".$item['section']."'"); else {
      $item['position'] = 0;
      dbShiftItems($this->table['name'], "`section`='".$item['section']."'", +1);
    }
    $db->insert($this->table['name'], $item);
    $path = filesRoot.'data/'.$this->name.'/';
    $item['id'] = $db->getInsertedID();
    if (count($_FILES)) for($i=1; $i<=$this->settings['imageCount']; $i++) {
      $filename = $_FILES['photo'.$i]['tmp_name'];
      if (is_uploaded_file($filename)) {
        $image = getimagesize($filename);
        switch ($image[2]) {
          case IMG_GIF: $src = imageCreateFromGIF($filename); break;
          case IMG_JPG: 
          case IMG_JPEG: $src = imageCreateFromJPEG($filename); break;
          case IMG_PNG: $src = imageCreateFromPNG($filename); break;
        }
        if ($src) {
          $dst = imageCreateTrueColor($image[0], $image[1]);
          imagecopy($dst, $src, 0, 0, 0, 0, $image[0], $image[1]);
          $filename = $item['id'].'-'.$i.'.jpg';
          ImageJPEG($dst, $path.$filename);
          imageDestroy($src);
          imageDestroy($dst);
          $this->resizeImage($path.$filename, $path.$filename, $this->settings['imageWidth'], $this->settings['imageHeight'], $this->settings['imageBG']);
          #if ($this->settings['logo']) $this->setLogo($path.$filename);
          $this->resizeImage($path.$filename, $path.substr($filename, 0, strrpos($filename, '.')).'-thmb.jpg', $this->settings['previewWidth'], $this->settings['previewHeight'], $this->settings['previewBG']);
        }
      }
    }
    sendNotify(admAdded.': <a href="'.httpRoot.'admin.php?mod=content&section='.$item['section'].'&id='.$item['id'].'">'.$item['caption'].'</a><br>'.$item['description'], array('editors'=>defined('CLIENTUI')));
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['update']."'");
    $item = setArgs($item);
    if (!isset($request['arg']['active'])) $item['active'] = false;
    $db->updateItem($this->table['name'], $item, "`id`='".$request['arg']['update']."'");
    $path = dataFiles.$this->name.'/';
    if (count($_FILES)) for($i=1; $i<=$this->settings['imageCount']; $i++) {
      $filename = $_FILES['photo'.$i]['tmp_name'];
      if (is_uploaded_file($filename)) {
        $image = getimagesize($filename);
        switch ($image[2]) {
          case 1: $src = imageCreateFromGIF($filename); break;
          case 2: $src = imageCreateFromJPEG($filename); break;
          case 3: $src = imageCreateFromPNG($filename); break;
        }
        if ($src) {
          $dst = imageCreateTrueColor($image[0], $image[1]);
          imagecopy($dst, $src, 0, 0, 0, 0, $image[0], $image[1]);
          $filename = $item['id'].'-'.$i.'.jpg';
          ImageJPEG($dst, $path.$filename);
          imageDestroy($src);
          imageDestroy($dst);
          $this->resizeImage($path.$filename, $path.$filename, $this->settings['imageWidth'], $this->settings['imageHeight'], $this->settings['imageBG']);
          #if ($this->settings['logo']) $this->setLogo($path.$filename);
          $this->resizeImage($path.$filename, $path.substr($filename, 0, strrpos($filename, '.')).'-thmb.jpg', $this->settings['previewWidth'], $this->settings['previewHeight'], $this->settings['previewBG']);
        }
      }
    }
    sendNotify(admUpdated.': <a href="'.$page->url().'">'.$item['caption'].'</a><br>'.$item['description']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function delete($id)
  {
    $files = glob(dataFiles.$this->name.'/'.$id.'-*.jpg');
    if (count($files)) foreach ($files as $file) if (file_exists($file)) unlink($file);
    return parent::delete($id);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminAddItem()
  {
  global $page, $request, $db;

    $form = array(
      'name' => 'newCatalog',
      'caption' => 'Добавить позицию',
      'width' => '100%',
      'fields' => array (
        array ('type' => 'hidden', 'name' => 'action', 'value' => 'insert'),
        array ('type' => 'hidden', 'name' => 'section', 'value' => $request['arg']['section']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Название', 'width' => '100%', 'maxlength' => '127', 'pattern' => '/.+/', 'errormsg' => 'Не указано название!'),
        array ('type' => 'edit', 'name' => 'cost', 'label' => 'Цена', 'width' => '50px', 'maxlength' => '31'),
        array ('type' => 'select', 'name' => 'block', 'label' => 'На главной', 'items' => array('не показывать', 'показывать в 1-м блоке', 'показывать в 2-м блоке', 'показывать в 3-м блоке'), 'values'=>array('',1,2,3)),
        array ('type' => 'html', 'name' => 'description', 'label' => 'Описание', 'height' => '200px'),
      ),
      'buttons' => array('ok', 'cancel'),
    );
    for ($i=1; $i<=$this->settings['imageCount']; $i++) $form['fields'][] = 
      array ('type' => 'file', 'name' => 'photo'.$i, 'label' => 'Фото'.($i>1?" $i":''), 'width' => '50');
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminEditItem()
  {
    global $db, $page, $request;
   
    $result = ''; 
    $path = dataFiles.$this->name.'/';
    if (isset($request['arg']['delphoto'])) {
      $filename = $path.$request['arg']['delphoto'].'.jpg';
      if (file_exists($filename)) unlink($filename);
      $filename = $path.$request['arg']['delphoto'].'-thmb.jpg';
      if (file_exists($filename)) unlink($filename);
      goto($page->url());
    }
    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['id']."'");
    $form = array(
      'name' => 'editCatalog',
      'caption' => 'Изменить позицию',
      'width' => '100%',
      'fields' => array (
        array ('type' => 'hidden', 'name' => 'update', 'value' => $item['id']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Название', 'width' => '100%', 'maxlength' => '127', 'pattern' => '/.+/', 'errormsg' => 'Не указано название!'),
        array ('type' => 'edit', 'name' => 'cost', 'label' => 'Цена', 'width' => '50px', 'maxlength' => '31'),
        array ('type' => 'select', 'name' => 'block', 'label' => 'На главной', 'items' => array('не показывать', 'показывать в 1-м блоке', 'показывать в 2-м блоке', 'показывать в 3-м блоке'), 'values'=>array('',1,2,3)),
        array ('type' => 'html', 'name' => 'description', 'label' => 'Описание', 'height' => '200px'),
        array ('type' => 'checkbox', 'name'=>'active', 'label'=>'Активно'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    for ($i=1; $i<=$this->settings['imageCount']; $i++) $form['fields'][] = 
      array ('type' => 'file', 'name' => 'photo'.$i, 'label' => 'Фото'.($i>1?" $i":''), 'width' => '50', 'comment'=>(file_exists($path.$item['id'].'-'.$i.'.jpg')?'<a href="'.$page->url(array('delphoto'=>$item['id'].'-'.$i)).'">Удалить</a>':''));
    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function settings()
  {
  global $page;

    $form = array(
      'name' => 'Settings',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'header', 'value'=>'Просмотр описания'),
        array('type'=>'memo','name'=>'tmplItem','label'=>'Просмотр описания','height'=>'6'),
        array('type'=>'header', 'value'=>'Список позиций'),
        array('type'=>'memo','name'=>'tmplList','label'=>'Список позиций','height'=>'3'),
        array('type'=>'memo','name'=>'tmplListItem','label'=>'Элемент списка позиций','height'=>'6'),
        array('type'=>'edit','name'=>'itemsPerPage','label'=>'Элементов на страницу','width'=>'50px', 'maxlength'=>'3'),
        array('type'=>'edit','name'=>'counter','label'=>'Обнулять счетчик при','width'=>'50px', 'maxlength'=>'4'),
        array('type'=>'header', 'value'=>'Картинки'),
        array('type'=>'edit','name'=>'imageCount','label'=>'Картинок на позицию','width'=>'50px', 'maxlength'=>'3'),
        array('type'=>'text', 'value'=>'<center><b>Маленькая картинка (preview)</b></center>'),
        array('type'=>'edit','name'=>'previewWidth','label'=>'Ширина','width'=>'50px', 'maxlength'=>'3', 'comment' => 'пикс.'),
        array('type'=>'edit','name'=>'previewHeight','label'=>'Высота','width'=>'50px', 'maxlength'=>'3', 'comment' => 'пикс.'),
        array('type'=>'edit','name'=>'previewBG','label'=>'Цвет фона','width'=>'50px', 'maxlength'=>'6', 'comment' => 'RRGGBB (hex)'),
        array('type'=>'text', 'value'=>'<center><b>Большая картинка (image)</b></center>'),
        array('type'=>'edit','name'=>'imageWidth','label'=>'Ширина','width'=>'50px', 'maxlength'=>'4', 'comment' => 'пикс.'),
        array('type'=>'edit','name'=>'imageHeight','label'=>'Высота','width'=>'50px', 'maxlength'=>'4', 'comment' => 'пикс.'),
        array('type'=>'edit','name'=>'imageBG','label'=>'Цвет фона','width'=>'50px', 'maxlength'=>'6', 'comment' => 'RRGGBB (hex)'),
        #array('type'=>'edit','name'=>'logo','label'=>'Логотип','width'=>'50px', 'maxlength'=>'3'),
    ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = 
      '<table><tr><td style="vertical-align: top;">'.
      $page->renderForm($form, $this->settings).
      '</td><td style="vertical-align: top;">'.
      $page->window(array('caption' => 'Макросы', 'body' =>
        '<ul>'.
        '<li><b>$(caption)</b> - Название</li>'.
        '<li><b>$(cost)</b> - Цена</li>'.
        '<li><b>$(size)</b> - Размеры</li>'.
        '<li><b>$(preview)</b> - Малая картинка</li>'.
        '<li><b>$(image)</b> - Картинка</li>'.
        '<li><b>$(description)</b> - Описание</li>'.
        '<li><b>$(link)</b> - Ссылка на полное описание (только для элементов списка)</li>'.
        '<li><b>$(items)</b> - Список позиций (только в шаблоне списка)</li>'.
        '<li><b>$(counter)</b> - Счетчик элементов списка (только для элементов списка)</li>'.
        '<li><b>{%counter=N?стройка1:строка2}</b> - Если счетчик элементов равен N вывести "строка1" иначе "строка2" (только для элементов списка)</li></ul>'
      )).
      '</td></tr></table>';
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminEditText()
  {
    global $db, $page, $request;

    if (isset($request['arg']['update'])) {
      $item = $db->selectItem('pages', "`id`='".$request['arg']['update']."'");
      $item['content'] = $request['arg']['content'];
      $db->updateItem('pages', $item, "`id`='".$request['arg']['update']."'");
      goto($page->url(array('action' => 'text')));
    } else {
      $item = $db->selectItem('pages', "`id`='".$request['arg']['section']."'");
      $form = array(
        'name' => 'contentEditor',
        'caption' => 'Текст страницы',
        'width' => '100%',
        'fields' => array (
          array ('type' => 'hidden','name' => 'action', 'value'=>'text'),
          array ('type' => 'hidden','name' => 'update', 'value'=>$item['id']),
          array ('type' => 'html','name' => 'content','height' => '400px', 'value'=>$item['content']),
        ),
        'buttons'=> array('ok', 'reset'),
      );
      $result = $page->renderTabs($this->table['tabs']).$page->renderForm($form);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRenderContent()
  {
    global $request;
    
    if (isset($request['arg']['action']) && ($request['arg']['action'] == 'text')) $result = $this->adminEditText();
    else $result = parent::adminRenderContent();
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Пользовательские функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientRenderList()
  {
    $result['items'] = parent::clientRenderList();
    $this->counter = 1;
    $result = $this->replaceMacros($this->settings['tmplList'], $result);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientRenderListItem($item)
  {
    global $request;
    $item['preview'] = img('data/catalog/'.$item['id'].'-1-thmb.jpg');
    $item['url'] = $request['link'].$item['id'].'/';
    $result = $this->replaceMacros($this->settings['tmplListItem'], $item);
    $result = preg_replace('!{%counter=(\d+)\?(.*):(.*)}!Usie', '($1 == '.$this->counter.')?"$2":"$3"', $result);
    if ($this->counter >= $this->settings['counter']) $this->counter = 0;
    $this->counter++;
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>