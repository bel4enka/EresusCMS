<?php
/**
 * Call
 *
 * Eresus 2
 *
 * Вызов других плагинов посредством макросов.
 *
 * @version 2.00
 *
 * @copyright   2005-2006, ProCreat Systems, http://procreat.ru/
 * @copyright   2007, Eresus Group, http://eresus.ru/
 * @license     http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @maintainer  Михалыч <mk@procreat.ru>
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

class TGallery extends TListContentPlugin {
  var $name = 'gallery';
	var $type = 'client,content,ondemand';
	var $title = 'Галерея';
	var $version = '1.06b';
	var $description = 'Галерея изображений';
	var $settings = array(
	  'tmplList' => '',
    'tmplListItem' => '',
    'tmplSubList' => '',
    'tmplSubListItem' => '',
    'tmplItem' => '',
    'buttonBack' => '[ &laquo; Назад ]',
    'buttonNext' => '[ Вперед &raquo; ]',
    'itemsPerPage' => 20,
    'imagesPerPage' => 20,
    'background' => '000000',
    'thumbnailWidth' => 120,
    'thumbnailHeight' => 90,
    'imageTemplate' => '',
    'imageResize' => false,
    'imageWidth' => 1024,
    'imageHeight' => 768,
    'logoEnable' => false,
    'logoPosition' => 'BR',
    'logoVPadding' => 10,
    'logoHPadding' => 10,
    'logoTVPadding' => 10,
    'logoTHPadding' => 10,
    'qualityJPEG' => 90,
    'logoOpacity' => 70,
  );
	var $table = array (
    'name' => 'gallery_albums',
    'key'=> 'id',
    'sortMode' => 'position',
    'sortDesc' => false,
    'columns' => array(
      array('name' => 'caption', 'caption' => 'Заголовок'),
      array('name' => 'posted', 'caption' => 'Дата'),
      array('name' => 'images', 'caption' => 'Картинок', 'align'=>'center'),
    ),
    'controls' => array (
      'delete' => '',
      'edit' => '',
      'position' => '',
      'toggle' => '',
    ),
    'tabs' => array(
      'width' => '150px',
      'items' => array(
        array('caption' => 'Новый альбом', 'name' => 'action', 'value' => 'create'),
      ),
    ),
    'fields' => array('active'),
    'sql' => "(
      `id` int(10) unsigned NOT NULL auto_increment,
      `section` int(10) unsigned default NULL,
      `position` int(10) unsigned NOT NULL default '0',
      `active` tinyint(1) unsigned default '0',
      `posted` datetime default NULL,
      `caption` varchar(128) default NULL,
      `images` int(10) unsigned default NULL,
      PRIMARY KEY  (`id`),
      KEY `section` (`section`),
      KEY `position` (`position`),
      KEY `active` (`active`),
      KEY `posted` (`posted`)
    ) TYPE=MyISAM COMMENT='Gallery albums';",
  );
	var $sub_table = array (
    'name' => 'gallery_images',
    'key'=> 'id',
    'sortMode' => 'position',
    'sortDesc' => false,
    'columns' => array(
      array('name' => 'caption', 'caption' => 'Описание', 'maxlength'=>300, 'striptags' => true),
      array('name' => 'posted', 'caption' => 'Дата'),
      array('name' => 'image', 'caption' => 'Файл'),
    ),
    'controls' => array (
      'delete' => '',
      'edit' => '',
      'position' => '',
    ),
    'tabs' => array(
      'width' => '150px',
      'items' => array(
        array('caption' => 'Новое изображение', 'name' => 'sub_action', 'value' => 'create'),
      ),
    ),
    'sql' => "(
      `id` int(10) unsigned NOT NULL auto_increment,
      `owner` int(10) unsigned default NULL,
      `position` int(10) unsigned NOT NULL default '0',
      `active` tinyint(1) unsigned default '0',
      `posted` datetime default NULL,
      `caption` varchar(128) default NULL,
      `image` varchar(128) default NULL,
      `thumbnail` varchar(128) default NULL,
      PRIMARY KEY  (`id`),
      KEY `owner` (`owner`),
      KEY `position` (`position`),
      KEY `active` (`active`),
      KEY `posted` (`posted`)
    ) TYPE=MyISAM COMMENT='Gallery iamges';",
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function install()
  {
    parent::install();
    $this->createTable($this->sub_table);
    umask(0000);
    if (!file_exists(filesRoot.'data/'.$this->name)) mkdir(filesRoot.'data/'.$this->name, 0777);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function uninstall()
  {
    parent::uninstall();
    $this->dropTable($this->sub_table);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function replaceMacros($template, $item)
  {
    global $request;

    $item['thumbnailWidth'] = $this->settings['thumbnailWidth'];
    $item['thumbnailHeight'] = $this->settings['thumbnailHeight'];
    $item['imageWidth'] = $this->settings['imageWidth'];
    $item['imageHeight'] = $this->settings['imageHeight'];
    if (isset($item['thumbnail']) && strpos($item['thumbnail'], httpRoot) === false) $item['thumbnail'] = httpRoot.'data/'.$this->name.'/'.$item['thumbnail'];
    if (isset($item['image']) && strpos($item['image'], httpRoot) === false) $item['image'] = httpRoot.'data/'.$this->name.'/'.$item['image'];
    $item['url'] = $request['path'].$item['id'].'/';
    if (isset($item['posted'])) $item['posted'] = FormatDate($item['posted']);

    $result = parent::replaceMacros($template, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function insert()
  {
  global $db, $request;

    $item = getArgs($db->fields($this->table['name']));
    $item['images'] = 0;
    $item['posted'] = gettime();
    dbShiftItems($this->table['name'], "`section`='".$item['section']."'", +1);
    $db->insert($this->table['name'], $item);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['update']."'");
    $item = SetArgs($item);
    if (!isset($request['arg']['active'])) $item['active'] = false;
    $db->updateItem($this->table['name'], $item, "`id`='".$item['id']."'");
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function delete()
  {
  global $db, $request, $page;

    $owner = $db->selectItem($this->table['name'], "`".$this->table['key']."`='".$request['arg']['delete']."'");

    $items = $db->select($this->sub_table['name'], "`owner`='".$owner['id']."'");
    if (count($items)) foreach($items as $item) {
      unlink(filesRoot.'data/'.$this->name.'/'.$item['image']);
      unlink(filesRoot.'data/'.$this->name.'/'.$item['thumbnail']);
      $db->delete($this->sub_table['name'], "`id`='".$item['id']."'");
    }
    $db->delete($this->table['name'], "`id`='".$owner['id']."'");
    goto($page->url(array('delete'=>'')));
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
  function overlayLogo(&$src, $thmb = false)
  {
    $logo = @imageCreateFromGIF(filesRoot.'style/'.$this->name.'/'.($thmb?'thumbnail':'logo').'.gif');
    if ($logo) {
      $sw = imageSX($src);
      $sh = imageSY($src);
      $lw = imageSX($logo);
      $lh = imageSY($logo);
      $pfx = $thmb?'logoT':'logo';
      switch($this->settings['logoPosition']) {
        case 'TL':
          $x = $this->settings[$pfx.'HPadding'];
          $y = $this->settings[$pfx.'VPadding'];
        break;
        case 'TR':
          $x = $sw-$lw-$this->settings[$pfx.'HPadding'];
          $y = $this->settings[$pfx.'VPadding'];
        break;
        case 'BL':
          $x = $this->settings[$pfx.'HPadding'];
          $y = $sh-$lh-$this->settings[$pfx.'VPadding'];
        break;
        case 'BR':
          $x = $sw-$lw-$this->settings[$pfx.'HPadding'];
          $y = $sh-$lh-$this->settings[$pfx.'VPadding'];
        break;
      }
      imageCopyMerge($src, $logo, $x, $y, 0, 0, $lw, $lh, $this->settings['logoOpacity']);
      imageDestroy($logo);
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function createThumbnail($filename, $type)
  {
    $path = filesRoot.'data/'.$this->name.'/';
    switch ($type) {
      case IMG_GIF: $src = imageCreateFromGIF($path.$filename); break;
      case IMG_JPEG: $src = imageCreateFromJPEG($path.$filename); break;
      case IMG_PNG: $src = imageCreateFromPNG($path.$filename); break;
    }
    $sW = imageSX($src);
    $sH = imageSY($src);
    $resizer = ($sW > $sH)?($sW / $this->settings['thumbnailWidth']):($sH / $this->settings['thumbnailHeight']);
    $dst = imageCreateTrueColor($this->settings['thumbnailWidth'], $this->settings['thumbnailHeight']);
    $R = hexdec(substr($this->settings['background'], 0, 2));
    $G = hexdec(substr($this->settings['background'], 2, 2));
    $B = hexdec(substr($this->settings['background'], 4, 2));
    imageFill($dst, 0, 0, imageColorAllocate($dst, $R, $G, $B));
    $dW = floor($sW / $resizer);
    $dH = floor($sH / $resizer);
    imageCopyResampled($dst, $src, round(($this->settings['thumbnailWidth']-$dW)/2), round(($this->settings['thumbnailHeight']-$dH)/2), 0, 0, $dW, $dH, $sW, $sH);
    if ($this->settings['logoEnable']) $this->overlayLogo($dst, true);
    $filename = substr($filename, 0, strrpos($filename, '.')).'-thmb.jpg';
    ImageJPEG($dst, $path.$filename, $this->settings['qualityJPEG']);
    imageDestroy($src);
    imageDestroy($dst);
    return $filename;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function resizeImage($filename, $type)
  {
    if ($this->settings['imageResize']) {
      $path = filesRoot.'data/'.$this->name.'/';
      switch ($type) {
        case IMG_GIF: $src = imageCreateFromGIF($path.$filename); break;
        case IMG_JPEG: $src = imageCreateFromJPEG($path.$filename); break;
        case IMG_PNG: $src = imageCreateFromPNG($path.$filename); break;
      }
      if ($src) {
        $sW = imageSX($src);
        $sH = imageSY($src);
        if ($sW > $this->settings['imageWidth'] || $sH > $this->settings['imageHeight']) {
          $resizer = ($sW > $sH)?($sW / $this->settings['imageWidth']):($sH / $this->settings['imageHeight']);
          $dW = floor($sW / $resizer);
          $dH = floor($sH / $resizer);
        } else {
          $dW = $sW;
          $dH = $sH;
        }
        $dst = imageCreateTrueColor($dW, $dH);
        imageCopyResampled($dst, $src, 0, 0, 0, 0, $dW, $dH, $sW, $sH);
        if ($this->settings['logoEnable']) $this->overlayLogo($dst);
        ImageJPEG($dst, $path.$filename, $this->settings['qualityJPEG']);
        imageDestroy($src);
        imageDestroy($dst);
      } else die("Error reading file $path$filename");
    }
    return $filename;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function insertImage()
  {
  global $db, $request, $page, $user, $session;

    $type = getimagesize($_FILES['image']['tmp_name']);
    if (in_array($type[2], array(IMG_GIF, IMG_JPEG, IMG_PNG))) {
      $owner = $db->selectItem($this->table['name'], "`id`='".$request['arg']['owner']."'");
      $item = getArgs($db->fields($this->sub_table['name']));
      unset($item['id']);
      $item['image'] = $this->encodeFilename($_FILES['image']['name']);
      $item['position'] = $db->count($this->sub_table['name'],"`owner`='".$item['owner']."'");
      $item['active'] = true;
      upload('image', filesRoot.'data/'.$this->name.'/'.$item['image']);
      $item['thumbnail'] = $this->createthumbnail($item['image'], $type[2]);
      $item['image'] = $this->resizeImage($item['image'], $type[2]);
      $item['posted'] = gettime();
      $db->insert($this->sub_table['name'], $item);
      $owner['images']++;
      $db->updateItem($this->table['name'], $owner, "`id`='".$owner['id']."'");
      goto($page->url(array('sub_action' => '')));
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function updateImage()
  {
  global $db, $request, $page;

    $item = $db->selectItem($this->sub_table['name'], "`id`='".$request['arg']['sub_update']."'");
    $album = $db->selectItem($this->table['name'], "`id`='".$item['owner']."'");
    $image = $item['image'];
    $item = GetArgs($item, array(), array('id'));
    if (!empty($_FILES['image']['tmp_name'])) {
      $type = getimagesize($_FILES['image']['tmp_name']);
      if (in_array($type[2], array(IMG_GIF, IMG_JPEG, IMG_PNG))) {
        if (is_file(filesRoot.'data/'.$this->name.'/'.$image)) unlink(filesRoot.'data/'.$this->name.'/'.$image);
        if (is_file(filesRoot.'data/'.$this->name.'/'.$item['thumbnail'])) unlink(filesRoot.'data/'.$this->name.'/'.$item['thumbnail']);
        upload('image', filesRoot.'data/'.$this->name.'/'.$item['image']);
        $item['thumbnail'] = $this->createThumbnail($item['image'], $type[2]);
        $item['image'] = $this->resizeImage($item['image'], $type[2]);
      }
    }
    $db->updateItem($this->sub_table['name'], $item, "`id`='".$item['id']."'");
    sendNotify(admUpdated.': <a href="'.$page->url(array('sub_update'=>'')).'">'.$album['caption'].'</a>', array('url'=>$page->url(array('sub_id'=>''))));
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function deleteImage()
  {
  global $db, $request, $page;

    $item = $db->selectItem($this->sub_table['name'], "`id`='".$request['arg']['sub_delete']."'");
    $owner = $db->selectItem($this->table['name'], "`id`='".$item['owner']."'");
    $owner['images']--;
    $db->updateItem($this->table['name'], $owner, "`id`='".$owner['id']."'");
    unlink(filesRoot.'data/'.$this->name.'/'.$item['image']);
    unlink(filesRoot.'data/'.$this->name.'/'.$item['thumbnail']);
    $db->delete($this->sub_table['name'], "`id`='".$request['arg']['sub_delete']."'");
    sendNotify(admDeleted.': <a href="'.$page->url(array('sub_delete'=>'')).'">'.$owner['caption'].'</a>', array('url'=>$page->url(array('sub_id'=>''))));
    goto($page->url(array('sub_delete'=>'')));
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sub_up()
  {
  global $page, $db, $request;

    dbReorderItems($this->sub_table['name'],"`owner`='".$request['arg']['id']."'");
    $item = $db->selectItem($this->sub_table['name'], "`".$this->table['key']."`='".$request['arg']['sub_up']."'");
    if ($item['position'] > 0) {
      $temp = $db->selectItem($this->sub_table['name'],"`owner`='".$request['arg']['id']."' AND `position`='".($item['position']-1)."'");
      $item['position']--;
      $temp['position']++;
      $db->updateItem($this->sub_table['name'], $item, "`".$this->table['key']."`='".$item['id']."'");
      $db->updateItem($this->sub_table['name'], $temp, "`".$this->table['key']."`='".$temp['id']."'");
    }
    goto($page->url(array('sub_up'=>'')));
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sub_down()
  {
  global $page, $db, $request;

    dbReorderItems($this->sub_table['name'],"`owner`='".$request['arg']['id']."'");
    $count = $db->count($this->sub_table['name']);
    $item = $db->selectItem($this->sub_table['name'], "`".$this->table['key']."`='".$request['arg']['sub_down']."'");
    if ($item['position'] < $count-1) {
      $temp = $db->selectItem($this->sub_table['name'],"`owner`='".$request['arg']['id']."' AND `position`='".($item['position']+1)."'");
      $item['position']++;
      $temp['position']--;
      $db->updateItem($this->sub_table['name'], $item, "`".$this->table['key']."`='".$item['id']."'");
      $db->updateItem($this->sub_table['name'], $temp, "`".$this->table['key']."`='".$temp['id']."'");
    }
    goto($page->url(array('sub_down'=>'')));
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function updateSettings()
  {
    global $request;

    parent::updateSettings();
    if (is_uploaded_file($_FILES['logoImage']['tmp_name'])) upload('logoImage', filesRoot.'style/gallery/logo.gif');
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function settings()
  {
    global $page, $request;

    if (arg('action') == 'thumbnail') {
      $files = glob(filesRoot.'data/'.$this->name.'/*.jpg');
      $count = 0;
      for($i=0; $i<count($files); $i++) if (strpos($files[$i], '-thmb') === false) {
        $type = getimagesize($files[$i]);
        $type = $type[2];
        $item['thumbnail'] = $this->createThumbnail(basename($files[$i]), $type);
        $count++;
      }
      InfoMessage('Миниатюры перестроены.');
    }
    $image = 'style/gallery/logo.gif';
    $image = is_file(filesRoot.$image)?'<a href="'.httpRoot.$image.'">На изображение (GIF)</a>':'На изображение (GIF)';
    $thmb = 'style/gallery/thumbnail.gif';
    $thmb = is_file(filesRoot.$thmb)?'<a href="'.httpRoot.$thmb.'">На миниатюра (GIF)</a>':'На миниатюра (GIF)';

    $templates[0] = array();
    $templates[1] = array();
    $dir = filesRoot.'templates/';
    $hnd = opendir($dir);
    while (($filename = readdir($hnd))!==false) if (preg_match('/.*\.tmpl$/', $filename)) {
      $description = file_get_contents($dir.$filename);
      preg_match('/<!--(.*?)-->/', $description, $description);
      $description = trim($description[1]);
      $templates[0][] = $description;
      $templates[1][] = substr($filename, 0, strrpos($filename, '.'));
    }

    $form = array(
      'name' => 'settings',
      'caption' => $this->title.' '.$this->version,
      'width' => '400px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'header', 'value'=>'Список альбомов'),
        array('type'=>'memo','name'=>'tmplList','label'=>'Шаблон списка','height'=>'5'),
        array('type'=>'memo','name'=>'tmplListItem','label'=>'Шаблон элемента списка','height'=>'5'),
        array('type'=>'edit','name'=>'itemsPerPage','label'=>'Альбомов на страницу','width'=>'50px', 'maxlength'=>'2'),
        array('type'=>'header', 'value'=>'Список изображений'),
        array('type'=>'memo','name'=>'tmplSubList','label'=>'Шаблон списка','height'=>'5'),
        array('type'=>'memo','name'=>'tmplSubListItem','label'=>'Шаблон элемента списка','height'=>'5'),
        array('type'=>'edit','name'=>'imagesPerPage','label'=>'Изображений на страницу','width'=>'50px', 'maxlength'=>'2'),
        array('type'=>'header', 'value'=>'Миниатюра'),
        array('type'=>'edit','name'=>'thumbnailWidth','label'=>'Ширина','width'=>'50px', 'maxlength'=>'3', 'comment'=>'пикселей'),
        array('type'=>'edit','name'=>'thumbnailHeight','label'=>'Высота','width'=>'50px', 'maxlength'=>'3', 'comment'=>'пикселей'),
        array('type'=>'edit','name'=>'background','label'=>'Цвет фона','width'=>'50px', 'maxlength'=>'6', 'comment' => 'Формат: RRGGBB'),
        array('type'=>'text','value'=>'<center><a href="'.$request['url'].'&action=thumbnail"><b>Перестроить миниатюры</b></a></center>'),
        array('type'=>'header', 'value'=>'Просмотр изображения'),
        array('type'=>'select','name' =>'imageTemplate','label' => 'Шаблон страницы', 'items' => $templates[0], 'values' => $templates[1]),
        array('type'=>'checkbox','name'=>'imageResize','label'=>'Уменьшать изображения больше чем:'),
        array('type'=>'edit','name'=>'imageWidth','label'=>'','width'=>'50px', 'maxlength'=>'4', 'comment' => 'пикселей по ширине'),
        array('type'=>'edit','name'=>'imageHeight','label'=>'','width'=>'50px', 'maxlength'=>'4', 'comment' => 'пикселей по высоте'),
        array('type'=>'memo','name'=>'tmplItem','label'=>'Шаблон','height'=>'5'),
        array('type'=>'edit','name'=>'buttonBack','label'=>'Кнопка Назад','width'=>'200px'),
        array('type'=>'edit','name'=>'buttonNext','label'=>'Кнопка Вперед','width'=>'200px'),
        array('type'=>'header', 'value'=>'Логотип'),
        array('type'=>'checkbox','name'=>'logoEnable','label'=>'Накладывать логотип'),
        array('type'=>'edit','name'=>'logoOpacity','label'=>'Прозрачность','width'=>'20px', 'maxlength'=>'3', 'comment' => '0 - 100'),
        array('type'=>'select','name'=>'logoPosition','label'=>'Положение', 'items'=>array('Верхний левый угол','Верхний правый угол','Нижний левый угол','Нижний правый угол'), 'values'=>array('TL','TR','BL','BR')),
        array('type'=>'file','name'=>'logoImage','label'=>$image,'width'=>'50'),
        array('type'=>'edit','name'=>'logoVPadding','label'=>'Отступ','width'=>'20px', 'maxlength'=>'4', 'comment'=>'пикселов по вертикали'),
        array('type'=>'edit','name'=>'logoHPadding','label'=>'Отступ','width'=>'20px', 'maxlength'=>'4', 'comment'=>'пикселов по горизонтали'),
        array('type'=>'file','name'=>'logoThumbnail','label'=>$thmb,'width'=>'50'),
        array('type'=>'edit','name'=>'logoTVPadding','label'=>'Отступ','width'=>'20px', 'maxlength'=>'4', 'comment'=>'пикселов по вертикали'),
        array('type'=>'edit','name'=>'logoTHPadding','label'=>'Отступ','width'=>'20px', 'maxlength'=>'4', 'comment'=>'пикселов по горизонтали'),
        array('type'=>'header', 'value'=>'Прочее'),
        array('type'=>'edit','name'=>'qualityJPEG','label'=>'Качество JPEG','width'=>'20px', 'maxlength'=>'3', 'comment' => '0 - 100'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = '<table><tr><td style="vertical-align: top;">'.$page->renderForm($form, $this->settings).'</td><td style="vertical-align: top;">'.
      $page->window(array('caption'=>'Макросы', 'body' =>
        '<b>Список альбомов</b> - Шаблон списка'.
        '<ul>'.
        '<li><b>$(items)</b> - список альбомов</li>'.
        '</ul>'.
        '<b>Список альбомов</b> - Шаблон элемента списка'.
        '<ul>'.
        '<li><b>$(caption)</b> - Название альбома</li>'.
        '<li><b>$(thumbnail)</b> - URL первой миниатюры</li>'.
        '<li><b>$(thumbnailWidth)</b> - ширина миниатюры</li>'.
        '<li><b>$(thumbnailHeight)</b> - высота миниатюры</li>'.
        '<li><b>$(posted)</b> - Дата добавления</li>'.
        '<li><b>$(url)</b> - URL изображений альбома</li>'.
        '<li><b>$(images)</b> - изображений в альбоме</li>'.
        '</ul>'.
        '<b>Список изображений</b> - Шаблон списка'.
        '<ul>'.
        '<li><b>$(items)</b> - список изображений</li>'.
        '</ul>'.
        '<b>Список изображений</b> - Шаблон элемента списка'.
        '<ul>'.
        '<li><b>$(caption)</b> - Название изображения</li>'.
        '<li><b>$(thumbnail)</b> - URL миниатюры</li>'.
        '<li><b>$(thumbnailWidth)</b> - ширина миниатюры</li>'.
        '<li><b>$(thumbnailHeight)</b> - высота миниатюры</li>'.
        '<li><b>$(posted)</b> - Дата добавления</li>'.
        '<li><b>$(url)</b> - URL полноразмерного просмотра</li>'.
        '</ul>'.
        '<b>Просмотр изображения</b>'.
        '<ul>'.
        '<li><b>$(caption)</b> - Название изображения</li>'.
        '<li><b>$(image)</b> - URL изображения</li>'.
        '<li><b>$(imageWidth)</b> - ширина изображения</li>'.
        '<li><b>$(imageHeight)</b> - высота изображения</li>'.
        '<li><b>$(list)</b> - Список ссылок на другие изображения альбома</li>'.
        '<li><b>$(back)</b> - Кнопка "Назад"</li>'.
        '<li><b>$(next)</b> - Кнопка "Вперед"</li>'.
        '</ul>'
      )).
      '</td></tr></table>';
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderListItem($item)
  {
    global $request, $db;

    $item['url'] = $request['link'].$item['id'].'/';
    $image = $db->select($this->sub_table['name'], "`owner`='".$item['id']."'", $this->sub_table['sortMode'], $this->sub_table['sortDesc'], '', 1);
    $item['thumbnail'] = httpRoot.(count($image)?'data/'.$this->name.'/'.$image[0]['thumbnail']:'style/dot.gif');
    $item['image'] = httpRoot.(count($image)?'data/'.$this->name.'/'.$image[0]['image']:'style/dot.gif');
    $item['thumbnailWidth'] = $this->settings['thumbnailWidth'];
    $item['thumbnailHeight'] = $this->settings['thumbnailHeight'];
    $result = $this->replaceMacros($this->settings['tmplListItem'], $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderList()
  {
    global $page;
    if (!$page->subpage) $page->subpage = 1;
    $result = str_replace('$(items)', parent::clientRenderList(), $this->settings['tmplList']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function renderAlbum($gallery)
  {
    global $Eresus, $page;

    $result = '';
    $items = $Eresus->db->select(
      $this->sub_table['name'],
      "`owner`='".$gallery['id']."'",
      $this->sub_table['sortMode'],
      $this->sub_table['sortDesc'] == 1
    );
    if (count($items)) foreach($items as $item)
      $result .= $this->replaceMacros($this->settings['tmplSubListItem'], $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function renderImage($gallery, $id)
  {
    global $db, $page, $request;

    $item = $db->selectItem($this->sub_table['name'], "`id`='".$id."'");
    if (is_null($item)) $page->HttpError(404); else {
      $items = $db->select($this->sub_table['name'], "`owner`='".$gallery['id']."'",  $this->sub_table['sortMode'], $this->sub_table['sortDesc'], '`id`,`caption`');
      $path = $request['path'].$gallery['id'].'/';
      $item['list'] = '';
      for($i=0; $i < count($items); $i++) {
        $item['list'] .= '<a href="'.$path.$items[$i]['id'].'"'.($items[$i]['id'] == $item['id']?' class="selected"':'').'>['.($i+1).']</a> ';
        if ($items[$i]['id'] == $item['id']) {
          if ($i>0) $prev = $items[$i-1];
          if ($i<count($items)-1) $next = $items[$i+1];
        }
      }
      $item['back'] = (isset($prev)?'<a href="'.$path.$prev['id'].'">'.$this->settings['buttonBack'].'</a> ':'');
      $item['next'] = (isset($next)?'<a href="'.$path.$next['id'].'">'.$this->settings['buttonNext'].'</a>':'');
      $result = $this->replaceMacros($this->settings['tmplItem'], $item);
    }
    $page->template = $this->settings['imageTemplate'];
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderItem()
  {
    global $Eresus, $page;

    $result = '';
    $gallery = $Eresus->db->selectItem($this->table['name'], "(`id`='".$page->topic."')AND(`active`='1')");
    if (is_null($gallery)) {
      $item = $page->Error404();
      $result = $item['content'];
    } else {
    	#$page->title[] = StripSlashes($gallery['caption']);
      #$plugins->clientOnPathSplit($gallery, $page->name.'/'.$gallery['id'].'/');
      #$page->content['section'] .= ' &raquo; '.StripSlashes($gallery['caption']);
      if (count($Eresus->request['params']) && $Eresus->request['params'][0][0] == 'p') {
        $page->content['sub_page'] = substr($Eresus->request['params'][0], 1);
        array_shift($Eresus->request['params']);
      }
      if (count($Eresus->request['params']) && (arg('action') != 'add_image')) {
      	$page->topic = array_shift($Eresus->request['params']);
      	$result .= $this->renderImage($gallery, $page->topic);
      } else $result .= str_replace('$(items)', $this->renderAlbum($gallery), $this->settings['tmplSubList']);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRenderContent()
  {
  global $db, $page, $user, $request;

    if (isset($request['arg']['id'])) {
      $item = $db->selectItem($this->table['name'], "`".$this->table['key']."` = '".$request['arg']['id']."'");
      $page->title .= ' - '.StripSlashes($item['caption']);
    }
    if (isset($request['arg']['sub_update'])) $this->updateImage();
    elseif (isset($request['arg']['sub_id'])) $result = $this->editImage();
    elseif (isset($request['arg']['sub_delete'])) $this->deleteImage();
    elseif (isset($request['arg']['sub_up']) && isset($this->sub_table['controls']['position'])) $this->sub_up();
    elseif (isset($request['arg']['sub_down']) && isset($this->sub_table['controls']['position'])) $this->sub_down();
    elseif (isset($request['arg']['sub_action'])) switch ($request['arg']['sub_action']) {
     case 'create': $result = $this->createImage(); break;
     case 'insert': $this->insertImage(); break;
    } elseif (isset($request['arg']['update']) && isset($this->table['controls']['edit'])) $this->update();
    elseif (isset($request['arg']['delete']) && isset($this->table['controls']['delete'])) $this->delete();
    elseif (isset($request['arg']['toggle']) && isset($this->table['controls']['toggle'])) $this->toggle();
    elseif (isset($request['arg']['up']) && isset($this->table['controls']['position'])) $this->up($request['arg']['up']);
    elseif (isset($request['arg']['down']) && isset($this->table['controls']['position'])) $this->down($request['arg']['down']);
    elseif (isset($request['arg']['id']) && isset($this->table['controls']['edit'])) $result = $this->adminEditItem();
    elseif (isset($request['arg']['action'])) switch ($request['arg']['action']) {
     case 'create': if(isset($this->table['controls']['edit'])) $result = $this->adminAddItem(); break;
     case 'insert': $this->insert(); break;
    } else {
      $this->table['condition'] = "`section`='".$request['arg']['section']."'";
      $result = $page->renderTable($this->table);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminAddItem()
  {
  global $db, $page, $request;

    $form = array(
      'name' => 'AddForm',
      'caption' => 'Новый альбом',
      'width' => '95%',
      'fields' => array (
        array ('type' => 'hidden', 'name'=>'action', 'value'=>'insert'),
        array ('type' => 'hidden', 'name'=>'section', 'value'=>$request['arg']['section']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Заголовок', 'width' => '100%', 'maxlength' => '64'),
        array ('type' => 'checkbox', 'name'=>'active', 'label'=>'Активно', 'value' => true),
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
      'name' => 'EditForm',
      'caption' => 'Изменить альбом',
      'width' => '95%',
      'fields' => array (
        array ('type' => 'hidden','name'=>'update', 'value'=>$item['id']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => 'Заголовок', 'width' => '100%', 'maxlength' => '64'),
        array ('type' => 'checkbox', 'name'=>'active', 'label'=>'Активно'),
        array ('type' => 'edit', 'name'=>'posted', 'label'=>'Написано'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $item);
    $this->sub_table['condition'] = "`owner`='".$request['arg']['id']."'";
    $result .= $page->renderTable($this->sub_table, null, 'sub_');

    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function createImage()
  {
  global $page, $request;

    $form = array(
      'name' => 'AddForm',
      'caption' => 'Новое фото',
      'width' => '95%',
      'fields' => array (
        array('type' => 'hidden', 'name' => 'sub_action', 'value' => 'insert'),
        array('type' => 'hidden', 'name' => 'owner', 'value' => $request['arg']['id']),
        array('type' => 'edit', 'name' => 'caption', 'label' => 'Название', 'width' => '100%'),
        array('type' => 'file', 'name' => 'image', 'label'=>'Файл', 'width' => '70'),
      ),
      'buttons' => array('ok', 'cancel'),
    );
    $result = $page->renderForm($form);

    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function editImage()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->sub_table['name'], "`id`='".$request['arg']['sub_id']."'");
    $form = array(
      'name' => 'EditForm',
      'caption' => 'Изменить картинку',
      'width' => '95%',
      'fields' => array (
        array('type'=>'hidden','name'=>'sub_update', 'value'=>$item['id']),
        array('type' => 'edit', 'name' => 'caption', 'label' => 'Название', 'width' => '100%'),
        array('type' => 'file', 'name' => 'image', 'label'=>'Файл', 'width' => '70'),
        array('type' => 'divider'),
        array('type' => 'edit', 'name'=>'posted', 'label'=>'Написано'),
        array('type' => 'edit', 'name'=>'position', 'label'=>'Позиция', 'access'=>ADMIN),
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