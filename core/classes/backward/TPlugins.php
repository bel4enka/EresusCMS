<?php

class TPlugins {
  var
    $list = array(), # Список всех плагинов
    $items = array(), # Массив плагинов
    $events = array(); # Таблица обработчиков событий
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function  TPlugins()
  {
  global $db;

    $items = $db->select('`plugins`', '', '`position`');
    if (count($items)) foreach($items as $item) $this->list[$item['name']] = $item;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function install($name)
  # Установка нового плагина
  {
  global $db;

    $filename = filesRoot.'ext/'.$name.'.php';
    if (file_exists($filename)) {
      include_once($filename);
      $Class = 'T'.$name;
      $this->items[$name] = new $Class;
      $this->items[$name]->install();
      $db->insert('plugins', $this->items[$name]->createPluginItem());
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function uninstall($name)
  # Удаление плагина
  {
  global $db;

    if (!isset($this->items[$name])) $this->load($name);
    if (isset($this->items[$name])) $this->items[$name]->uninstall();
    $item = $db->selectItem('plugins', "`name`='".$name."'");
    if (!is_null($item)) {
      $db->delete('plugins', "`name`='".$name."'");
      $db->update('plugins', "`position` = `position`-1", "`position` > '".$item['position']."'");
    }
    $filename = filesRoot.'ext/'.$name.'.php';
    #if (file_exists($filename)) unlink($filename);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function preload($include, $exclude)
  {
    if (count($this->list)) foreach($this->list as $item) if ($item['active']) {
      $type = explode(',', $item['type']);
      if (count(array_intersect($include, $type)) && count(array_diff($exclude, $type))) $this->load($item['name']);
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function load($name)
  {
    $result = isset($this->items[$name]) ? $this->items[$name] : false;
    if (isset($this->list[$name]) && !$result) {
      $filename = filesRoot.'ext/'.$name.'.php';
      if (file_exists($filename)) {
        include_once($filename);
        $Class = 'T'.$name;
        $this->items[$name] = new $Class;
        $result = $this->items[$name];
      } else $result = false;
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderContent()
  {
  global $page, $db, $user, $session, $request;

    $result = '';
    switch ($page->type) {
      case 'default':
        $plugin = new TContentPlugin;
        $result = $plugin->clientRenderContent();
      break;
      case 'list':
        if (isset($page->topic)) $page->httpError('404');
        $subitems = $db->select('pages', "(`owner`='".$page->id."') AND (`active`='1') AND (`access` >= '".($user['auth'] ? $user['access'] : GUEST)."')", "`position`");
        if (empty($page->content)) $page->content = '$(items)';
        $template = loadTemplate('std/SectionListItem');
        if ($template === false) $template['html'] = '<h1><a href="$(link)" title="$(hint)">$(caption)</a></h1>$(description)';
        $items = '';
        foreach($subitems as $item) {
          $items .= str_replace(
            array(
              '$(id)',
              '$(name)',
              '$(title)',
              '$(caption)',
              '$(description)',
              '$(hint)',
              '$(link)',
            ),
            array(
              $item['id'],
              $item['name'],
              $item['title'],
              $item['caption'],
              $item['description'],
              $item['hint'],
              $request['url'].($page->name == 'main' && !$page->owner ? 'main/' : '').$item['name'].'/',
            ),
            $template['html']
          );
          $result = str_replace('$(items)', $items, $page->content);
        }
      break;
      case 'url':
        goto($page->replaceMacros($page->content));
      break;
      default:
      if ($this->load($page->type)) {
        if (method_exists($this->items[$page->type], 'clientRenderContent'))
        $result = $this->items[$page->type]->clientRenderContent();
        else $session['errorMessage'] = sprintf(errMethodNotFound, 'clientRenderContent', get_class($this->items[$page->type]));
      }
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnStart()
  {
    if (isset($this->events['clientOnStart'])) foreach($this->events['clientOnStart'] as $plugin) $this->items[$plugin]->clientOnStart();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnURLSplit($item, $url)
  {
    if (isset($this->events['clientOnURLSplit'])) foreach($this->events['clientOnURLSplit'] as $plugin) $this->items[$plugin]->clientOnURLSplit($item, $url);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnTopicRender($text, $topic = null, $buttonBack = true)
  {
  global $page;
    if (isset($this->events['clientOnTopicRender'])) foreach($this->events['clientOnTopicRender'] as $plugin) $text = $this->items[$plugin]->clientOnTopicRender($text, $topic);
    if ($buttonBack) $text .= '<br /><br />'.$page->buttonBack();
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnContentRender($text)
  {
    if (isset($this->events['clientOnContentRender']))
      foreach($this->events['clientOnContentRender'] as $plugin) $text = $this->items[$plugin]->clientOnContentRender($text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
    if (isset($this->events['clientOnPageRender']))
      foreach($this->events['clientOnPageRender'] as $plugin) $text = $this->items[$plugin]->clientOnPageRender($text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientBeforeSend($text)
  {
    if (isset($this->events['clientBeforeSend']))
      foreach($this->events['clientBeforeSend'] as $plugin) $text = $this->items[$plugin]->clientBeforeSend($text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  /* function clientOnFormControlRender($formName, $control, $text)
  {
    if (isset($this->events['clientOnFormControlRender'])) foreach($this->events['clientOnFormControlRender'] as $plugin) $text = $this->items[$plugin]->clientOnFormControlRender($formName, $control, $text);
    return $text;
  }*/
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminOnMenuRender()
  {
    if (isset($this->events['adminOnMenuRender'])) foreach($this->events['adminOnMenuRender'] as $plugin) $this->items[$plugin]->adminOnMenuRender();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}

?>