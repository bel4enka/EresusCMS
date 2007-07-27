<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# blinking (CMS EresusЩ Plugin)
# © 2007, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TBlinking extends TPlugin {
  var $name = 'blinking';
  var $title = 'ћерцание';
  var $type = 'client';
  var $version = '1.00';
  var $description = 'Ёффект мерцани€';
  var $settings = array(
        'class' => 'blink',
        'interval' => '1000',
      );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # —тандартные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function TBlinking()
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
      'name' => 'settings',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'edit','name'=>'class','label'=>'»м€ класса', 'width'=>'100%'),
        array('type'=>'edit','name'=>'interval','label'=>'»нтервал', 'width'=>'50px', 'comment' => 'миллисекунд'),
    ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }

  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ќбработчики событий
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
    global $page;

    $browser = strtolower($_SERVER['HTTP_USER_AGENT']);
		$browser = strpos($browser, 'gecko')?'gecko':((strpos($browser, 'msie') && !strpos($browser, 'opera'))?'ie':'other');

    $page->scripts .= "
    
    var BlinkNodeList = new Array();
    var BlinkReady = false;
    
    function findBlinkNodes(root)
    {
      for(var i=0; i < root.childNodes.length; i++) if (root.childNodes[i].nodeType == 1) {
        if (root.childNodes[i].className.match(/(^|\s)".$this->settings['class']."($|\s)/)) BlinkNodeList[BlinkNodeList.length] = root.childNodes[i];
        findBlinkNodes(root.childNodes[i]);
      }
    }
    
    function blinkElements()
    {
      if (BlinkReady) {
        for(var i = 0; i < BlinkNodeList.length; i++) {
          if (BlinkNodeList[i].className.match(/(^|\s)".$this->settings['class']."_on($|\s)/))
            BlinkNodeList[i].className = BlinkNodeList[i].className.replace(/(^|\s)".$this->settings['class']."_on($|\s)/, ' ".$this->settings['class']." ');
          else
            BlinkNodeList[i].className = BlinkNodeList[i].className.replace(/(^|\s)".$this->settings['class']."($|\s)/, ' ".$this->settings['class']."_on ');
        }
      } else {
        BlinkReady = true;
        var s = findBlinkNodes(document.body);
      }
    }
    
    window.setInterval('blinkElements()', ".$this->settings['interval'].")
    
    ";
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>