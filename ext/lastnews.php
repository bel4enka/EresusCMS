<?php
/**
* LastNews, Eresus™ 2 plugin. 
*
* © 2005-2007, ProCreat Systems
* http://eresus.ru/
*
* @author  Mikhail Krasilnikov <mk@procreat.ru>
* @version  1.00
* @modified  2007-07-20
*/

class TLastNews extends TListContentPlugin {
  var $name = 'lastnews';
  var $type = 'client';
  var $title = 'Последние новости';
  var $version = '1.00a';
  var $description = 'Блок последних новостей';
  var $settings = array(
      'count' => 5,
      'tmplItem' => '<b>$(posted)</b><br /><a href="$(url)">$(caption)</a><br />',
      'dateFormat' => DATE_SHORT,
  );
  var $table = array (
      'name' => 'news',
      'key'=> 'id',
      'sortMode' => 'posted',
      'sortDesc' => true,
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Disable Functions
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function createTable($table) {}
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function dropTable($table) {}
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # Стандартные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TLastNews()
  {
    global $plugins;
  
    parent::TListContentPlugin();
    $plugins->events['clientOnPageRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function replaceMacros($template, $item, $dateFormat)
  {
  global $page;

    $item['preview'] = '<p>'.str_replace("\n", "</p>\n<p>", $item['preview']).'</p>';
    $item['posted'] = FormatDate($item['posted'], $dateFormat);
    $item['url'] = $page->clientURL($item['section']).$item['id'].'/';
    $result = parent::replaceMacros($template, $item);
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
        array('type'=>'memo','name'=>'tmplItem','label'=>'Шаблон','height'=>'3'),
        array('type'=>'edit','name'=>'count','label'=>'Показывать новостей', 'width'=>'100px'),
        array('type'=>'edit','name'=>'dateFormat','label'=>'Формат даты', 'width'=>'100px'),
    ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function render()
  {
    global $db;
    
    $result = '';
    $items = $db->select($this->table['name'], "`active`='1'", 'posted', true, '', $this->settings['count']);
    if (count($items)) foreach($items as $item) $result .= $this->replaceMacros($this->settings['tmplItem'], $item, $this->settings['dateFormat']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Обработчики событий
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientOnPageRender($text)
  {
  global $page;
  
    $text = preg_replace('/\$\(LastNews\)/i', $this->render(), $text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>