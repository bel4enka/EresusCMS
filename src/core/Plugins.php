<?php
/**
 * ${product.title}
 *
 * Интерфейс к модулям расширения
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
 * @package Eresus
 *
 * $Id$
 */

/**
* Работа с плагинами
*
* @package Eresus
*/
class Eresus_Plugins
{
	/**
	 * Список всех плагинов
	 *
	 * @var array
	 * @todo сделать private
	 */
	public $list = array();

	/**
	 * Массив плагинов
	 *
	 * @var array
	 * @todo сделать private
	 */
	public $items = array();

	/**
	 * Таблица обработчиков событий
	 *
	 * @var array
	 * @todo сделать private
	 */
	public $events = array();

	/**
	 * Загружает активные плагины
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function init()
	{
		$items = $GLOBALS['Eresus']->db->select('plugins', 'active = 1');
		if ($items)
		{
			foreach ($items as &$item)
			{
				$item['info'] = unserialize($item['info']);
				$this->list[$item['name']] = $item;
			}

			/* Проверяем зависимости */
			do
			{
				$success = true;
				foreach ($this->list as $plugin => $item)
				{
					foreach ($item['info']->getRequiredPlugins() as $required)
					{
						list ($name, $minVer, $maxVer) = $required;
						if (
						!isset($this->list[$name]) ||
						($minVer && version_compare($this->list[$name]['info']->version, $minVer, '<')) ||
						($maxVer && version_compare($this->list[$name]['info']->version, $maxVer, '>'))
						)
						{
							$msg = 'Plugin "%s" requires plugin %s';
							$requiredPlugin = $name . ' ' . $minVer . '-' . $maxVer;
							eresus_log(__CLASS__, LOG_ERR, $msg, $plugin, $requiredPlugin);
							/*$msg = I18n::getInstance()->getText($msg, $this);
							 ErrorMessage(sprintf($msg, $plugin, $requiredPlugin));*/
							unset($this->list[$plugin]);
							$success = false;
						}
					}
				}
			}
			while (!$success);

