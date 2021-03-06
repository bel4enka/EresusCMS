<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * Плагин повреждён
 *
 * Обычно это означает синтаксическую ошибку в файле плагина.
 *
 * @package Eresus
 */
class EresusSourceParseException extends EresusRuntimeException {};


/**
 * Работа с плагинами
 *
 * @package Eresus
 */
class Plugins
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
        spl_autoload_register(array($this, 'autoload'));

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
	 * Деинсталлирует плагин
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
	 * @return Plugin|TPlugin|bool  Экземпляр плагина или false если не удалось загрузить плагин
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
				$plugin = new ContentPlugin;
				$result = $plugin->clientRenderContent();
			break;

			case 'list':
				/* Если в URL указано что-либо кроме адреса раздела, отправляет ответ 404 */
				if (Eresus_CMS::getLegacyKernel()->request['file'] ||
					Eresus_CMS::getLegacyKernel()->request['query'] ||
					$page->subpage ||
					$page->topic)
				{
					Eresus_Kernel::app()->getPage()->httpError(404);
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
		if (isset($this->events['clientOnStart'])) foreach($this->events['clientOnStart'] as $plugin) $this->items[$plugin]->clientOnStart();
	}

	function clientOnURLSplit($item, $url)
	{
		if (isset($this->events['clientOnURLSplit'])) foreach($this->events['clientOnURLSplit'] as $plugin) $this->items[$plugin]->clientOnURLSplit($item, $url);
	}

	function clientOnTopicRender($text, $topic = null)
	{
		if (isset($this->events['clientOnTopicRender'])) foreach($this->events['clientOnTopicRender'] as $plugin) $text = $this->items[$plugin]->clientOnTopicRender($text, $topic);
		return $text;
	}

	function clientOnContentRender($text)
	{
		if (isset($this->events['clientOnContentRender']))
			foreach($this->events['clientOnContentRender'] as $plugin) $text = $this->items[$plugin]->clientOnContentRender($text);
		return $text;
	}

	function clientOnPageRender($text)
	{
		if (isset($this->events['clientOnPageRender']))
			foreach($this->events['clientOnPageRender'] as $plugin) $text = $this->items[$plugin]->clientOnPageRender($text);
		return $text;
	}

	function clientBeforeSend($text)
	{
		if (isset($this->events['clientBeforeSend']))
			foreach($this->events['clientBeforeSend'] as $plugin) $text = $this->items[$plugin]->clientBeforeSend($text);
		return $text;
	}

	/* function clientOnFormControlRender($formName, $control, $text)
	{
		if (isset($this->events['clientOnFormControlRender'])) foreach($this->events['clientOnFormControlRender'] as $plugin) $text = $this->items[$plugin]->clientOnFormControlRender($formName, $control, $text);
		return $text;
	}*/

	function adminOnMenuRender()
	{
		if (isset($this->events['adminOnMenuRender'])) foreach($this->events['adminOnMenuRender'] as $plugin)
			if (method_exists($this->items[$plugin], 'adminOnMenuRender')) $this->items[$plugin]->adminOnMenuRender();
			else ErrorMessage(sprintf(errMethodNotFound, 'adminOnMenuRender', $plugin));
	}

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
				include $filename;
				return Eresus_Kernel::classExists($className);
			}
		}

		return false;
	}
	//-----------------------------------------------------------------------------

}

/* * * * * * * * * * * * * * * * * * * * * * * *
*
*     Классы-предки для создания плагинов
*
* * * * * * * * * * * * * * * * * * * * * * * */

/**
 * Родительский класс для всех плагинов
 *
 * @package Eresus
 */
class Plugin
{
	/**
	 * Имя плагина
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Версия плагина
	 *
	 * Потомки должны перекрывать это своим значением
	 *
	 * @var string
	 */
	public $version = '0.00';

	/**
	 * Необходимая версия Eresus
	 *
	 * Потомки могут перекрывать это своим значением
	 *
	 * @var string
	 */
	public $kernel = '2.10b2';

