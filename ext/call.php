<?php
/**
* Call (CMS Eresus™ 2 Plugin)
*
* This plugin adds a $(call) macros wich allows to call other plugins from templates
* Format:
*   $(call:plugin->method)
*
* @author Mikhail Krasilnikov <mk@procreat.ru>
* @version 1.00
* @modified 2007-07-16
*/
class TCall extends TPlugin {
  var $name = 'call';
  var $title = 'Call';
  var $type = 'client';
  var $version = '1.00a';
  var $description = 'Вызов плагинов из шаблонов';
  var $settings = array(
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Стандартные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TCall()
  # производит регистрацию обработчиков событий
  {
    global $plugins;
  
    parent::TPlugin();
    $plugins->events['clientOnPageRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Обработчики событий
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientOnPageRender($text)
  {
    global $plugins;
    
    preg_match_all('/\$\(call:(.*)->(.*)\)/Usi', $text, $calls, PREG_SET_ORDER);
    foreach($calls as $call) {
      if (isset($plugins->list[$call[1]])) {
        $plugin = isset($plugins->items[$call[1]]) ? $plugins->items[$call[1]] : $plugins->load($call[1]);
        if (method_exists($plugin, $call[2])) {
          $result = call_user_func(array($plugin, $call[2]));
        } else $result = "Method {$call[2]} not found in '{$call[1]}' plugin";
      } else $result = "Plugin '{$call[1]}' not installed";
      $text = str_replace($call[0], $result, $text);
    }
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>