<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Главный модуль
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 *
 * $Id$
 */

/**
 * Класс приложения Eresus CMS
 *
 * @package EresusCMS
 */
class EresusCMS extends EresusApplication
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
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		/* Подключение таблицы автозагрузки классов */
		EresusClassAutoloader::add('core/cms.autoload.php');

		/* Общая инициализация */
		$this->checkEnviroment();
		$this->createFileStructure();

		eresus_log(__METHOD__, LOG_DEBUG, 'Init legacy kernel');

		/* Подключение старого ядра */
		include_once 'kernel-legacy.php';
		$GLOBALS['Eresus'] = new Eresus;
		$this->initConf();
		$i18n = I18n::getInstance();
		TemplateSettings::setGlobalValue('i18n', $i18n);
		//$this->initDB();
		//$this->initSession();
		$GLOBALS['Eresus']->init();
		TemplateSettings::setGlobalValue('Eresus', $GLOBALS['Eresus']);

		if (PHP::isCLI())
		{
			return $this->runCLI();
		}
		else
		{
			$this->runWeb();
			return 0;
		}

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
			if (!FS::exists($filename))
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
			if (!FS::isWritable($filename))
				$errors []= array('file' => $filename, 'problem' => 'non-writable');

		if ($errors)
		{
			if (!PHP::isCLI())
				require_once 'errors.html.php';
			else
				die("Errors...\n"); // TODO Доделать
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

		$errors = array();

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
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация Web
	 */
	protected function initWeb()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		Core::setValue('core.template.templateDir', $this->getFsRoot());
		Core::setValue('core.template.compileDir', $this->getFsRoot() . '/var/cache/templates');

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
		global $page;

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
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		$DOCUMENT_ROOT = realpath($_SERVER['DOCUMENT_ROOT']);
		$SUFFIX = dirname(__FILE__);
		$SUFFIX = substr($SUFFIX, strlen($DOCUMENT_ROOT));
		$SUFFIX = substr($SUFFIX, 0, -strlen('/core'));
		$this->request->setLocalRoot($SUFFIX);

		TemplateSettings::setGlobalValue('siteRoot',
			$this->request->getScheme() . '://' .
			$this->request->getHost() .
			$this->request->getLocalRoot()
		);

	}
	//-----------------------------------------------------------------------------

	/**
	 * Выполнение в режиме CLI
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

		global $Eresus; // FIXME: Устаревшая переменная $Eresus

		@include_once $this->getFsRoot() . '/cfg/main.php';

		// TODO: Сделать проверку успешного подключения файла
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация БД
	 */
	protected function initDB()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');
/*
		global $Eresus; // FIXME: Устаревшая переменная $Eresus

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

		$filename = $this->getFsRoot().'/ext-3rd/'.$extension.'/eresus-connector.php';
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
 * @package EresusCMS
 */
class EresusAdminComponent
{

}