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
	 * @uses Eresus_Kernel_PHP::isCLI()
	 * @uses fatalError()
	 * @uses Eresus_CMS_Mode_CLI
	 */
	public function main()
	{
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, '()');

		try
		{
			$this->initFS();
			$this->checkEnviroment();
			$this->createFileStructure();
			$this->initConf();
			$this->initLocale();
			$this->initDB();
			$this->initSite(); // TODO Скорее всего надо перенести это куда-то после создания интерфейса

			if (Eresus_Kernel_PHP::isCLI())
			{
				$mode = new Eresus_CMS_Mode_CLI();
			}
			else
			{
				$mode = new Eresus_CMS_Mode_Web();
			}

			/*
			 * Собираем контейнер объектов CMS
			 */
			$this->container['mode'] = $mode;
			$this->container['request'] = $mode->getRequest();
			$this->container['ui'] = $mode->getUI();

			$response = $mode->process();
			$response->send();

			// FIXME Сделать вывод зависимым от режима
			if (Eresus_Config::get('eresus.cms.debug'))
			{
				$memory = number_format(memory_get_peak_usage(true) / 1024, 0, ',', ' ');
				echo "<!-- Memory: $memory MiB -->\n";
				if (!Eresus_Kernel::isWindows())
				{
					$ru = getrusage();
					echo sprintf("<!-- utime: %d.%06d sec -->\n", $ru['ru_utime.tv_sec'],
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
		}

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
	 * Проверка окружения
	 *
	 * Проверяет наличие файлов, необходимых для работы CMS, а также доступность нужных файлов на
	 * запись.
	 *
	 * @return void
	 *
	 * @uses Eresus_Kernel_PHP::isCLI()
	 */
	private function checkEnviroment()
	{
		$errors = array();

		/* Проверяем наличие нужных файлов */
		$required = array('cfg/main.php');
		foreach ($required as $filename)
		{
			if (!file_exists($filename))
			{
				$errors []= array('file' => $filename, 'problem' => 'missing');
			}
		}

		/* Проверяем доступность для записи */
		$writable = array(
			'cfg/settings.php',
			'var',
			'data',
			'templates',
			'style'
		);
		foreach ($writable as $filename)
		{
			if (!is_writable($filename))
			{
				$errors []= array('file' => $filename, 'problem' => 'non-writable');
			}
		}

		if ($errors)
		{
			if (!Eresus_Kernel_PHP::isCLI())
			{
				require_once 'errors.html.php';
			}
			else
			{
				die("Errors...\n"); // TODO Доделать
			}
		}
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
		$dirs = array(
			'/var/log',
			'/var/cache',
			'/var/cache/templates',
		);

		$errors = array();

		foreach ($dirs as $dir)
		{
			if (!file_exists($this->getRootDir() . $dir))
			{
				$umask = umask(0000);
				mkdir($this->getRootDir() . $dir, 0777);
				umask($umask);
			}
			// TODO Сделать проверку на запись в созданные директории
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
		$config = file_get_contents($this->getRootDir() . '/cfg/main.php');
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
		$this->site = Eresus_DB_ORM::getTable('Eresus_Model_Site')->find(1);
		Eresus_Template::setGlobalValue('site', $this->site);
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
