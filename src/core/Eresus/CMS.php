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
 */

use Symfony\Component\ClassLoader\MapClassLoader;

/**
 * Класс приложения Eresus CMS
 *
 * @package Eresus
 */
class Eresus_CMS extends EresusApplication
{
	/**
	 * HTTP-запрос
	 *
	 * @var HttpRequest
	 */
	protected $request;

	/**
	 * @var Eresus_WebPage
	 * @since 3.00
	 */
	protected $page;

	/**
	 * Основной метод приложения
	 *
	 * @return int  Код завершения для консольных вызовов
	 *
	 * @see framework/core/EresusApplication#main()
	 */
	public function main()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		try
		{
			/* Общая инициализация */
			$this->checkEnvironment();
			$this->createFileStructure();

			eresus_log(__METHOD__, LOG_DEBUG, 'Init legacy kernel');

			/* Подключение старого ядра */
			include ERESUS_SITE_ROOT . '/core/kernel-legacy.php';

			/**
			 * @global Eresus Eresus
			 */
			$GLOBALS['Eresus'] = new Eresus;
			$this->initConf();

			$i18n = Eresus_I18n::getInstance();
			TemplateSettings::setGlobalValue('i18n', $i18n);
			//$this->initDB();
			//$this->initSession();
			Eresus_CMS::getLegacyKernel()->init();
			TemplateSettings::setGlobalValue('Eresus', Eresus_CMS::getLegacyKernel());

			if (Eresus_Kernel::isCLI())
			{
				return $this->runCLI();
			}
			else
			{
				$this->runWeb();
			}
		}
		catch (Exception $e)
		{
			Core::logException($e);
			ob_end_clean();
			$this->fatalError($e, false);
		}
		return 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выводит сообщение о фатальной ошибке и прекращает работу приложения
	 *
	 * @param Exception|string $error  исключение или описание ошибки
	 * @param bool             $exit   завершить или нет выполнение приложения
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function fatalError(/** @noinspection PhpUnusedParameterInspection */
		$error = null, $exit = true)
	{
		include ERESUS_SITE_ROOT . '/core/fatal.html.php';
		die;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает экземпляр класса Eresus
	 *
	 * Метод нужен до отказа от класса Eresus
	 *
	 * @return Eresus
	 *
	 * @since 3.00
	 */
	public static function getLegacyKernel()
	{
		return $GLOBALS['Eresus'];
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает экземпляр класса Eresus_ClientUI или Eresus_AdminUI
	 *
	 * Метод нужен до отказа от переменной $page
	 *
	 * @return Eresus_WebPage
	 *
	 * @since 3.00
	 */
	public function getPage()
	{
		return $this->page;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка окружения
	 *
	 * @return void
	 */
	protected function checkEnvironment()
	{
		$errors = array();

		/* Проверяем наличие нужных файлов */
		$required = array('cfg/main.php');
		foreach ($required as $filename)
		{
			if (!FS::exists($filename))
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
			if (!FS::isWritable($filename))
			{
				$errors []= array('file' => $filename, 'problem' => 'non-writable');
			}
		}

		if ($errors)
		{
			if (!PHP::isCLI())
			{
				include ERESUS_SITE_ROOT . '/core/errors.html.php';
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
	 */
	protected function createFileStructure()
	{
		$dirs = array(
			'/var/log',
			'/var/cache',
			'/var/cache/templates',
		);

		foreach ($dirs as $dir)
		{
			if (!FS::exists($this->getFsRoot() . $dir))
			{
				$umask = umask(0000);
				mkdir(FS::nativeForm($this->getFsRoot() . $dir), 0777);
				umask($umask);
			}
			// TODO Сделать проверку на запись в созданные директории
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выполнение в режиме Web
	 */
	protected function runWeb()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		$this->initWeb();

		$output = '';

		switch (true)
		{
			case substr($this->request->getLocal(), 0, 8) == '/ext-3rd':
				$this->call3rdPartyExtension();
			break;

			case substr($this->request->getLocal(), 0, 6) == '/admin':
				$output = $this->runWebAdminUI();
			break;

			default:
				$output = $this->runWebClientUI();
			break;
		}

		echo $output;
	}

	/**
	 * Инициализация Web
	 */
	protected function initWeb()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		Core::setValue('core.template.templateDir', $this->getFsRoot());
		Core::setValue('core.template.compileDir', $this->getFsRoot() . '/var/cache/templates');

		Eresus_Kernel::sc()->set('request', Eresus_HTTP_Request::createFromGlobals());
		$this->request = HTTP::request();
		//$this->response = new HttpResponse();
		$this->detectWebRoot();
		//$this->initRoutes();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Запуск КИ
	 * @return string
	 * @deprecated Это временная функция
	 */
	protected function runWebClientUI()
	{
		eresus_log(__METHOD__, LOG_DEBUG, 'This method is temporary.');

		$GLOBALS['page'] = $this->page = new Eresus_ClientUI();
		$this->page->init();
		/*return */$this->page->render();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Запуск АИ
	 * @return string
	 * @deprecated Это временная функция
	 */
	protected function runWebAdminUI()
	{
		eresus_log(__METHOD__, LOG_DEBUG, 'This method is temporary.');

		$GLOBALS['page'] = $this->page = new Eresus_AdminUI();
		/*return */$this->page->render();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Определение корневого веб-адреса сайта
	 *
	 * Метод определяет корневой адрес сайта и устанавливает соответствующим
	 * образом localRoot объекта EresusCMS::request
	 */
	protected function detectWebRoot()
	{
		$webServer = Eresus_WebServer::getInstance();
		$DOCUMENT_ROOT = $webServer->getDocumentRoot();
		$SUFFIX = $this->getFsRoot();
		$SUFFIX = substr($SUFFIX, strlen($DOCUMENT_ROOT));
		$this->request->setLocalRoot($SUFFIX);
		eresus_log(__METHOD__, LOG_DEBUG, 'detected root: %s', $SUFFIX);

		TemplateSettings::setGlobalValue('siteRoot',
			$this->request->getScheme() . '://' .
			$this->request->getHost() .
			$this->request->getLocalRoot()
		);

	}
	//-----------------------------------------------------------------------------

	/**
	 * Выполнение в режиме CLI
	 *
	 * @return int
	 */
	protected function runCLI()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		$this->initCLI();
		return 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация CLI
	 */
	protected function initCLI()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация конфигурации
	 */
	protected function initConf()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		/*
		 * Переменную $Eresus приходится делать глобальной, чтобы файл конфигурации
		 * мог записывать в неё свои значения.
		 * TODO Избавиться от глобальной переменной
		 */
		/** @noinspection PhpUnusedLocalVariableInspection */
		global $Eresus;

		$filename = $this->getFsRoot() . '/cfg/main.php';
		if (file_exists($filename))
		{
			/** @noinspection PhpIncludeInspection */
			include $filename;
			// TODO: Сделать проверку успешного подключения файла
		}
		else
		{
			$this->fatalError("Main config file '$filename' not found!");
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация БД
	 */
	protected function initDB()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');
		/*
		global $Eresus;

		// FIXME Использование устаревших настроек
		$dsn = ($Eresus->conf['db']['engine'] ? $Eresus->conf['db']['engine'] : 'mysql') .
			'://' . $Eresus->conf['db']['user'] .
			':' . $Eresus->conf['db']['password'] .
			'@' . ($Eresus->conf['db']['host'] ? $Eresus->conf['db']['host'] : 'localhost') .
			'/' . $Eresus->conf['db']['name'];

		DBSettings::setDSN($dsn);*/
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация сессии
	 */
	protected function initSession()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		/*global $Eresus;

		session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
		session_name('sid');
		session_start();

		# Обратная совместимость
		$Eresus->session = &$_SESSION['session'];
		#if (!isset($Eresus->session['msg']))
			$Eresus->session['msg'] = array('error' => array(), 'information' => array());
		#$Eresus->user = &$_SESSION['user'];
		$GLOBALS['session'] = &$_SESSION['session'];
		$GLOBALS['user'] = &$_SESSION['user'];*/
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обрабатывает запрос к стороннему расширению
	 *
	 * Вызов производится через коннектор этого расширения
	 *
	 * @return void
	 */
	protected function call3rdPartyExtension()
	{
		$extension = substr($this->request->getLocal(), 9);
		$extension = substr($extension, 0, strpos($extension, '/'));

		$filename = $this->getFsRoot().'/ext-3rd/'.$extension.'/eresus-connector.php';
		if ($extension && is_file($filename))
		{
			/** @noinspection PhpIncludeInspection */
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
