<?php
/**
 * ${product.title}
 *
 * Главный модуль
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
 * Класс приложения Eresus CMS
 *
 * Класс Eresus_CMS представляет собой реализацию шаблона {@link
 * http://martinfowler.com/eaaCatalog/frontController.html Front Controller}. Экземпляр Eresus_CMS
 * (доступный через {@link Eresus_Kernel::app()}) принимает все запросы к сайту и перенаправляет их
 * подчинённым классам.
 *
 * Также класс проводит инициализацию необходимых источников данных, таких как файл настроек, СУБД,
 * сессии.
 *
 * <b>История изменений</b>
 *
 * <i>2.16</i>
 *
 * - Переименован из EresusCMS в Eresus_CMS
 * - Класс более не наследуется от EresusApplication
 *
 * @package Eresus
 * @since 2.14
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
	 * Корневая директория приложения
	 *
	 * @var string
	 * @see getRootDir()
	 */
	private $rootDir;

	/**
	 * Сайт
	 *
	 * @var Eresus_Model_Site
	 */
	private $site;

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
	 * Полный файловый путь к директории приложения без финального слеша.
	 *
	 * @return string  корневая директория приложения
	 */
	public function getRootDir()
	{
		if (!$this->rootDir)
		{
			$this->rootDir = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..');
			if (DIRECTORY_SEPARATOR != '/')
			{
				$this->rootDir = str_replace($this->rootDir, DIRECTORY_SEPARATOR, '/');
			}
		}
		return $this->rootDir;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает модель сайта
	 *
	 * @return Eresus_Model_Site
	 *
	 * @since 2.16
	 */
	public function getSite()
	{
		if (!$this->site)
		{
			$req = Eresus_CMS_Request::getInstance();
			/*
			 $this->rootURL = Eresus_HTTP_Toolkit::buildURL($req->getRequestUri(), array(),
			Eresus_HTTP_Toolkit::URL_STRIP_PATH) . '/' . Eresus_WebServer::getInstance()->getPrefix();


			$this->prefix = $prefix;
			$this->rootURL = Eresus_HTTP_Toolkit::buildURL($message->getRequestUrl(), array(),
			Eresus_HTTP_Toolkit::URL_STRIP_PATH) . '/' . $prefix;

			$host = preg_replace('/^www\./i', '', $req->getHost());

			$docRoot = Eresus_WebServer::getInstance()->getDocumentRoot();
			$root = $this->getRootDir();
			var_dump($root);die;
			$root = substr($root, strlen($docRoot));

			$site = Eresus_DB_ORM::getTable('Eresus_Model_Site')->
			findOneByDql('host IN (?, "") AND (root IN (?, "")) ORDER BY host DESC, root DESC', $host,
			$root);
			if (!$site)
			{
			throw new DomainException('No default site found in database');
			}
			*/
			$this->site = Eresus_DB_ORM::getTable('Eresus_Model_Site')->find(1);

			//$site->setHost($req->getHost()); // не используем $host, чтобы не потерять "www."
			$this->site->root = $req->getRootPrefix();
		}
		return $this->site;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Основной метод приложения
	 *
	 * Этот метод вызывается автоматически.
	 *
	 * @return void
	 *
	 * @uses Eresus_Logger::log()
	 * @uses Eresus_Logger::exception()
	 * @uses Eresus_CMS_Request::getBasePath()
	 * @uses Eresus_CMS_UI_Admin::process()
	 * @uses Eresus_CMS_UI_Client::process()
	 * @uses Eresus_CMS_Response::send()
	 * @uses Eresus_Config::get()
	 * @uses Eresus_SuccessException
	 */
	public function main()
	{
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, '()');

		try
		{
			$this->createFileStructure();
			$this->initConf();
			$this->initDB();
			$this->initLocale();
			$this->initSession();
			$this->initTemplateEngine();

			if (substr(Eresus_CMS_Request::getInstance()->getBasePath(), 0, 6) == '/admin')
			{
				$ui = Eresus_CMS_UI::getInstance('Eresus_CMS_UI_Admin');
			}
			else
			{
				$ui = Eresus_CMS_UI::getInstance('Eresus_CMS_UI_Client');
			}

			$response = $ui->process();
			$response->send();

		}
		catch (Eresus_SuccessException $e)
		{
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
	 * Создание файловой структуры
	 *
	 * @return void
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
	 * Чтение настроек
	 *
	 * @throws DomainException  если файл настроек содержит ошибки
	 *
	 * @uses getRootDir()
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
	 * @uses Eresus_Config::get()
	 * @uses Eresus_DB_ORM
	 * @uses Doctrine::autoload()
	 * @uses Doctrine_Core::modelsAutoload()
	 * @uses Doctrine_Manager::connection()
	 * @uses Doctrine_Manager::getInstance()
	 * @uses Doctrine_Manager::setAttribute()
	 * @uses Doctrine_Core::loadModels()
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
	 * Инициализация сессии
	 *
	 * @return void
	 * @uses Eresus_Logger::log()
	 * @uses Eresus_Auth::getInstance()
	 * @uses Eresus_Auth::init()
	 * @uses Eresus_Kernel::isCLI()
	 */
	private function initSession()
	{
		//session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
		ini_set('session.use_only_cookies', true);
		session_name('sid');
		// Проверка на CLI для юнит-тестов
		Eresus_Kernel::isCLI() || session_start();

		Eresus_Auth::getInstance()->init();
		$_SESSION['activity'] = time();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализирует шаблонизатор
	 *
	 * @return void
	 *
	 * @uses Eresus_Config::set()
	 * @since 2.16
	 */
	private function initTemplateEngine()
	{
		Eresus_Config::set('core.template.templateDir', $this->getRootDir());
		Eresus_Config::set('core.template.compileDir', $this->getRootDir() . '/var/cache/templates');
		Eresus_Template::setGlobalValue('site', $this->getSite());
	}
	//-----------------------------------------------------------------------------

	/**
	 * TODO Проверить работоспособность
	 *
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

}