	/**
	 * Название плагина
	 *
	 * Потомки должны перекрывать это своим значением
	 *
	 * @var string
	 */
	public $title = 'no title';

	/**
	 * Описание плагина
	 *
	 * Потомки должны перекрывать это своим значением
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Настройки плагина
	 *
	 * Потомки могут перекрывать это своим значением
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * Директория данных
	 *
	 * /data/имя_плагина
	 *
	 * @var string
	 */
	protected $dirData;

	/**
	 * URL данных
	 *
	 * @var string
	 */
	protected $urlData;

	/**
	 * Директория скриптов
	 *
	 * /ext/имя_плагина
	 *
	 * @var string
	 */
	protected $dirCode;

	/**
	 * URL скриптов
	 *
	 * @var string
	 */
	protected $urlCode;

	/**
	 * Директория оформления
	 *
	 * style/имя_плагина
	 *
	 * @var string
	 */
	protected $dirStyle;

	/**
	 * URL оформления
	 *
	 * @var string
	 */
	protected $urlStyle;

	/**
	 * Конструктор
	 *
	 * Производит чтение настроек плагина и подключение языковых файлов
	 *
	 * @uses $locale
	 * @uses FS::isFile
	 * @uses Core::safeInclude
	 * @uses Plugin::resetPlugin
	 */
	public function __construct()
	{
		global $locale;

		$this->name = strtolower(get_class($this));
		if (!empty($this->name) && isset(Eresus_CMS::getLegacyKernel()->plugins->list[$this->name]))
		{
			$this->settings =
				decodeOptions(Eresus_CMS::getLegacyKernel()->plugins->list[$this->name]['settings'],
				$this->settings);
			# Если установлена версия плагина отличная от установленной ранее
			# то необходимо произвести обновление информации о плагине в БД
			if ($this->version != Eresus_CMS::getLegacyKernel()->plugins->list[$this->name]['version'])
				$this->resetPlugin();
		}
		$this->dirData = Eresus_CMS::getLegacyKernel()->fdata . $this->name . '/';
		$this->urlData = Eresus_CMS::getLegacyKernel()->data . $this->name . '/';
		$this->dirCode = Eresus_CMS::getLegacyKernel()->froot . 'ext/' . $this->name . '/';
		$this->urlCode = Eresus_CMS::getLegacyKernel()->root . 'ext/' . $this->name . '/';
		$this->dirStyle = Eresus_CMS::getLegacyKernel()->fstyle . $this->name . '/';
		$this->urlStyle = Eresus_CMS::getLegacyKernel()->style . $this->name . '/';
		$filename = filesRoot . 'lang/' . $this->name . '/' . $locale['lang'] . '.php';
		if (FS::isFile($filename))
		{
			Core::safeInclude($filename);
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * Возвращает информацию о плагине
	 *
	 * @param  array  $item  Предыдущая версия информации (по умолчанию null)
	 *
	 * @return  array  Массив информации, пригодный для записи в БД
	 */
	public function __item($item = null)
	{
		$result['name'] = $this->name;
		$result['content'] = false;
		$result['active'] = is_null($item)? true : $item['active'];
		$result['settings'] = Eresus_CMS::getLegacyKernel()->db->
			escape(is_null($item) ? encodeOptions($this->settings) : $item['settings']);
		$result['title'] = $this->title;
		$result['version'] = $this->version;
		$result['description'] = $this->description;
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Перехватчик обращений к несуществующим методам плагинов
	 *
	 * @param string $method  Имя вызванного метода
	 * @param array  $args    Переданные аргументы
	 *
	 * @throws EresusMethodNotExistsException
	 */
	public function __call($method, $args)
	{
		throw new EresusMethodNotExistsException($method, get_class($this));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает URL директории данных плагина
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getDataURL()
	{
		return $this->urlData;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает URL директории файлов плагина
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getCodeURL()
	{
		return $this->urlCode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает URL директории стилей плагина
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getStyleURL()
	{
		return $this->urlStyle;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Чтение настроек плагина из БД
	 *
	 * @return bool  Результат выполнения
	 */
	protected function loadSettings()
	{
		$result = Eresus_CMS::getLegacyKernel()->db->
			selectItem('plugins', "`name`='".$this->name."'");
		if ($result)
		{
			$this->settings = decodeOptions($result['settings'], $this->settings);
		}
		return (bool) $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Сохранение настроек плагина в БД
	 *
	 * @return bool  Результат выполнения
	 */
	protected function saveSettings()
	{
		$result = Eresus_CMS::getLegacyKernel()->db->selectItem('plugins', "`name`='{$this->name}'");
		$result = $this->__item($result);
		$result['settings'] = Eresus_CMS::getLegacyKernel()->db->escape(encodeOptions($this->settings));
		$result = Eresus_CMS::getLegacyKernel()->db->
			updateItem('plugins', $result, "`name`='".$this->name."'");

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Обновление данных о плагине в БД
	 */
	protected function resetPlugin()
	{
		$this->loadSettings();
		$this->saveSettings();
	}

	/**
	 * Действия, выполняемые при инсталляции плагина
	 */
	public function install()
	{

	}

	/**
	 * Действия, выполняемые при деинсталляции плагина
	 */
	public function uninstall()
	{
		# TODO: Перенести в IDataSource
		$eresus = Eresus_CMS::getLegacyKernel();
		$tables = $eresus->db->query_array("SHOW TABLES LIKE '{$eresus->db->prefix}{$this->name}_%'");
		$tables = array_merge($tables, $eresus->db->
				query_array("SHOW TABLES LIKE '{$eresus->db->prefix}{$this->name}'"));
		for ($i=0; $i < count($tables); $i++)
		{
			$this->dbDropTable(substr(current($tables[$i]), strlen($this->name)+1));
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * Действия при изменении настроек
	 */
	public function onSettingsUpdate()
	{
	}
	//------------------------------------------------------------------------------

	/**
	 * Сохраняет в БД изменения настроек плагина
	 */
	public function updateSettings()
	{
		foreach ($this->settings as $key => $value)
		{
			if (!is_null(arg($key)))
			{
				$this->settings[$key] = arg($key);
			}
		}
		$this->onSettingsUpdate();
		$this->saveSettings();
	}
	//------------------------------------------------------------------------------

	/**
	 * Замена макросов
	 *
	 * @param  string  $template  Строка в которой требуется провести замену макросов
	 * @param  mixed   $item      Ассоциативный массив со значениями для подстановки вместо макросов
	 *
	 * @return  string  Обработанная строка
	 */
	protected function replaceMacros($template, $item)
	{
		$result = replaceMacros($template, $item);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Создание новой директории
	 *
	 * @param string $name Имя директории
	 * @return bool Результат
	 */
	protected function mkdir($name = '')
	{
		$result = true;
		$umask = umask(0000);
		# Проверка и создание корневой директории данных
		if (!is_dir($this->dirData)) $result = mkdir($this->dirData);
		if ($result) {
			# Удаляем директории вида "." и "..", а также финальный и лидирующий слэши
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
	protected function rmdir($name = '')
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
	protected function __table($table)
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
	protected function dbCreateTable($SQL, $name = '')
	{
		$result = Eresus_CMS::getLegacyKernel()->db->create($this->__table($name), $SQL);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Удаление таблицы БД
	 *
	 * @param string $name Имя таблицы
	 *
	 * @return bool Результат выполнения
	 */
	protected function dbDropTable($name = '')
	{
		$result = Eresus_CMS::getLegacyKernel()->db->drop($this->__table($name));
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Производит выборку из таблицы БД
	 *
	 * @param string	$table				Имя таблицы (пустое значение - таблица по умолчанию)
	 * @param string	$condition		Условие выборки
	 * @param string	$order				Порядок выборки
	 * @param string	$fields				Список полей
	 * @param int			$limit				Вернуть не больше полей чем limit
	 * @param int			$offset				Смещение выборки
	 * @param bool		$distinct			Только уникальные результаты
	 *
	 * @return array|bool  Выбранные элементы в виде массива или FALSE в случае ошибки
	 */
	public function dbSelect($table = '', $condition = '', $order = '', $fields = '', $limit = 0,
		$offset = 0, $group = '', $distinct = false)
	{
		$result = Eresus_CMS::getLegacyKernel()->db->select($this->__table($table), $condition, $order,
			$fields, $limit, $offset, $group, $distinct);

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
	public function dbItem($table, $id, $key = 'id')
	{
		$result = Eresus_CMS::getLegacyKernel()->db->selectItem($this->__table($table),
			"`$key` = '$id'");

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Вставка в таблицу БД
	 *
	 * @param string $table          Имя таблицы
	 * @param array  $item           Вставляемый элемент
	 * @param string $key[optional]  Имя ключевого поля. По умолчанию "id"
	 */
	public function dbInsert($table, $item, $key = 'id')
	{
		Eresus_CMS::getLegacyKernel()->db->insert($this->__table($table), $item);
		$result = $this->dbItem($table, Eresus_CMS::getLegacyKernel()->db->getInsertedId(), $key);

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
	public function dbUpdate($table, $data, $condition = '')
	{
		if (is_array($data))
		{
			if (empty($condition)) $condition = 'id';
			$result = Eresus_CMS::getLegacyKernel()->db->
				updateItem($this->__table($table), $data, "`$condition` = '{$data[$condition]}'");
		}
		elseif (is_string($data))
		{
			$result = Eresus_CMS::getLegacyKernel()->db->
				update($this->__table($table), $data, $condition);
		}
		else
		{
			$result = false;
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
	public function dbDelete($table, $item, $key = 'id')
	{
		$result = Eresus_CMS::getLegacyKernel()->db->
			delete($this->__table($table), "`$key` = '".(is_array($item)? $item[$key] : $item)."'");

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
	public function dbCount($table, $condition = '')
	{
		$result = Eresus_CMS::getLegacyKernel()->db->count($this->__table($table), $condition);

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Получение информации о таблицах
	 *
	 * @param string $table  Маска имени таблицы
	 * @param string $param  Вернуть только указанный параметр
	 *
	 * @return mixed
	 */
	public function dbTable($table, $param = '')
	{
		$result = Eresus_CMS::getLegacyKernel()->db->tableStatus($this->__table($table), $param);

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Регистрация обработчиков событий
	 *
	 * @param string $event1  Имя события1
	 * ...
	 * @param string $eventN  Имя событияN
	 */
	protected function listenEvents()
	{
		for ($i=0; $i < func_num_args(); $i++)
		{
			Eresus_CMS::getLegacyKernel()->plugins->events[func_get_arg($i)][] = $this->name;
		}
	}
	//------------------------------------------------------------------------------
}

/**
* Базовый класс для плагинов, предоставляющих тип контента
*
* @package Eresus
*/
class ContentPlugin extends Plugin
{
	/**
	 * Конструктор
	 *
	 * Устанавливает плагин в качестве плагина контента и читает локальные настройки
	 */
	public function __construct()
	{
		parent::__construct();

		/* @var TClientUI $page */
		$page = Eresus_Kernel::app()->getPage();
		if ($page instanceof TClientUI)
		{
			$page->plugin = $this->name;
			if (isset($page->options) && count($page->options))
			{
				foreach ($page->options as $key=>$value)
				{
					$this->settings[$key] = $value;
				}
			}
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * Возвращает информацию о плагине
	 *
	 * @param  array  $item  Предыдущая версия информации (по умолчанию null)
	 *
	 * @return  array  Массив информации, пригодный для записи в БД
	 */
	public function __item($item = null)
	{
		$result = parent::__item($item);
		$result['content'] = true;
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Действия при удалении раздела данного типа
	 * @param int     $id     Идентификатор удаляемого раздела
	 * @param string  $table  Имя таблицы
	 */
	public function onSectionDelete($id, $table = '')
	{
		if (count($this->dbTable($table)))
			$this->dbDelete($table, $id, 'section');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обновляет контент страницы в БД
	 *
	 * @param  string  $content  Контент
	 */
	public function updateContent($content)
	{
		$item = Eresus_CMS::getLegacyKernel()->db->selectItem('pages', "`id`='".Eresus_Kernel::app()->getPage()->id."'");
		$item['content'] = $content;
		Eresus_CMS::getLegacyKernel()->db->updateItem('pages', $item, "`id`='".Eresus_Kernel::app()->getPage()->id."'");
	}
	//------------------------------------------------------------------------------

	/**
	* Обновляет контент страницы
	*/
	function adminUpdate()
	{
		$this->updateContent(arg('content', 'dbsafe'));
		HTTP::redirect(arg('submitURL'));
	}

	/**
	 * Отрисовка клиентской части
	 *
	 * @return  string  Контент
	 */
	public function clientRenderContent()
	{
		/* Если в URL указано что-либо кроме адреса раздела, отправляет ответ 404 */
		if (Eresus_CMS::getLegacyKernel()->request['file'] ||
			Eresus_CMS::getLegacyKernel()->request['query'] ||
			Eresus_Kernel::app()->getPage()->subpage || Eresus_Kernel::app()->getPage()->topic)
		{
			Eresus_Kernel::app()->getPage()->httpError(404);
		}

		return Eresus_Kernel::app()->getPage()->content;
	}

	/**
	 * Отрисовка административной части
	 *
	 * @return  string  Контент
	 */
	public function adminRenderContent()
	{
		if (arg('action') == 'update') $this->adminUpdate();
		$item = Eresus_CMS::getLegacyKernel()->db->selectItem('pages', "`id`='".
			Eresus_Kernel::app()->getPage()->id."'");
		$form = array(
			'name' => 'editForm',
			'caption' => Eresus_Kernel::app()->getPage()->title,
			'width' => '100%',
			'fields' => array (
				array ('type'=>'hidden','name'=>'action', 'value' => 'update'),
				array ('type' => 'memo', 'name' => 'content', 'label' => strEdit, 'height' => '30'),
			),
			'buttons' => array('apply', 'reset'),
		);

		$result = Eresus_Kernel::app()->getPage()->renderForm($form, $item);
		return $result;
	}
	//------------------------------------------------------------------------------
}

/**
 * Базовый класс коннектора сторонних расширений
 *
 * @package Eresus
 */
class EresusExtensionConnector
{
	/**
	 * Корневой URL расширения
	 *
	 * @var string
	 */
	protected $root;

	/**
	 * Корневой путь расширения
	 *
	 * @var string
	 */
	protected $froot;

	/**
	 * Конструктор
	 *
	 * @return EresusExtensionConnector
	 */
	function __construct()
	{
		$name = strtolower(substr(get_class($this), 0, -9));
		$this->root = Eresus_CMS::getLegacyKernel()->root.'ext-3rd/'.$name.'/';
		$this->froot = Eresus_CMS::getLegacyKernel()->froot.'ext-3rd/'.$name.'/';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Заменяет глобальные макросы
	 *
	 * @param string $text
	 * @return string
	 */
	protected function replaceMacros($text)
	{
		$text = str_replace(
			array(
				'$(httpHost)',
				'$(httpPath)',
				'$(httpRoot)',
				'$(styleRoot)',
				'$(dataRoot)',
			),
			array(
				Eresus_CMS::getLegacyKernel()->host,
				Eresus_CMS::getLegacyKernel()->path,
				Eresus_CMS::getLegacyKernel()->root,
				Eresus_CMS::getLegacyKernel()->style,
				Eresus_CMS::getLegacyKernel()->data
			),
			$text
		);

		return $text;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Метод вызывается при проксировании прямых запросов к расширению
	 *
	 */
	function proxy()
	{
		if (!UserRights(EDITOR))
			die;

		$filename = Eresus_CMS::getLegacyKernel()->request['path'] .
			Eresus_CMS::getLegacyKernel()->request['file'];
		$filename = Eresus_CMS::getLegacyKernel()->froot . substr($filename,
			strlen(Eresus_CMS::getLegacyKernel()->root));

		if (FS::isDir($filename))
		{
			$filename = FS::normalize($filename . '/index.php');
		}

		if (!FS::isFile($filename))
		{
			header('Not found', true, 404);
			die('<h1>Not found.</h1>');
		}

		$ext = strtolower(substr($filename, strrpos($filename, '.') + 1));

		switch (true)
		{
			case in_array($ext, array('png', 'jpg', 'jpeg', 'gif')):
				$info = getimagesize($filename);
				header('Content-type: '.$info['mime']);
				echo file_get_contents($filename);
			break;

			case $ext == 'js':
				header('Content-type: text/javascript');
				$s = file_get_contents($filename);
				$s = $this->replaceMacros($s);
				echo $s;
			break;

			case $ext == 'css':
				header('Content-type: text/css');
				$s = file_get_contents($filename);
				$s = $this->replaceMacros($s);
				echo $s;
			break;

			case $ext == 'html':
			case $ext == 'htm':
				header('Content-type: text/html');
				$s = file_get_contents($filename);
				$s = $this->replaceMacros($s);
				echo $s;
			break;

			case $ext == 'php':
				Eresus_CMS::getLegacyKernel()->conf['debug']['enable'] = false;
				restore_error_handler();
				chdir(dirname($filename));
				require $filename;
			break;
		}
	}
	//-----------------------------------------------------------------------------
}



/**
 * Класс для работы с расширениями системы
 *
 * @package Eresus
 */
class EresusExtensions
{
 /**
	* Загруженные расширения
	*
	* @var array
	*/
	var $items = array();
 /**
	* Определение имени расширения
	*
	* @param string $class     Класс расширения
	* @param string $function  Расширяемая функция
	* @param string $name      Имя расширения
	*
	* @return mixed  Имя расширения или false если подходящего расширения не найдено
	*/
	function get_name($class, $function, $name = null)
	{
		$result = false;
		if (isset(Eresus_CMS::getLegacyKernel()->conf['extensions']))
		{
			if (isset(Eresus_CMS::getLegacyKernel()->conf['extensions'][$class]))
			{
				if (isset(Eresus_CMS::getLegacyKernel()->conf['extensions'][$class][$function]))
				{
					$items = Eresus_CMS::getLegacyKernel()->conf['extensions'][$class][$function];
					reset($items);
					$result = isset($items[$name]) ? $name : key($items);
				}
			}
		}

		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* Загрузка расширения
	*
	* @param string $class     Класс расширения
	* @param string $function  Расширяемая функция
	* @param string $name      Имя расширения
	*
	* @return mixed  Экземпляр класса EresusExtensionConnector или false если не удалось загрузить расширение
	*/
	function load($class, $function, $name = null)
	{
		$result = false;
		$name = $this->get_name($class, $function, $name);

		if (isset($this->items[$name]))
		{
			$result = $this->items[$name];
		}
			else
		{
			$filename = Eresus_CMS::getLegacyKernel()->froot.'ext-3rd/' . $name .
				'/eresus-connector.php';
			if (is_file($filename))
			{
				include_once $filename;
				$class = $name.'Connector';
				if (class_exists($class))
				{
					$this->items[$name] = new $class();
					$result = $this->items[$name];
				}
			}
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
}