			/* Загружаем плагины */
			foreach ($this->list as $item)
			{
				$this->load($item['name']);
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

		eresus_log(__METHOD__, LOG_DEBUG, '("%s")', $name);

		$filename = filesRoot.'ext/'.$name.'.php';
		if (FS::exists($filename))
		{
			$info = Eresus_PluginInfo::loadFromFile($filename);
			/*
			 * Подключаем плагин через eval чтобы убедиться в отсутствии фатальных синтаксических
			* ошибок. Хотя и не факт, что это сработает.
			*/
			$code = file_get_contents($filename);
			$code = preg_replace('/^\s*<\?php|\?>\s*$/m', '', $code);
			$code = str_replace('__FILE__', "'$filename'", $code);
			ini_set('track_errors', true);
			$valid = eval($code) !== false;
			ini_set('track_errors', false);
			if (!$valid)
			{
				throw new DomainException(
				sprintf('Plugin "%s" is broken: %s', $name, $php_errormsg)
				);
			}

			$className = $name;
			if (!class_exists($className, false) && class_exists('T' . $className, false))
			{
				$className = 'T' . $className; // FIXME: Обратная совместимость с версиями до 2.10b2
			}

			if (class_exists($className, false))
			{
				$this->items[$name] = new $className();
				$this->items[$name]->install();
				$item = $this->items[$name]->__item();
				$item['info'] = serialize($info);
				$Eresus->db->insert('plugins', $item);
			}
			else
			{
				FatalError(sprintf(i18n('Класс "%s" не найден.'), $className));
			}
		}
		else
		{
			eresus_log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"', $filename,
			$name);
			$msg = i18n('Не удалось найти основной файл "%s" для модуля расширения "%s".', __CLASS__);
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
		$item = $Eresus->db->selectItem('plugins', "`name`='".$name."'");
		if (!is_null($item))
		{
			$Eresus->db->delete('plugins', "`name`='".$name."'");
		}
		$filename = filesRoot.'ext/'.$name.'.php';
		#if (file_exists($filename)) unlink($filename);
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#

	/**
	 * Загружает плагин и возвращает его экземпляр
	*
	* @param string $name  Имя плагина
	*
	* @return Plugin|TPlugin|false  Экземпляр плагина или FASLE если не удалось загрузить плагин
	*/
	public function load($name)
	{
		eresus_log(__METHOD__, LOG_DEBUG, '("%s")', $name);
		/* Если плагин уже был загружен возвращаем экземпляр из реестра */
		if (isset($this->items[$name]))
		{
			eresus_log(__METHOD__, LOG_DEBUG, 'Plugin "%s" already loaded', $name);
			return $this->items[$name];
		}

		/* Если такой плагин не зарегистрирован, возвращаем FASLE */
		if (!isset($this->list[$name]))
		{
			eresus_log(__METHOD__, LOG_DEBUG, 'Plugin "%s" not registered', $name);
			return false;
		}

		// Путь к файлу плагина
		$filename = filesRoot . 'ext/' . $name . '.php';

		/* Если такого файла нет, возвращаем FASLE */
		if (!file_exists($filename))
		{
			eresus_log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"', $filename,
			$name);
			return false;
		}

		Core::safeInclude($filename);
		$className = $name;

		/* TODO: Обратная совместимость с версиями до 2.10b2. Отказаться в новых версиях */
		if (!class_exists($className, false) && class_exists('T' . $className))
		{
			$className = 'T' . $className;
		}

		if (!class_exists($className, false))
		{
			eresus_log(__METHOD__, LOG_ERR, 'Main class %s for plugin "%s" not found in "%s"',
			$className, $name, $filename);
			FatalError(sprintf(i18n('Класс "%s" не найден.'), $name));
		}

		// Заносим экземпляр в реестр
		$this->items[$name] = new $className();
		eresus_log(__METHOD__, LOG_DEBUG, 'Plugin "%s" loaded', $name);

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

				$subitems = $Eresus->db->select('pages', "(`owner`='".$page->id."') AND (`active`='1') AND (`access` >= '".($Eresus->user['auth'] ? $Eresus->user['access'] : GUEST)."')", "`position`");
				if (empty($page->content)) $page->content = '$(items)';
				useLib('templates');
				$templates = new Templates();
				$template = $templates->get('SectionListItem', 'std');
				if (false === $template)
				{
					$template = '<h1><a href="$(link)" title="$(hint)">$(caption)</a></h1>$(description)';
				}
				$items = '';
				foreach ($subitems as $item)
				{
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
					$template
					);
				}
				$result = str_replace('$(items)', $items, $page->content);
				break;

			case 'url':
				HTTP::redirect($page->replaceMacros($page->content));
				break;

			default:
				if ($this->load($page->type))
			{
				if (method_exists($this->items[$page->type], 'clientRenderContent'))
				{
					$result = $this->items[$page->type]->clientRenderContent();
				}
				else
				{
					ErrorMessage(sprintf(i18n('Метод "%s" не найден в классе "%s".'), 'clientRenderContent',
					get_class($this->items[$page->type])));
				}
			}
			else
			{
				ErrorMessage(sprintf(i18n('Не найдено модуля поддержки типа контента "%s"'), $page->type));
			}
			break;
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
	function clientOnTopicRender($text, $topic = null)
	{
		global $page;
		if (isset($this->events['clientOnTopicRender'])) foreach($this->events['clientOnTopicRender'] as $plugin) $text = $this->items[$plugin]->clientOnTopicRender($text, $topic);
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
	//-----------------------------------------------------------------------------

	function adminOnMenuRender()
	{
		if (isset($this->events['adminOnMenuRender']))
		{
			foreach($this->events['adminOnMenuRender'] as $plugin)
			{
				if (method_exists($this->items[$plugin], 'adminOnMenuRender'))
				{
					$this->items[$plugin]->adminOnMenuRender();
				}
				else
				{
					ErrorMessage(sprintf(i18n('Метод "%s" не найден в классе "%s".'), 'adminOnMenuRender',
					$plugin));
				}
			}
		}
	}
	//-----------------------------------------------------------------------------

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
