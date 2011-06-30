<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Главный модуль
 *
 * @copyright 2004, Eresus Project, http://eresus.ru/
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
 * @package CMS
 *
 * $Id$
 */



/**
 * Интерфейс коннектора файлового менеджера
 *
 * @package CoreExtensionsAPI
 * @since 2.16
 */
interface FileManagerConnectorInterface
{
	/**
	 * Метод должен возвращать разметку для файлового менеджера директории data.
	 *
	 * @return string HTML
	 *
	 * @since 2.16
	 */
	public function getDataBrowser();
	//-----------------------------------------------------------------------------
}



/**
 * Класс приложения Eresus CMS
 *
 * @package CMS
 */
class Eresus_CMS
{
	/**
	 * Версия CMS
	 *
	 * @var string
	 */
	private $version = '${product.version}';

	/**
	 * Контейнер оснонвых объектов CMS
	 *
	 * @var array
	 * @see get()
	 * @since 2.16
	 */
	private $container = array();

	/**
	 * Корневая директория приложения
	 *
	 * Устанавливается в {@link initFS()}. Используйте {@link getRootDir()} для получения значения.
	 *
	 * @var string
	 * @see getRootDir(), initFS()
	 */
	private $rootDir;

	/**
	 * Возвращает объект CMS
	 *
	 * @param string $name
	 *
	 * @throws LogicException если нет объекта с запрошенным именем
	 *
	 * @return object
	 *
	 * @since 2.16
	 * @see $container
	 */
	public function get($name)
	{
		if (isset($this->container[$name]))
		{
			return $this->container[$name];
		}
		throw new LogicException('CMS continer has no object "' . $name . '"');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Основной метод приложения
	 *
	 * @return int  Код завершения для консольных вызовов
	 *
	 * @uses Eresus_Logger::exception()
	 * @uses initFS()
	 * @uses checkEnviroment()
	 * @uses createFileStructure()
	 * @uses initConf()
	 * @uses initLocale()
	 * @uses initDB()
	 * @uses initSite()
	 * @uses fatalError()
	 * @uses Eresus_CMS_Mode_CLI
	 */
	public function main()
	{
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, '()');

		try
		{
			$this->initFS();
			$this->createFileStructure();
			$this->initConf();
			$this->initRequest();
			$this->initLocale();
			$this->initDB();
			$this->initSite();
			$this->initSession();
			$this->initTemplateEngine();

			if (substr($this->get('request')->getBasePath(), 0, 6) == '/admin')
			{
				$this->container['ui'] = new Eresus_CMS_UI_Admin();
			}
			else
			{
				$this->container['ui'] = new Eresus_CMS_UI_Client();
			}

			$response = $mode->process();
			$response->send();

			// FIXME Сделать вывод зависимым от режима
			if (Eresus_Config::get('eresus.cms.debug'))
			{
				$memory = number_format(memory_get_peak_usage(true) / 1024, 0, ',', ' ');
				echo "\n<!-- Memory: $memory MiB -->";
				if (!Eresus_Kernel::isWindows())
				{
					$ru = getrusage();
					echo sprintf("\n<!-- utime: %d.%06d sec -->", $ru['ru_utime.tv_sec'],
						$ru['ru_utime.tv_usec']);
				}
			}

		}
		catch (SuccessException $e)
		{
			return 0;
		}
		catch (Exception $e)
		{
			Eresus_Logger::exception($e);
			ob_end_clean();
			include dirname(__FILE__) . '/fatal.html.php';
			return -1;
		}
		return 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает версию приложения
	 *
	 * @return string
	 * @uses $version
	 */
	public function getVersion()
	{
		return $this->version;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает корневую директорию приложения
	 *
	 * @return string
	 * @uses $rootDir
	 */
	public function getRootDir()
	{
		return $this->rootDir;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает путь к директории данных сайта (без финального слеша)
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getDataDir()
	{
		return $this->getRootDir() . '/data';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Создание файловой структуры
	 *
	 * @return void
	 *
	 * @uses getRootDir()
	 */
	private function createFileStructure()
	{
		$root = $this->getRootDir() . '/var';
		if (is_dir($root))
		{
			$dirs = array(
				'/log',
				'/cache',
				'/cache/templates',
			);

			$errors = array();

			foreach ($dirs as $dir)
			{
				if (!file_exists($root . $dir))
				{
					$umask = umask(0000);
					mkdir($root . $dir, 0777);
					umask($umask);
				}
				// TODO Сделать проверку на запись в созданные директории
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализирует работу с файловой системой
	 *
	 * Устанвливает {@link $rootDir} при помощи {@link detectRootDir()}.
	 *
	 * @return void
	 *
	 * @uses detectRootDir()
	 */
	private function initFS()
	{
		$this->rootDir = $this->detectRootDir();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Чтение настроек
	 *
	 * @throws DomainException  если файл настроек содержит ошибки
	 *
	 * @uses getRootDir()
	 * @uses Eresus_Logger::log()
	 */
	private function initConf()
	{
		$filename = $this->getRootDir() . '/cfg/main.php';
		if (file_exists($filename))
		{
			$config = file_get_contents($filename);
			if (substr($config, 0, 5) == '<?php')
			{
				$config = substr($config, 5);
			}
			elseif (substr($config, 0, 2) == '<?')
			{
				$config = substr($config, 2);
			}
			$result = @eval($config);
			if ($result === false)
			{
				throw new DomainException('Error parsing cfg/main.php');
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация запроса к CMS
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	private function initRequest()
	{
		$req = Eresus_HTTP_Message::fromEnv(Eresus_HTTP_Message::TYPE_REQUEST);
		$webServer = Eresus_WebServer::getInstance();
		$this->container['request'] = new Eresus_CMS_Request($req, $webServer->getPrefix());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация локали
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @uses Eresus_i18n::getInstance()
	 * @uses Eresus_i18n::setLocale()
	 * @uses Eresus_Config::get()
	 * @uses Eresus_Template::setGlobalValue()
	 */
	private function initLocale()
	{
		$i18n = Eresus_i18n::getInstance();
		$locale = Eresus_Config::get('eresus.cms.locale', 'ru_RU');
		$i18n->setLocale($locale);
		$this->container['i18n'] = $i18n;
		Eresus_Template::setGlobalValue('i18n', $i18n);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация БД
	 *
	 * Настраивает "ленивое" соединение с БД.
	 *
	 * @throws DomainException если в настройках не указан параметр "eresus.cms.dsn"
	 *
	 * @return void
	 *
	 * @uses Eresus_Logger::log()
	 * @uses getRootDir()
	 * @uses Eresus_Config::get()
	 */
	private function initDB()
	{
		/**
		 * Подключение Doctrine
		 */
		include $this->getRootDir() . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR .
			'Doctrine.php';
		spl_autoload_register(array('Doctrine', 'autoload'));
		spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));

		$dsn = Eresus_Config::get('eresus.cms.dsn');
		if (!$dsn)
		{
			throw new DomainException('Configuration parameter "eresus.cms.dsn" not set.');
		}

		Doctrine_Manager::connection($dsn);

		$manager = Doctrine_Manager::getInstance();
		$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
		$manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);

		$prefix = Eresus_Config::get('eresus.cms.dsn.prefix');
		if ($prefix)
		{
			$manager->setAttribute(Doctrine_Core::ATTR_TBLNAME_FORMAT, $prefix . '%s');
		}

		Doctrine_Core::loadModels(dirname(__FILE__) . '/Model');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализирует сайт
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @uses Eresus_DB_ORM::getTable()
	 * @uses Eresus_Template::setGlobalValue() для установки глобальной переменной "site"
	 */
	private function initSite()
	{
		$site = Eresus_DB_ORM::getTable('Eresus_Model_Site')->find(1);
		$this->container['site'] = $site;
		Eresus_Template::setGlobalValue('site', $site);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация сессии
	 *
	 * @return void
	 * @uses Eresus_Logger::log()
	 * @uses Eresus_Service_Auth::getInstance()
	 */
	private function initSession()
	{
		//session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
		ini_set('session.use_only_cookies', true);
		session_name('sid');
		// Проверка на CLI для юнит-тестов
		Eresus_Kernel::isCLI() || session_start();

		Eresus_Service_Auth::getInstance()->init();
		$_SESSION['activity'] = time();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализирует шаблонизатор
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	private function initTemplateEngine()
	{
		Eresus_Config::set('core.template.templateDir', $this->getRootDir());
		Eresus_Config::set('core.template.compileDir', $this->getRootDir() . '/var/cache/templates');
		Eresus_Template::setGlobalValue('cms', new Eresus_Helper_ArrayAccessDecorator($this));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обрабатывает запрос к стороннему расширению
	 *
	 * Вызов производитеся через коннектор этого расширения
	 *
	 * @return void
	 */
	private function call3rdPartyExtension()
	{
		$extension = substr($this->request->getLocal(), 9);
		$extension = substr($extension, 0, strpos($extension, '/'));

		$filename = $this->getRootDir().'/ext-3rd/'.$extension.'/eresus-connector.php';
		if ($extension && is_file($filename))
		{
			include_once $filename;
			$className = $extension.'Connector';
			$connector = new $className;
			$connector->proxy();
		}
		else
		{
			header('404 Not Found', true, 404);
			echo '404 Not Found';
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Определяет корневую директорию приложения
	 *
	 * @return string
	 */
	private function detectRootDir()
	{
		$path = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..');
		if (DIRECTORY_SEPARATOR != '/')
		{
			$path = str_replace($path, DIRECTORY_SEPARATOR, '/');
		}

		return $path;
	}
	//-----------------------------------------------------------------------------
}
