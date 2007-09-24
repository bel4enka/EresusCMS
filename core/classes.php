<?php
/**
 * Основные классы системы
 * 
 * Система управления контентом Eresus™ 2
 * © 2004-2007, ProCreat Systems, http://procreat.ru/
 * © 2007, Eresus Group, http://eresus.ru/
 * 
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# КЛАСС "ПЛАГИНЫ"
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

class Plugins {
  var $list = array(); # Список всех плагинов
  var $items = array(); # Массив плагинов
  var $events = array(); # Таблица обработчиков событий
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function  Plugins()
  {
  	global $Eresus;

    $items = $Eresus->db->select('`plugins`', '', '`position`');
    if (count($items)) foreach($items as $item) $this->list[$item['name']] = $item;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function install($name)
  # Установка нового плагина
  {
  	global $Eresus;

    $filename = filesRoot.'ext/'.$name.'.php';
    if (file_exists($filename)) {
      include_once($filename);
      $ClassName = $name;
      if (!class_exists($ClassName) && class_exists('T'.$ClassName)) $ClassName = 'T'.$ClassName; # FIX: Обратная совместимость с версиями до 2.10b2
      if (class_exists($ClassName)) {
	      $this->items[$name] = new $ClassName;
	      $this->items[$name]->install();
	      $Eresus->db->insert('plugins', $this->items[$name]->__item());
      } else FatalError(sprintf(errClassNotFound, $ClassName));
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function uninstall($name)
  # Удаление плагина
  {
  	global $Eresus;

    if (!isset($this->items[$name])) $this->load($name);
    if (isset($this->items[$name])) $this->items[$name]->uninstall();
    $item = $Eresus->db->selectItem('plugins', "`name`='".$name."'");
    if (!is_null($item)) {
      $Eresus->db->delete('plugins', "`name`='".$name."'");
      $Eresus->db->update('plugins', "`position` = `position`-1", "`position` > '".$item['position']."'");
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
        $ClassName = $name;
        if (!class_exists($ClassName) && class_exists('T'.$ClassName)) $ClassName = 'T'.$ClassName; # FIX: Обратная совместимость с версиями до 2.10b2
	      if (class_exists($ClassName)) {
	        $this->items[$name] = new $ClassName;
	        $result = $this->items[$name];
	      } else FatalError(sprintf(errClassNotFound, $name));
      } else $result = false;
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  /**
   * Отрисовка контента раздела
   *
   * @return stirng  Контент
   */
  function clientRenderContent()
  {
  	global $page, $db, $user, $session, $request;

    $result = '';
    switch ($page->type) {
      case 'default':
        $plugin = new ContentPlugin;
        $result = $plugin->clientRenderContent();
      break;
      case 'list':
        if ($page->topic) $page->httpError(404);
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
        else ErrorMessage(sprintf(errMethodNotFound, 'clientRenderContent', get_class($this->items[$page->type])));
      } else die("FIXME: ".__FILE__." ".__LINE__);
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
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

/* * * * * * * * * * * * * * * * * * * * * * * *
*
*     Классы-предки для создания плагинов
*
* * * * * * * * * * * * * * * * * * * * * * * */

/**
 * Родительский класс для всех плагинов
 *
 * @var  string  $name        Имя плагина
 * @var  string  $version	   	Версия плагина
 * @var  string  $kernel      Необходимая версия Eresus
 * @var  string  $title       Название плагина
 * @var  string  $description	Описание плагина
 * @var  string  $type        Тип плагина, перечисленые через запятую ключевые слова:
 *                            	client   - Загружать плагин в КИ
 *                              admin    - Загружать плагин в АИ
 *                              content  - Плагин предоставляет тип контента
 *                              ondemand - Не загружать плагин автоматически
 * @var  array   $settings    Настройки плагина
 */
class Plugin {
  var $name;
  var $version = '0.00';
  var $kernel = '2.10b2';
  var $title = 'no title';
  var $description = '';
  var $type;
  var $settings = array();
  var $dirData; # Директория данных (/data/имя_плагина)
  var $urlData; # URL данных
  var $dirCode; # Директория скриптов (/ext/имя_плагина)
  var $urlCode; # URL скриптов
  var $dirStyle; # Директория оформления (style/имя_плагина)
  var $urlStyle; # URL оформления
/**
 * Конструктор
 *
 * Производит чтение настроек плагина и подключение языковых файлов
 */
function Plugin()
{
	global $Eresus, $plugins, $locale;

	$this->name = strtolower(get_class($this));
	# Обратная совместимость с версиями до 2.10b2
	if (!property_exists($this, 'kernel')) $this->name = substr($this->name, 1);
	
  if (!empty($this->name) && isset($plugins->list[$this->name])) {
    $this->settings = decodeOptions($plugins->list[$this->name]['settings'], $this->settings);
		# Если установлена версия плагина отличная от установленной ранее
		# то необходимо произвести обновление информации о плагине в БД
    if ($this->version != $plugins->list[$this->name]['version']) $this->resetPlugin();
  }
  $this->dirData = $Eresus->fdata.$this->name.'/'; 
  $this->urlData = $Eresus->data.$this->name.'/'; 
  $this->dirCode = $Eresus->froot.'ext/'.$this->name.'/'; 
  $this->urlCode = $Eresus->root.'ext/'.$this->name.'/'; 
  $this->dirStyle = $Eresus->fstyle.$this->name.'/'; 
  $this->urlStyle = $Eresus->style.$this->name.'/'; 
  $filename = filesRoot.'lang/'.$this->name.'/'.$locale['lang'].'.inc';
  if (is_file($filename)) include_once($filename);
}
//------------------------------------------------------------------------------
/**
 * Возвращает информацию о плагине
 *
 * @param  array  $item  Предыдущая версия информации (по умолчанию null)
 *
 * @return  array  Массив информации, пригодный для записи в БД
 */
function __item($item = null)
{
	global $Eresus;

  $result['name'] = $this->name;
  $result['type'] = $this->type;
  $result['active'] = is_null($item)? true : $item['active'];
  $result['position'] = is_null($item) ? $Eresus->db->count('plugins') : $item['position'];
  $result['settings'] = is_null($item) ? encodeOptions($this->settings) : $item['settings'];
  $result['title'] = $this->title;
  $result['version'] = $this->version;
  $result['description'] = $this->description;
  return $result;
}
//------------------------------------------------------------------------------
/**
 * Чтение настроек плагина из БД
 *
 * @return  bool  Результат выполнения
 */
function loadSettings()
{
	global $Eresus;
  $result = $Eresus->db->selectItem('plugins', "`name`='".$this->name."'");
	if ($result) $this->settings = decodeOptions($result['settings'], $this->settings);
	return (bool)$result;
}
//------------------------------------------------------------------------------
/**
 * Сохранение настроек плагина в БД
 *
 * @return  bool  Результат выполнения
 */
function saveSettings()
{
	global $Eresus;
  $result = $Eresus->db->updateItem('plugins', $this->__item(), "`name`='".$this->name."'");
	return $result;
}
//------------------------------------------------------------------------------
/**
 * Обновление данных о плагине в БД
 */
function resetPlugin()
{
  $this->loadSettings();
  $this->saveSettings();
}
//------------------------------------------------------------------------------
/**
 * Действия, выполняемые при инсталляции плагина
 */
function install() {}
//------------------------------------------------------------------------------
/**
 * Действия, выполняемые при деинсталляции плагина
 */
function uninstall()
{
	global $Eresus;
	
	$tables = $Eresus->db->query_array("SHOW TABLES LIKE '{$Eresus->db->prefix}{$this->name}_%'");
	for ($i=0; $i < count($tables); $i++)
		$this->dbDropTable(substr(current($tables[$i]), strlen($this->name)+1));
	$this->dbDropTable();
}
//------------------------------------------------------------------------------
/**
 * Действия при изменении настроек
 */
function onSettingsUpdate() {}
//------------------------------------------------------------------------------
/**
 * Сохраняет в БД изменения настроек плагина
 */
function updateSettings()
{
	global $Eresus;

  foreach ($this->settings as $key => $value) if (isset($Eresus->request['arg'][$key])) $this->settings[$key] = $Eresus->request['arg'][$key];
	$this->onSettingsUpdate();
  $this->saveSettings();
}
//------------------------------------------------------------------------------
/**
 * Замена макросов
 *
 * @param  string  $template  Строка в которой требуется провести замену макросов
 * @param  array   $item      Ассоциативный массив со значениями для подстановки вместо макросов
 *
 * @return  string  Метод возвращает строку, в которой заменены все макросы, совпадающие с полями массива item
 */
function replaceMacros($template, $item)
{
  preg_match_all('/\$\(([^(]+)\)/U', $template, $matches);
  if (count($matches[1])) foreach($matches[1] as $macros)
    if (isset($item[$macros])) $template = str_replace('$('.$macros.')', $item[$macros], $template);
  return $template;
}
//------------------------------------------------------------------------------
/**
 * Создание новой директории
 * 
 * @param string $name Имя директории
 * @return bool Результат
 */
function mkdir($name = '')
{
	$result = true;
	$umask = umask(0000);
	# Проверка и создание корневой директории данных
	if (!is_dir($this->dirData)) $result = mkdir($this->dirData);
	if ($result) {
		$name = preg_replace(array('!\.{1,2}/!', '!^/!', '!/$!'), '', $name);
		if ($name) {
			$name = explode('/', $name);
			$root = substr($this->dirData, 0, -1);
			for($i=0; $i<count($name); $i++) if ($name[$i]) {
				$root .= '/'.$name[$i];
				if (!is_dir($root)) $result = mkdir($root);
				if (!$result) break;
			}
		}
	}
	umask($umask);
	return $result;
}
//------------------------------------------------------------------------------
/**
 * Удаление директории и файлов
 * 
 * @param string $name Имя директории
 * @return bool Результат
 */
function rmdir($name = '')
{
	$result = true;
	$name = preg_replace(array('!\.{1,2}/!', '!^/!', '!/$!'), '', $name);
	$name = $this->dirData.$name;
	if (is_dir($name)) {
		$files = glob($name.'/{.*,*}', GLOB_BRACE);
		for ($i = 0; $i < count($files); $i++) {
			if (substr($files[$i], -2) == '/.' || substr($files[$i], -3) == '/..') continue;
			if (is_dir($files[$i])) $result = $this->rmdir(substr($files[$i], strlen($this->dirData)));
			elseif (is_file($files[$i])) $result = filedelete($files[$i]);
			if (!$result) break;
		}
		if ($result) $result = rmdir($name);
	}
	return $result;
}
//------------------------------------------------------------------------------
/**
 * Возвращает реальное имя таблицы
 * 
 * @param string $table  Локальное имя таблицы
 * @return string Реальное имя таблицы
 */
function __table($table)
{
	return $this->name.(empty($table)?'':'_'.$table);
}
//------------------------------------------------------------------------------
/**
 * Создание таблицы в БД
 * 
 * @param string $SQL Описание таблицы
 * @param string $name Имя таблицы
 * 
 * @return bool Результат выполенения
 */
function dbCreateTable($SQL, $name = '')
{
	global $Eresus;
	
	$result = $Eresus->db->create($this->__table($name), $SQL);
	return $result;
}
//------------------------------------------------------------------------------
/**
 * Удаление таблицы БД
 * 
 * @param string $name Имя таблицы
 * 
 * @return bool Результат выполенения
 */
function dbDropTable($name = '')
{
	global $Eresus;
	
	$result = $Eresus->db->drop($this->__table($name));
	return $result;
}
//------------------------------------------------------------------------------
/**
 * Производит выборку из таблицы БД
 * 
 * @param string	$table				Имя таблицы (пустое значение - таблица по умолчанию)
 * @param string	$condition		Условие выборки
 * @param string	$order				Порядок выборки
 * @param bool		$desc					Обратный порядок
 * @param string	$fields				Список полей
 * @param int			$limit				Вернуть не больше полей чем limit
 * @param int			$offset				Смещение выборки
 * @param bool		$distinct			Только уникальные результаты
 * 
 * @return array	Список записей
 */ 
function dbSelect($table = '', $condition = '', $order = '', $desc = false, $fields = '', $limit = 0, $offset = 0, $group = '', $distinct = false)
{
	global $Eresus;
	
	$result = $Eresus->db->select($this->__table($table), $condition, $order, $desc, $fields, $limit, $offset, $group, $distinct);

  return $result;
}
//------------------------------------------------------------------------------
/**
 * Получение записи из БД
 * 
 * @param string $table  Имя таблицы
 * @param mixed  $id   	 Идентификатор элемента
 * @param string $key    Имя ключевого поля
 * 
 * @return array Элемент
 */
function dbItem($table, $id, $key = 'id')
{
	global $Eresus;
	
	$result = $Eresus->db->selectItem($this->__table($table), "`$key` = '$id'");

  return $result;
}
//------------------------------------------------------------------------------
/**
 * Вставка в таблицу БД
 * 
 * @param string $table  Имя таблицы
 * @param array  $item   Вставляемый элемент
 */
function dbInsert($table, $item)
{
	global $Eresus;
	
	$result = $Eresus->db->insert($this->__table($table), $item);
	$result = $Eresus->db->getInsertedId();

  return $result;
}
//------------------------------------------------------------------------------
/**
 * Изменение данных в БД
 * 
 * @param string $table      Имя таблицы
 * @param mixed  $data       Изменяемый эелемент / Изменения
 * @param string $condition  Ключевое поле / Условие для замены
 * 
 * @return bool Результат
 */
function dbUpdate($table, $data, $condition = '')
{
	global $Eresus;
	
	if (is_array($data)) {
		if (empty($condition)) $condition = 'id';
		$result = $Eresus->db->updateItem($this->__table($table), $data, "`$condition` = '{$data[$condition]}'");
	} elseif (is_string($data)) {
		$result = $Eresus->db->update($this->__table($table), $data, $condition);
	}

  return $result;
}
//------------------------------------------------------------------------------
/**
 * Удаление элемента из БД
 * 
 * @param string $table  Имя таблицы
 * @param mixed  $item   Удаляемый элемент / Идентификатор
 * @param string $key    Ключевое поле
 * 
 * @return bool Результат
 */
function dbDelete($table, $item, $key = 'id')
{
	global $Eresus;
	
  $result = $Eresus->db->delete($this->__table($table), "`$key` = '".(is_array($item)? $item[$key] : $item)."'");

  return $result;
}
//------------------------------------------------------------------------------
/**
 * Подсчёт количества записей в БД
 * 
 * @param string $table      Имя таблицы
 * @param string $condition  Условие для включения в подсчёт
 * 
 * @return int Количество записей, удовлетворяющих условию
 */
function dbCount($table, $condition = '')
{
	global $Eresus;
	
	$result = $Eresus->db->count($this->__table($table), $condition);

  return $result;
}
//------------------------------------------------------------------------------
}

/**
* Базовый класс для плагинов, предоставляющих тип контента
*
*
*/
class ContentPlugin extends Plugin {
/**
* Конструктор
*
* Устанавливает плагин в качестве плагина контента и читает локальные настройки
*/
function ContentPlugin()
{
	global $page;

  parent::Plugin();
  if (isset($page)) {
    $page->plugin = $this->name;
    if (count($page->options)) foreach ($page->options as $key=>$value) $this->settings[$key] = $value;
  }
}
//------------------------------------------------------------------------------
/**
* Обновляет контент страницы в БД
* 
* @param  string  $content  Контент
*/
function update($content)
{
	global $Eresus, $page;

  $item = $Eresus->db->selectItem('pages', "`id`='".$page->id."'");
  $item['content'] = $content;
  $Eresus->db->updateItem('pages', $item, "`id`='".$page->id."'");
}
//------------------------------------------------------------------------------
/**
* Обновляет контент страницы
*/
function adminUpdate()
{
	$this->updateContent(arg('content'));
  goto(arg('submitURL'));
}
//------------------------------------------------------------------------------
/**
* Отрисовка клиентской части
*
* @return  string  Контент
*/
function clientRenderContent()
{
	global $page;

  return $page->content;
}
//------------------------------------------------------------------------------
/**
 * Отрисовка административной части
 *
 * @return  string  Контент
 */
function adminRenderContent()
{
	global $page, $Eresus;

  if (arg('action') == 'update') $this->adminUpdate();
	$item = $Eresus->db->selectItem('pages', "`id`='".$page->id."'");
  $form = array(
    'name' => 'content',
    'caption' => $page->title,
    'width' => '100%',
    'fields' => array (
      array ('type'=>'hidden','name'=>'action', 'value' => 'update'),
      array ('type' => 'memo', 'name' => 'content', 'label' => strEdit, 'height' => '30'),
    ),
    'buttons' => array('apply', 'reset'),
  );

  $result = $page->renderForm($form, $item);
  return $result;
}
//------------------------------------------------------------------------------
}
?>