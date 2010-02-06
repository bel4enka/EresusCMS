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
 * @package Eresus2
 *
 * $Id$
 */

/**
 * Класс приложения Eresus CMS
 *
 * @package EresusCMS
 */
class EresusCMS extends EresusApplication {

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
		//EresusClassAutoloader::add('cms.autoload.php');

		/* Общая инициализация */
		$this->checkEnviroment();
		//$this->initConf();
		//$this->initDB();
		//$this->initSession();

		eresus_log(__METHOD__, LOG_DEBUG, 'Init legacy kernel');
		/* Подключение старого ядра */
		//include_once 'kernel-legacy.php';
		//$GLOBALS['Eresus'] = new Eresus;
		//$GLOBALS['Eresus']->init();

		if (PHP::isCLI()) {

			return $this->runCLI();

		} else {

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
			'tmp',
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
	 * Выполнение в режиме Web
	 */
	protected function runWeb()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		//$this->initWeb();

	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация Web
	 */
	protected function initWeb()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		Registry::set('core.template.compileDir', Core::app()->getFsRoot() . 'cache/templates');

		//$this->request = HTTP::request();
		//$this->response = new HttpResponse();
		//$this->detectWebRoot();
		//$this->initRoutes();
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

		/*$DOCUMENT_ROOT = realpath($_SERVER['DOCUMENT_ROOT']);
		$SUFFIX = dirname(__FILE__);
		$SUFFIX = substr($SUFFIX, strlen($DOCUMENT_ROOT));
		$SUFFIX = substr($SUFFIX, 0, -strlen('/core'));
		$this->request->setLocalRoot($SUFFIX);

		TemplateSettings::setGlobalValue('siteRoot',
			$this->request->getScheme() . '://' .
			$this->request->getHost() .
			$this->request->getLocalRoot() . '/'
		);
*/
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

		//global $Eresus; // FIXME: Устаревшая переменная $Eresus

		//@include_once 'cfg/main.php';

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
}