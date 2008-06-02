<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# modURL (CMS Eresus™ Plugin)
# © 2006, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TmodURL extends TPlugin {
  var 
    $name = 'modurl',
    $title = 'modURL',
    $type = 'client',
    $version = '0.01',
    $description = 'Управление URL',
    $settings = array(
    );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Стандартные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TmodURL()
  # производит регистрацию обработчиков событий
  {
  global $plugins;
  
    parent::TPlugin();
    $plugins->events['clientOnStart'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientOnStart()
  {
    global $request, $page;
    
    if (isset($request['arg']['sid'])) {
      $request['path'] = $page->clientURL($request['arg']['sid']);
      $request['url'] = str_replace('sid='.$request['arg']['sid'], '', $request['url']);
      if ($request['url'][strlen($request['url'])-1] == '?') $request['url'] = substr($request['url'], 0, strlen($request['url'])-1);
      # Создаем безопасный URL для ссылок
      $request['link'] = $request['url'];
      if ((strpos($request['link'], '?') === false) && ($request['link'][strlen($request['link'])-1] != '/')) $request['link'] .= '/';
      unset($request['arg']['sid']);
      # Разбивка параметров вызова скрипта
      if (defined('CLIENTUI')) {
        $request['params'] = explode('/', substr($request['path'], strlen(httpRoot)));
        while (empty($request['params']) && (count($request['params'])>0)) array_shift($request['params']);
        while (empty($request['params'][count($request['params'])-1]) && (count($request['params'])>0)) array_pop($request['params']);
      }
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>