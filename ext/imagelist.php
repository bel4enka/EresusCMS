<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus™
# © 2005, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TImageList extends TListContentPlugin {
  var 
    $name = 'imagelist',
    $type = 'client,content,ondemand',
    $title = 'Картинки',
    $version = '0.01',
    $description = 'Список изображений',
    $settings = array(
      'tmplList' => '',
      'tmplListItem' => '',
      'tmplItem' => '',
      'itemsPerPage' => 20,
      'previewWidth' => 120,
      'previewHeight' => 90,
      'buttonBack' => '[ &laquo; Назад ]',
      'buttonNext' => '[ Вперед &raquo; ]',
      'imageWidth' => 800,
      'imageHeight' => 600,
    ),
    $table = array (
      'name' => 'imagelist',
      'key'=> 'id',
      'sortMode' => 'position',
      'sortDesc' => false,
      'columns' => array(
        array('name' => 'caption', 'caption' => 'Название', 'maxlength'=>100),
        array('name' => 'posted', 'caption' => 'Дата'),
        array('name' => 'image', 'caption' => 'Файл'),
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
         array('caption'=>'Добавить', 'name'=>'action', 'value'=>'create'),
         #array('caption'=>'Загрузка из папки', 'name'=>'action', 'value'=>'load'),
        ),
      ),
      'sql' => "(
        `id` int(10) unsigned NOT NULL auto_increment,
        `section` int(10) unsigned default NULL,
        `position` int(10) unsigned NOT NULL default '0',
        `active` tinyint(1) unsigned default '0',
        `posted` datetime default NULL,
        `caption` varchar(128) default NULL,
        `image` varchar(128) default NULL,
        `preview` varchar(128) default NULL,
        `source` varchar(255) default NULL,
        PRIMARY KEY  (`id`),
        KEY `section` (`section`),
        KEY `position` (`position`),
        KEY `active` (`active`),
        KEY `posted` (`posted`),
        KEY `source` (`source`)
      ) TYPE=MyISAM COMMENT='ImageList';",
    );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function install()
  {
    parent::install();
    umask(0000);
    if (!file_exists(filesRoot.'data/'.$this->name)) mkdir(filesRoot.'data/'.$this->name, 0777);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function uninstall()
  {
    parent::uninstall();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function encodeFilename($s)
  {
    
    $s = strtr(strtolower($s), array(
      ' '=>'_',
      'а'=> 'a', 'б'=> 'b', 'в'=> 'v', 'г'=> 'g', 'д'=> 'd', 'е'=> 'e', 'ё'=> 'yo', 'ж'=> 'zh', 'з'=> 'z', 'и'=> 'i', 'й'=> 'y', 'к'=> 'k', 'л'=> 'l', 'м'=> 'm', 'н'=> 'n', 'о'=> 'o', 'п'=> 'p', 'р'=> 'r', 'с'=> 's', 'т'=> 't', 'у'=> 'u', 'ф'=> 'f', 'х'=> 'h', 'ц'=> 'tc', 'ч'=> 'ch', 'ш'=> 'sh', 'щ'=> 'sch', 'ь'=> '', 'ы'=> 'y', 'ъ'=> '', 'э'=> 'e', 'ю'=> 'yu', 'я'=> 'ya',
      'А'=> 'a', 'Б'=> 'b', 'В'=> 'v', 'Г'=> 'g', 'Д'=> 'd', 'Е'=> 'e', 'Ё'=> 'yo', 'Ж'=> 'zh', 'З'=> 'z', 'И'=> 'i', 'Й'=> 'y', 'К'=> 'k', 'Л'=> 'l', 'М'=> 'm', 'Н'=> 'n', 'О'=> 'o', 'П'=> 'p', 'Р'=> 'r', 'С'=> 's', 'Т'=> 't', 'У'=> 'u', 'Ф'=> 'f', 'Х'=> 'h', 'Ц'=> 'tc', 'Ч'=> 'ch', 'Ш'=> 'sh', 'Щ'=> 'sch', 'Ь'=> '', 'Ы'=> 'y', 'Ъ'=> '', 'Э'=> 'e', 'Ю'=> 'yu', 'Я'=> 'ya'
    ));
    $s = preg_replace('/[^\d\w_\-\.]/', '', $s);
    if (empty($s)) $s = 'image';
    if (file_exists(filesRoot.'data/'.$this->name.'/'.$s)) {
      $n = 1;
      while (file_exists(filesRoot.'data/'.$this->name.'/'.substr($s, 0, strrpos($s, '.')).$n.substr($s, strrpos($s, '.')))) $n++;
      $s = substr($s, 0, strrpos($s, '.')).$n.substr($s, strrpos($s, '.'));
    }
    return $s;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function resizeImage($filename, $mode, $newname='')
  {
    $path = filesRoot.'data/'.$this->name.'/';
    if (empty($newname)) $newname = substr($filename, 0, strrpos($filename, '.')).'-thmb.jpg';
    $type = getimagesize($path.$filename);
    switch ($type[2]) {
      case IMG_GIF: $src = imageCreateFromGIF($path.$filename); break;
      case IMG_JPG: 
      case IMG_JPEG: $src = imageCreateFromJPEG($path.$filename); break;
      case IMG_PNG: $src = imageCreateFromPNG($path.$filename); break;
    }
    $sW = imageSX($src);
    $sH = imageSY($src);
    $width = $this->settings[$mode.'Width'];
    $height = $this->settings[$mode.'Height'];
    if ($mode == 'preview' || $sW > $width || $sH > $height) {
      $resizer = ($sW > $sH)?($sW / $width):($sH / $height);
      $dst = imageCreateTrueColor($width, $height);
      imageFill($dst, 0, 0, 0);
      $dW = floor($sW / $resizer);
      $dH = floor($sH / $resizer);
      imageCopyResampled($dst, $src, round(($width-$dW)/2), round(($height-$dH)/2), 0, 0, $dW, $dH, $sW, $sH);
      ImageJPEG($dst, $path.$newname);
    }
    return $newname;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function insert()
  {
    global $db, $request;

    $item = GetArgs($db->fields($this->table['name']));
    $item['image'] = $this->encodeFilename($_FILES['image']['name']);
    $item['position'] = 0;
    $item['active'] = true;
    $item['posted'] = gettime();
    dbShiftItems($this->table['name'], "`section`='".$item['section']."'", +1);
    move_uploaded_file($_FILES['image']['tmp_name'], filesRoot.'data/'.$this->name.'/'.$item['image']);
    $item['preview'] = $this->resizeImage($item['image'], 'preview');
    $this->resizeImage($item['image'], 'image', $item['image']);
    $db->insert($this->table['name'], $item);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $page, $request;
  
    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['update']."'");
    $image = $item['image'];
    $item = GetArgs($item);
    if (!isset($request['arg']['active'])) $item['active'] = false;
    if (!empty($_FILES['image']['tmp_name'])) {
      $item['image'] = $image;
      if (file_exists(filesRoot.'data/'.$this->name.'/'.$item['image'])) unlink(filesRoot.'data/'.$this->name.'/'.$item['image']);
      if (file_exists(filesRoot.'data/'.$this->name.'/'.$item['preview'])) unlink(filesRoot.'data/'.$this->name.'/'.$item['preview']);
      move_uploaded_file($_FILES['image']['tmp_name'], filesRoot.'data/'.$this->name.'/'.$item['image']);
      $item['preview'] = $this->resizeImage($item['image'], 'preview');
      $this->resizeImage($item['image'], 'image', $item['image']);
    }
    $db->updateItem($this->table['name'], $item, "`id`='".$item['id']."'");
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function updateImage()
  {
  global $db, $request, $page;

    $id = $item['id'];
    $item = setArgs($item);
    $item['id'] = $id;
    if (!empty($_FILES['image']['tmp_name'])) {
      $item['image'] = $image;
      if (file_exists(filesRoot.'data/'.$this->name.'/'.$item['image'])) unlink(filesRoot.'data/'.$this->name.'/'.$item['image']);
      if (file_exists(filesRoot.'data/'.$this->name.'/'.$item['preview'])) unlink(filesRoot.'data/'.$this->name.'/'.$item['preview']);
      move_uploaded_file($_FILES['image']['tmp_name'], filesRoot.'data/'.$this->name.'/'.$item['image']);
      $item['preview'] = $this->createPreview($item['image']);
    }
    $db->updateItem($this->sub_table['name'], $item, "`id`='".$item['id']."'");
    sendNotify(admUpdated.': <a href="'.$page->url(array('sub_update'=>'')).'">'.$album['caption'].'</a><br>'.$item['text'], array('url'=>$page->url(array('sub_id'=>''))));
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function deleteImageEx($id)
  {
    global $db;
    
    $item = $db->selectItem($this->table['name'], "`id`='".$id."'");
    $filename = filesRoot.'data/'.$this->name.'/'.$item['image'];
    if (is_file($filename)) unlink($filename);
    $filename = filesRoot.'data/'.$this->name.'/'.$item['preview'];
    if (is_file($filename)) unlink($filename);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function delete($id)
  {
  global $db, $request;
  
    $this->deleteImageEx($id);
    parent::delete($id);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  /*function loadFolder()
  {
    global $request, $db, $session, $user;

    $owner = $db->selectItem($this->table['name'], "`id`='".$request['arg']['owner']."'");
    $folder = $request['arg']['folder'];
    if (strpos($folder, httpRoot) === 0) $folder = substr($folder, strlen(httpRoot));
    if ($folder[strlen($folder)-1] != '/') $folder .= '/';
    $files = glob(filesRoot.$folder.'*.*');
    foreach($files as $file) {
      $item = $db->selectItem($this->sub_table['name'], "`source`='".$file."'");
      if (is_null($item)) {
        $type = getimagesize($file);
        $type = $type[2];
        switch ($type) {
          case 1: $supported = defined('IMG_GIF'); break;
          case 2: $supported = defined('IMG_JPG'); break;
          case 3: $supported = defined('IMG_PNG'); break;
          default: $supported = false;
        }
        if ($supported) {
          $item['caption'] = substr($file, strrpos($file, '/')+1);
          $item['image'] = $this->encodeFilename($item['caption']);
          $item['caption'] = substr($item['caption'], 0, strrpos($item['caption'], '.'));
          $item['owner'] = $owner['id'];
          $item['position'] = $db->count($this->sub_table['name'],"`owner`='".$item['owner']."'");
          $item['active'] = true;
          copy($file, filesRoot.'data/'.$this->name.'/'.$item['image']);
          $item['preview'] = $this->createPreview($item['image'], $type);
          $item['posted'] = gettime();
          $item['updated'] = gettime();
          $item['source'] = $file;
          $db->insert($this->sub_table['name'], $item);
          $owner['images']++;
          $db->updateItem($this->table['name'], $owner, "`id`='".$owner['id']."'");
        } else $session['message'] .= 'Формат файла "'.$file.'" не поддерживается<br>';
      } else $session['message'] .= 'Файл "'.$file.'" уже есть в галерее<br>';
    }
    goto($request['arg']['submitURL']);
  }*/
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Административные функции
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
        array('type'=>'memo','name'=>'tmplList','label'=>'Шаблон списка','height'=>'5'),
        array('type'=>'memo','name'=>'tmplListItem','label'=>'Шаблон элемента списка','height'=>'5'),
        array('type'=>'edit','name'=>'itemsPerPage','label'=>'Картинок на страницу','width'=>'50px', 'maxlength'=>'2'),
        array('type'=>'header', 'value'=>'Миниатюра'),
        array('type'=>'edit','name'=>'previewWidth','label'=>'Ширина','width'=>'50px', 'maxlength'=>'3'),
        array('type'=>'edit','name'=>'previewHeight','label'=>'Высота','width'=>'50px', 'maxlength'=>'3'),
        array('type'=>'header', 'value'=>'Полная картинка'),
        array('type'=>'memo','name'=>'tmplItem','label'=>'Шаблон','height'=>'5'),
        array('type'=>'edit','name'=>'imageWidth','label'=>'Ширина','width'=>'50px', 'maxlength'=>'3'),
        array('type'=>'edit','name'=>'imageHeight','label'=>'Высота','width'=>'50px', 'maxlength'=>'3'),
        array('type'=>'edit','name'=>'buttonBack','label'=>'Кнопка Назад','width'=>'200px'),
        array('type'=>'edit','name'=>'buttonNext','label'=>'Кнопка Вперед','width'=>'200px'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientRenderListItem($item)
  {
    global $request;

    $item['url'] = $request['link'].$item['id'].'/';
    $item['preview'] = img('data/imagelist/'.$item['preview']);
    $item['previewWidth'] = $this->settings['previewWidth'];
    $item['previewHeight'] = $this->settings['previewHeight'];
    $result = $this->replaceMacros($this->settings['tmplListItem'], $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientRenderList()
  {
    $result = str_replace('$(items)', parent::clientRenderList(), $this->settings['tmplList']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientRenderItem()
  {
    global $db, $page, $request;
    
    $item = $db->selectItem($this->table['name'], "`id`='".$page->topic."'");
    if (is_null($item)) $page->HttpError(404); else {
      $item['image'] = img('data/imagelist/'.$item['image']);
      $page->section[] = StripSlashes($item['caption']);

      $items = $db->select($this->table['name'], "`section`='".$page->id."'",  $this->table['sortMode'], $this->table['sortDesc'], '`id`,`caption`');

      for($i=0; $i < count($items); $i++) if ($items[$i]['id'] == $item['id']) {
        if ($i>0) $prev = $items[$i-1];
        if ($i<count($items)-1) $next = $items[$i+1];
      }
      
      #$plugins->clientOnPathSplit($item, $page->name.'/'.$item['id'].'/');
      #if (preg_match('/p[\d]+/i', $request['params'][0])) $page->subpage = substr(array_shift($request['params']), 1);
      #if (count($request['params']) && ($request['arg']['action'] != 'add_image')) {
      #  $page->content['topic'] = array_shift($request['params']);
      #  $result .= $this->renderImage($imagelist, $page->content['topic']);
      #} else $result .= $this->renderAlbum($imagelist);
      $item['back'] = (isset($prev)?'<a href="'.$request['path'].$prev['id'].'">'.$this->settings['buttonBack'].'</a> ':'');
      $item['next'] = (isset($next)?'<a href="'.$request['path'].$next['id'].'">'.$this->settings['buttonNext'].'</a>':'');
      $result = $this->replaceMacros($this->settings['tmplItem'], $item);

    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminAddItem()
  {
  global $page, $request;

    $form = array(
      'name' => 'AddForm',
      'caption' => 'Добавить картинку',
      'width' => '95%',
      'fields' => array (
        array ('type' => 'hidden', 'name' => 'action', 'value' => 'insert'),
        array ('type' => 'hidden', 'name' => 'section', 'value' => $request['arg']['section']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Название', 'width' => '100%', 'maxlength'=>'128'),
        array ('type' => 'file', 'name' => 'image', 'label'=>'Файл', 'width' => '70'),
      ),
      'buttons' => array('ok', 'cancel'),
    );
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  /*function loadDialog()
  {
    global $request, $page;
  
    $form = array(
      'name' => 'loadForm',
      'caption' => 'Добавить директорию',
      'width' => '500',
      'fields' => array (
        array ('type' => 'hidden', 'name'=>'action', 'value'=>'loadFolder'),
        array ('type' => 'hidden', 'name'=>'owner', 'value'=>$request['arg']['id']),
        array ('type' => 'edit', 'name' => 'folder', 'label' => 'Директория', 'width' => '100%'),
        array ('type' => 'text', 'value' => 
          'Введите путь к директории, из которой вы хотите загрузить фотографии.<br>'.
          'Например: /old/imagelist/album1/<br>'.
          'Так же можно воспользоваться <a href="'.httpRoot.'admin.php?mod=files" target="_blank">Файловым менеджером</a>. Для этого<ol>'.
          '<li>Откройте <a href="'.httpRoot.'admin.php?mod=files" target="_blank">файловый менеджер</a>'.
          '<li>Найдите в нем нужную директорию'.
          '<li>Щелкните один раз по ней мышью. Над списком файлов появится ее адрес'.
          '<li>Если у вас Internet Explorer, нажмите на "Скопировать имя", иначе просто выделите и скопируйте имя вручную'.
          '<li>Закройте файловый менеджер'.
          '<li>Вставьте текст из буфера в поле ввода и нажмите кнопку OK'.
          '</ol>'
        ),
      ),
      'buttons' => array('ok', 'cancel'),
    );
    $result = $page->renderForm($form, $item);
    return $result;
  } */
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminEditItem()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['id']."'");
    $form = array(
      'name' => 'EditForm',
      'caption' => 'Изменить картинку',
      'width' => '95%',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Заголовок', 'width' => '100%', 'maxlength' => '64'),
        array ('type' => 'file', 'name' => 'image', 'label'=>'Файл', 'width' => '70'),
        array ('type' => 'divider'),
        array ('type' => 'checkbox', 'name'=>'active', 'label'=>'Активно'),
        array ('type' => 'edit', 'name' => 'section', 'label' => 'Раздел', 'access'=>ADMIN),
        array ('type' => 'edit', 'name'=>'posted', 'label'=>'Написано'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>