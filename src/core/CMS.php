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
 * @package Eresus
 */
class Eresus_CMS extends Eresus_Application
{
	/**
	 * HTTP-запрос
	 *
	 * @var HttpRequest
	 */
	protected $request;

	/**
	 * Основной метод приложения
	 *
	 * @return int  Код завершения для консольных вызовов
	 *
	 * @see framework/core/EresusApplication#main()
	 */
	public function main()
	{
		try
		{
			/* Подключение таблицы автозагрузки классов */
			EresusClassAutoloader::add('core/cms.autoload.php');

			/* Общая инициализация */
			$this->checkEnviroment();
			$this->createFileStructure();

			/* Подключение старого ядра */
			Eresus_Logger::log(__METHOD__, LOG_NOTICE, 'Init legacy kernel');
			include_once 'kernel-legacy.php';

			/**
			 * @global Eresus Eresus
			 */
			$GLOBALS['Eresus'] = new Eresus;

			$this->initConf();
			$this->initDebugTools();
			$this->initTimezone();
			$this->initLocale();
			$this->initDB();

			$GLOBALS['Eresus']->init();
			Eresus_Template::setGlobalValue('Eresus', $GLOBALS['Eresus']);

			$this->initPlugins();
			//$this->initSession();
			$this->initTemplateEngine();

			$this->request = HTTP::request();
			//$this->response = new HttpResponse();
			$this->detectWebRoot();
			//$this->initRoutes();

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
		catch (Exception $e)
		{
			Eresus_Logger::exception($e);
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
	public function fatalError($error = null, $exit = true)
	{
		include dirname(__FILE__) . '/fatal.html.php';
		die;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка окружения
	 *
	 * @return void
	 */
	protected function checkEnviroment()
	{
		$errors = array();

		/* Проверяем наличие нужных файлов */
		$required = array('cfg/main.php');
		foreach ($required as $filename)
			if (!file_exists($filename))
				$errors []= array('file' => $filename, 'problem' => 'missing');

		/* Проверяем доступность для записи */
		$writable = array(
			'cfg/settings.php',
			'var',
			'data',
			'templates',
			'style'
		);
		foreach ($writable as $filename)
			if (!is_writable($filename))
				$errors []= array('file' => $filename, 'problem' => 'non-writable');

		if ($errors)
		{
			if (!Eresus_Kernel::isCLI())
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
	 * Запуск КИ
	 * @return string
	 * @deprecated Это временная функция
	 */
	protected function runWebClientUI()
	{
		global $page;

		Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'This method is temporary.');

		include_once 'client.php';

		$page = new TClientUI();
		$page->init();
		/*return */$page->render();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Запуск АИ
	 * @return string
	 * @deprecated Это временная функция
	 */
	protected function runWebAdminUI()
	{
		global $page;

		Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'This method is temporary.');

		include_once 'admin.php';

		$page = new TAdminUI();
		/*return */$page->render();
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
		$webServer = WebServer::getInstance();
		$DOCUMENT_ROOT = $webServer->getDocumentRoot();
		$SUFFIX = $this->getRootDir();
		$SUFFIX = substr($SUFFIX, strlen($DOCUMENT_ROOT));
		$this->request->setLocalRoot($SUFFIX);
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'detected root: %s', $SUFFIX);

		Eresus_Template::setGlobalValue('siteRoot',
			$this->request->getScheme() . '://' .
			$this->request->getHost() .
			$this->request->getLocalRoot()
		);

	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация сессии
	 */
	protected function initSession()
	{
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, '()');

/*		global $Eresus; // FIXME: Устаревшая переменная $Eresus

		session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
		session_name('sid');
		session_start();

		# Обратная совместимость
		$Eresus->session = &$_SESSION['session'];
		#if (!isset($Eresus->session['msg'])) $Eresus->session['msg'] = array('error' => array(), 'information' => array());
		#$Eresus->user = &$_SESSION['user'];
		$GLOBALS['session'] = &$_SESSION['session'];
		$GLOBALS['user'] = &$_SESSION['user'];*/
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обрабатывает запрос к стороннему расширению
	 *
	 * Вызов производитеся через коннектор этого расширения
	 *
	 * @return void
	 */
	protected function call3rdPartyExtension()
	{
		$extension = substr($this->request->getLocal(), 9);
		$extension = substr($extension, 0, strpos($extension, '/'));

		$filename = $this->getRootDir() . '/ext-3rd/' . $extension . '/eresus-connector.php';
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



/**
 * Компонент АИ
 *
 * @package Eresus
 */
class EresusAdminComponent
{

}