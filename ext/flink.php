<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus™
# © 2005, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TFLink extends TPlugin {
  var $name = 'flink';
  var $title = 'FLink';
  var $type = 'client';
  var $version = '1.00a';
  var $description = 'Ссылки на файлы';
  var $settings = array(
    'folder' => 'download',
    'template' => '<a href="$(url)">$(caption)</a> ($(size))',
  );
  var $path = array(); # Строка пути
  var $level = -1; # Вложенность страницы
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function TFlink()
  # производит регистрацию обработчиков событий
  {
  global $plugins;

    parent::TPlugin();
    $plugins->events['clientOnPageRender'][] = $this->name;
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
        array('type'=>'edit','name'=>'folder','label'=>'Корневая папка','width'=>'100%'),
        array('type'=>'edit','name'=>'template','label'=>'Шаблон','width'=>'100%'),
        array('type'=>'text','value'=>
          "Маросы:\n".
          "<ul>\n".
          "  <li><b>$(url)</b> - ссылка на файл</li>\n".
          "  <li><b>$(caption)</b> - название файла (см. ниже)</li>\n".
          "  <li><b>$(filename)</b> - имя файла</li>\n".
          "  <li><b>$(size)</b> - размер файла</li>\n".
          "</ul>"
        ),
        array('type'=>'divider'),
        array('type'=>'text','value'=>"Заменяет макрос $(flink:имя_файла:название_файла) ссылкой на файл. ':название_файла' может быть опущено"),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
    global $page;

    $result = $text;
    preg_match_all('/\$\(flink:([^:\)]+)(:([^\)]+))?\)/', $text, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
    if (count($matches)) {
      if (substr($this->settings['folder'], 0, 1) == '/') $this->settings['folder'] = substr($this->settings['folder'], 1);
      if (substr($this->settings['folder'], -1) != '/') $this->settings['folder'] .= '/';
      $delta = 0;
      foreach($matches as $match) {
        $filename = filesRoot.$this->settings['folder'].$match[1][0];
        $info['filename'] = basename($filename);
        $info['size'] = FormatSize(@filesize($filename));
        $info['caption'] = empty($match[3][0])?$info['filename']:$match[3][0];
        $info['url'] = str_replace(filesRoot, httpRoot, $filename);
        $replace = $this->replaceMacros($this->settings['template'], $info);
        $result = substr_replace($result, $replace, $delta+$match[0][1], strlen($match[0][0]));
        $delta += strlen($replace) - strlen($match[0][0]);
      }
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>