<?php
/**
 * ${product.title}
 *
 * Работа с плагинами
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
 */


/**
 * Работа с плагинами
 *
 * @package Eresus
 */
class Eresus_Extensions_Registry
{
	/**
	 * Список всех активированных плагинов
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
		$items = Eresus_CMS::getLegacyKernel()->db->select('plugins', 'active = 1');
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
					if (!($item['info'] instanceof Eresus_PluginInfo))
					{
						continue;
					}
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

		spl_autoload_register(array($this, 'autoload'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает плагин
	 *
	 * @param string $name  Имя плагина
	 *
	 * @throws DomainException
	 *
	 * @return void
	 */
	public function install($name)
	{
		eresus_log(__METHOD__, LOG_DEBUG, '("%s")', $name);

		$filename = Eresus_CMS::getLegacyKernel()->froot.'ext/'.$name.'.php';
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
			if (class_exists($className, false))
			{
				$this->items[$name] = new $className();
				$this->items[$name]->install();
				$item = $this->items[$name]->__item();
				$item['info'] = serialize($info);
				Eresus_CMS::getLegacyKernel()->db->insert('plugins', $item);
			}
			else
			{
				FatalError(sprintf(errClassNotFound, $className));
			}
		}
		else
		{
			eresus_log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"', $filename,
				$name);
			$msg = I18n::getInstance()->getText('Can not find main file "%s" for plugin "%s"', __CLASS__);
			$msg = sprintf($msg, $filename, $name);
			ErrorMessage($msg);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Исключает плагин из подключенных
	 *
	 * @param string $name  Имя плагина
	 */
	public function uninstall($name)
	{
		if (!isset($this->items[$name]))
		{
			$this->load($name);
		}
		if (isset($this->items[$name]))
		{
			$this->items[$name]->uninstall();
		}
		$item = Eresus_CMS::getLegacyKernel()->db->selectItem('plugins', "`name`='".$name."'");
		if (!is_null($item))
		{
			Eresus_CMS::getLegacyKernel()->db->delete('plugins', "`name`='".$name."'");
		}
		//$filename = filesRoot.'ext/'.$name.'.php';
		//if (file_exists($filename)) unlink($filename);
	}

	/**
	 * Загружает плагин и возвращает его экземпляр
	 *
	 * Метод пытается загрузить плагин с именем $name (если он не был загружен ранее). В случае успеха
	 * создаётся и возвращается экземпляр основного класса плагина (либо экземпляр, созданный ранее).
	 *
	 * @param string $name  Имя плагина
	 *
	 * @return Eresus_Extensions_Plugin|bool  Экземпляр плагина или false
	 *
	 * @since 2.10
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

		/* Если такой плагин не зарегистрирован, возвращаем false */
		if (!isset($this->list[$name]))
		{
			eresus_log(__METHOD__, LOG_DEBUG, 'Plugin "%s" not registered', $name);
			return false;
		}

		// Путь к файлу плагина
		$filename = Eresus_CMS::getLegacyKernel()->froot . 'ext/' . $name . '.php';

