<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus™
# © 2005, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TSQLConsole extends TPlugin {
  var 
    $name = 'sqlconsole',
    $title = 'SQLConsole',
    $type = 'admin',
    $version = '1.02b',
    $description = 'Web SQL консоль';
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Стандартные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TSQLConsole()
  # производит регистрацию обработчиков событий
  {
    global $plugins;
  
    parent::TPlugin();
    $plugins->events['adminOnMenuRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminRender()
  {
  global $db, $page, $request;
    
    $sql = isset($request['arg']['sql']) ? $request['arg']['sql'] : '';
    if (get_magic_quotes_gpc()) $sql = StripSlashes($sql);
    $form = array (
      'name' => 'SQLConsole',
      'caption' => ' Выполнить SQL запрос:',
      'width' => '100%',
      'fields' => array (
        array ('type' => 'hidden', 'name' => 'action', 'value' => 'exec'),
        array ('type' => 'memo', 'name' => 'sql', 'label' => 'Текст запроса', 'height' => '10', 'value'=>$sql),
      ),
      'buttons'=>array('ok'),
    );
    $result = $page->renderForm($form);
    if (isset($request['arg']['action']) && $request['arg']['action']=='exec') {
      $wnd['caption'] = 'Результат запроса';
      $wnd['width'] = '100%';
      $wnd['body'] = '';
      $hnd = $db->query($request['arg']['sql'], false);
      if ($hnd == false) $wnd['body'] .= mysql_error();
      if (gettype($hnd) == 'resource') {
        $wnd['body'] .= 'Получено рядов: '.mysql_num_rows($hnd)."<br>\n";
        $wnd['body'] .= "<table class=\"sqlconsole\">\n";
        $body = false;
        while ($row = mysql_fetch_assoc($hnd)) {
          if (!$body) {
            $keys = array_keys($row);
            $wnd['body'] .= "<tr>";
            foreach($keys as $key) $wnd['body'] .= "<th>".$key."</th>";
            $wnd['body'] .= "</tr>\n";
            $body = true;
          }
          $wnd['body'] .= "<tr>";
          foreach($row as $value) $wnd['body'] .= "<td>".$value."</td>";
          $wnd['body'] .= "</tr>\n";
        }
        $wnd['body'] .= "</table>\n";
      } elseif (gettype($hnd) == 'boolean') 
        if ($hnd) $wnd['body'] .= "Запрос выполнен успешно<br>\nЗатронуто рядов: ".mysql_affected_rows($db->Connection);
        else "Запрос выполнить не удалось";
      $result .= '<br>'.$page->window($wnd);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Обработчики событий
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminOnMenuRender()
  {
    global $page;
  
    $page->addMenuItem('Расширения', array ("access"  => ROOT, "link"  => "sqlconsole", "caption"  => "Консоль SQL", "hint"  => "Консоль SQL"));
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>