<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package EresusCMS
 * @subpackage Domain
 *
 * $Id$
 */

/**
 * Работа с плагинами
 *
 * @package EresusCMS
 * @subpackage Domain
 */
class Plugins
{
	/**
	 * Список всех плагинов
	 * @var array
	 */
	public $list = array();

	/**
	 * Массив плагинов
	 * @var array
	 */
	public $items = array();

	/**
	 * Таблица обработчиков событий
	 * @var array
	 */
	public $events = array();

	/**
	 * Конструктор
	 */
	public function __construct()
	{
		$items = EresusORM::getTable('EresusPlugin')->findAll();
		if (count($items))
		{
			foreach($items as $item)
			{
				$this->list[$item->name] = $item;
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает плагин
	 *
	 * @param string $name  Имя плагина
	 *
	 * @return void
	 *
	 * @throws EresusSourceParseException
	 */
	public function install($name)
	{
		global $Eresus;

		EresusLogger::log(__METHOD__, LOG_DEBUG, '("%s")', $name);

		$filename = Core::app()->getFsRoot() . '/ext/'.$name.'.php';
		if (is_file($filename))
		{
			/*
			 * Подключаем плагин через eval чтобы убедиться в отсутствии фатальных синтаксических
			 * ошибок. Хотя и не факт, что это не сработает.
			 */
			$code = file_get_contents($filename);
			$code = preg_replace('/^\s*<\?php|\?>\s*$/m', '', $code);
			$code = str_replace('__FILE__', "'$filename'", $code);
			@$valid = eval($code) !== false;
			if (!$valid)
			{
				throw new EresusSourceParseException(
					sprintf('Parsing error in file "%s"', $filename),
					sprintf('Plugin "%s" is broken (parse error)', $name)
				);
			}

			$ClassName = $name;
			if (!class_exists($ClassName, false) && class_exists('T'.$ClassName, false))
				$ClassName = 'T'.$ClassName; # FIXME: Обратная совместимость с версиями до 2.10b2
			if (class_exists($ClassName, false))
			{
				$this->items[$name] = new $ClassName();
				$this->items[$name]->install();
				$Eresus->db->insert('plugins', $this->items[$name]->__item());
			}
				else FatalError(sprintf(errClassNotFound, $ClassName));
		}
		else
		{
			EresusLogger::log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"', $filename,
				$name);
			$msg = I18n::getInstance()->getText('Can not find main file "%s" for plugin "%s"', __CLASS__);
			$msg = sprintf($msg, $filename, $name);
			ErrorMessage($msg);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Деинсталлирует плагин
	 *
	 * @param string $name  Имя плагина
	 */
	public function uninstall($name)
	{
		global $Eresus;

		if (!isset($this->items[$name]))
		{
			$this->load($name);
		}
		if (isset($this->items[$name]))
		{
			$this->items[$name]->uninstall();
		}
		$pluginInfo = ORM::getTable('EresusPlugin')->find($name);
		if ($pluginInfo)
		{
			$pluginInfo->delete();
		}
		$filename = Core::app()->getFsRoot() . '/ext/'.$name.'.php';
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#

	/**
	 * Производит предварительную загрузку плагинов
	 *
	 * @return void
	 */
	function preload()
	{
		EresusLogger::log(__METHOD__, LOG_DEBUG, '()');

		if (count($this->list))
		{
			EresusLogger::log(__METHOD__, LOG_DEBUG, 'Preloading plugins...');
			foreach($this->list as $item)
			{
				if ($item['active'])
				{
					$this->load($item['name']);
				}
			}
		}
		else
		{
			EresusLogger::log(__METHOD__, LOG_DEBUG, 'Nothing to preload');
		}
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#

	/**
	 * Загружает плагин и возвращает его экземпляр
	 *
	 * @param string $name  Имя плагина
	 * @return Plugin|TPlugin|false  Экземпляр плагина или FASLE если не удалось загрузить плагин
	 */
	public function load($name)
	{
		EresusLogger::log(__METHOD__, LOG_DEBUG, '("%s")', $name);
		/* Если плагин уже был загружен возвращаем экземпляр из реестра */
		if (isset($this->items[$name]))
		{
			EresusLogger::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" already loaded', $name);
			return $this->items[$name];
		}

		/* Если такой плагин не зарегистрирован, возвращаем FASLE */
		if (!isset($this->list[$name]))
		{
			EresusLogger::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" not registered', $name);
			return false;
		}

		// Путь к файлу плагина
		$filename = Core::app()->getFsRoot() . '/ext/' . $name . '.php';

		/* Если такого файла нет, возвращаем FASLE */
		if (!file_exists($filename))
		{
			EresusLogger::log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"', $filename,
				$name);
			return false;
		}

		include $filename;
		$className = $name;

		/* TODO: Обратная совместимость с версиями до 2.10b2. Отказаться в новых версиях */
		if (!class_exists($className, false) && class_exists('T' . $className))
		{
			$className = 'T' . $className;
		}

		if (!class_exists($className, false))
		{
			EresusLogger::log(__METHOD__, LOG_ERR, 'Main class %s for plugin "%s" not found in "%s"',
				$className, $name, $filename);
			FatalError(sprintf(errClassNotFound, $name));
		}

		// Заносим экземпляр в реестр
		$this->items[$name] = new $className();
		EresusLogger::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" loaded', $name);

		return $this->items[$name];
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовка контента раздела
	 *
	 * @return stirng  Контент
	 */
	function clientRenderContent()
	{
		global $Eresus, $page;

		$result = '';
		switch ($page->type)
		{

			case 'default':
				$plugin = new ContentPlugin;
				$result = $plugin->clientRenderContent();
			break;

			case 'list':
				/* Если в URL указано что-либо кроме адреса раздела, отправляет ответ 404 */
				if ($Eresus->request['file'] || $Eresus->request['query'] || $page->subpage || $page->topic)
					$page->httpError(404);

				$subitems = $Eresus->db->select('pages', "(`owner`='".$page->id."') AND (`active`='1') " .
					"AND (`access` >= '".($_SESSION['user_auth'] ? $Eresus->user['access'] : GUEST)."')", "`position`");
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
							$Eresus->request['url'].($page->name == 'main' && !$page->owner ? 'main/' : '').$item['name'].'/',
						),
						$template['html']
					);
					$result = str_replace('$(items)', $items, $page->content);
				}
			break;
			case 'url':
				HttpResponse::redirect($page->replaceMacros($page->content));
			break;
			default:
			if ($this->load($page->type)) {
				if (method_exists($this->items[$page->type], 'clientRenderContent'))
					$result = $this->items[$page->type]->clientRenderContent();
				else ErrorMessage(sprintf(errMethodNotFound, 'clientRenderContent', get_class($this->items[$page->type])));
			} else ErrorMessage(sprintf(errContentPluginNotFound, $page->type));
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
		if (isset($this->events['adminOnMenuRender'])) foreach($this->events['adminOnMenuRender'] as $plugin)
			if (method_exists($this->items[$plugin], 'adminOnMenuRender')) $this->items[$plugin]->adminOnMenuRender();
			else ErrorMessage(sprintf(errMethodNotFound, 'adminOnMenuRender', $plugin));
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
 /**
	* Событие ajaxOnRequest
	*/
	function ajaxOnRequest()
	{
		if (isset($this->events['ajaxOnRequest']))
			foreach($this->events['ajaxOnRequest'] as $plugin)
				$this->items[$plugin]->ajaxOnRequest();
	}
	//-----------------------------------------------------------------------------
}