		/* Если такого файла нет, возвращаем false */
		if (!file_exists($filename))
		{
			eresus_log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"', $filename,
				$name);
			return false;
		}

		Core::safeInclude($filename);
		$className = $name;

		if (!class_exists($className, false))
		{
			eresus_log(__METHOD__, LOG_ERR, 'Main class %s for plugin "%s" not found in "%s"',
				$className, $name, $filename);
			FatalError(sprintf(errClassNotFound, $name));
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
	 * @return string  Контент
	 */
	function clientRenderContent()
	{
		/* @var TClientUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = '';
		switch ($page->type)
		{

			case 'default':
				$plugin = new Eresus_Extensions_ContentPlugin;
				$result = $plugin->clientRenderContent();
				break;

			case 'list':
				$request = Eresus_CMS::getLegacyKernel()->request;
				/* Если в URL указано что-либо кроме адреса раздела, отправляет ответ 404 */
				if ($request['file'] || $request['query'] || $page->subpage || $page->topic)
				{
					$page->httpError(404);
				}

				$subitems = Eresus_CMS::getLegacyKernel()->db->select('pages', "(`owner`='" .
					$page->id .
					"') AND (`active`='1') AND (`access` >= '" .
					(Eresus_CMS::getLegacyKernel()->user['auth'] ?
						Eresus_CMS::getLegacyKernel()->user['access'] : GUEST)."')", "`position`");
				if (empty($page->content))
				{
					$page->content = '$(items)';
				}
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
							Eresus_CMS::getLegacyKernel()->request['url'] .
								($page->name == 'main' &&
									!$page->owner ? 'main/' : '').$item['name'].'/',
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
						ErrorMessage(sprintf(errMethodNotFound, 'clientRenderContent',
							get_class($this->items[$page->type])));
					}
				}
				else
				{
					ErrorMessage(sprintf(errContentPluginNotFound, $page->type));
				}
		}
		return $result;
	}

	function clientOnStart()
	{
		if (isset($this->events['clientOnStart']))
		{
			foreach ($this->events['clientOnStart'] as $plugin)
			{
				$this->items[$plugin]->clientOnStart();
			}
		}
	}

	public function clientOnURLSplit($item, $url)
	{
		if (isset($this->events['clientOnURLSplit']))
		{
			foreach ($this->events['clientOnURLSplit'] as $plugin)
			{
				$this->items[$plugin]->clientOnURLSplit($item, $url);
			}
		}
	}

	public function clientOnTopicRender($text, $topic = null)
	{
		if (isset($this->events['clientOnTopicRender']))
		{
			foreach ($this->events['clientOnTopicRender'] as $plugin)
			{
				$text = $this->items[$plugin]->clientOnTopicRender($text, $topic);
			}
		}
		return $text;
	}

	public function clientOnContentRender($text)
	{
		if (isset($this->events['clientOnContentRender']))
		{
			foreach ($this->events['clientOnContentRender'] as $plugin)
			{
				$text = $this->items[$plugin]->clientOnContentRender($text);
			}
		}
		return $text;
	}

	public function clientOnPageRender($text)
	{
		if (isset($this->events['clientOnPageRender']))
		{
			foreach ($this->events['clientOnPageRender'] as $plugin)
			{
				$text = $this->items[$plugin]->clientOnPageRender($text);
			}
		}
		return $text;
	}

	public function clientBeforeSend($text)
	{
		if (isset($this->events['clientBeforeSend']))
		{
			foreach ($this->events['clientBeforeSend'] as $plugin)
			{
				$text = $this->items[$plugin]->clientBeforeSend($text);
			}
		}
		return $text;
	}

	public function adminOnMenuRender()
	{
		if (isset($this->events['adminOnMenuRender']))
		{
			foreach ($this->events['adminOnMenuRender'] as $plugin)
			{
				if (method_exists($this->items[$plugin], 'adminOnMenuRender'))
				{
					$this->items[$plugin]->adminOnMenuRender();
				}
				else
				{
					ErrorMessage(sprintf(errMethodNotFound, 'adminOnMenuRender', $plugin));
				}
			}
		}
	}

	/**
	 * Автозагрузка классов плагинов
	 *
	 * @param string $className
	 *
	 * @return boolean
	 *
	 * @since 3.00
	 */
	public function autoload($className)
	{
		$pluginName = strtolower(substr($className, 0, strpos($className, '_')));

		if ($this->load($pluginName))
		{
			$filename = Eresus_Kernel::app()->getFsRoot() . '/ext/' . $pluginName . '/classes/' .
				str_replace('_', '/', substr($className, strlen($pluginName) + 1)) . '.php';
			if (file_exists($filename))
			{
				/** @noinspection PhpIncludeInspection */
				include $filename;
				return Eresus_Kernel::classExists($className);
			}
		}

		return false;
	}
	//-----------------------------------------------------------------------------

}
