<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# LinkEx для Eresus 2.00
# © 2006, ProCreat Systems
# Web: http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TLinkEx extends TPlugin {
  var 
    $name = 'linkex',
    $title = 'LinkEx',
    $type = 'client',
    $version = '3.00',
    $description = 'Блок ссылок "Наши проекты" (srv:1.04)',
    $settings = array(
      'url' => 'http://dvaslona.ru/tools/linkex/linkex.php',
      'mode' => 'default',
    );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # STANDARD METHODS
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TLinkEx()
  # производит регистрацию обработчиков событий
  {
  global $plugins, $page;
  
    parent::TPlugin();
    $plugins->events['clientOnPageRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ADMIN-SIDE METHODS
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function settings()
  {
  global $page;

    $form = array(
      'name' => 'settings',
      'caption' => $this->title.' '.$this->version,
      'width' => '100%',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'text', 'value' => 'Для вставки блока ссылок в шаблон страницы используйте макросы <b>$(LinkExIn)</b> и <b>$(LinkExOut)</b>'),
        array('type'=>'edit','name'=>'url','label'=>'URL скрипта','width'=>'100%'),
        array('type'=>'select','name'=>'mode','label'=>'Режим','items'=>array('Всплывающее окно', 'Просто текст'), 'values'=>array('popup', 'default')),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # CLIENT-SIDE METHODS
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientOnPageRender($text)
  {
    $url = $this->settings['url'].'?mode='.$this->settings['mode'].'&host='.$_SERVER['HTTP_HOST'].'&uri='.$_SERVER['REQUEST_URI'];
    $hfile = @fopen($url, 'r');
    $code = '';
    if ($hfile) {
      while (!feof($hfile)) $code .= StripSlashes(fgets($hfile));
      fclose($hfile);
    }
    if (strpos($code, 'LinkExPopup') !== false) {
      $text = preg_replace('/<\/head>/i', "<link href=\"/linkex/linkex.css\" rel=\"stylesheet\" type=\"text/css\">\n<script src=\"/linkex/linkex.js\" type=\"text/javascript\"></script>\n</head>\n", $text);
      if (preg_match('/<body[^>]*onload[^>]*>/si', $text)) $text = preg_replace('/<body([^>]*)onload=("|\')?([^>]*?)("|\')?>/si', '<body$1onload=$2$3;linkexAttach()$4>'."\n".$code, $text);
      else $text = preg_replace('/<body([^>]*)>/si', '<body$1 onload="linkexAttach()">'."\n".$code, $text);
      $text = str_replace('$(LinkExOut)', '', $text);
    } else $text = str_replace('$(LinkExOut)', $code, $text);
    $url = $this->settings['url'].'?mode=self&host='.$_SERVER['HTTP_HOST'].'&uri='.$_SERVER['REQUEST_URI'];
    $hfile = @fopen($url, 'r');
    $code = '';
    if ($hfile) {
      while (!feof($hfile)) $code .= StripSlashes(fgets($hfile));
      fclose($hfile);
    }
    $text = str_replace('$(LinkExIn)', $code, $text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>