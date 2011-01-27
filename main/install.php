<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Установка CMS
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
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
 * @package EresusCMS
 *
 * $Id$
 */

/**
 * Простой некеширующий шаблонизатор
 *
 * @package EresusCMS
 * @since 2.16
 */
class EresusTemplateNC
{
	/**
	 * Имя файла шаблона
	 *
	 * @var string
	 */
	private $filename;

	/**
	 * Создаёт новый шаблон
	 *
	 * @param string $filename
	 *
	 * @return EresusTemplateNC
	 *
	 * @since 2.16
	 */
	public function __construct($filename)
	{
		$this->filename = $filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Компилирует шаблон
	 *
	 * @param array $data
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function compile($data = array())
	{
		$tmpl = file_get_contents($this->filename);
		$tmpl = $this->processTemplate($tmpl, $data);
		$tmpl = preg_replace('/\{\$.*\}/', '', $tmpl);
		return $tmpl;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обрабатывает шаблон
	 *
	 * @param string $code  код шаблона
	 * @param array  $vars  переменные
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	private function processTemplate($code, $vars)
	{
		$l = 0;
		while (($p = strpos($code, '{', $l)) !== false)
		{
			switch (true)
			{
				case substr($code, $p, 2) == '{$':
					$l = strpos($code, '}', $p);
					$var = substr($code, $p + 2, $l - $p - 2);
					$value = isset($vars[$var]) ? $vars[$var] : '';
					$code = substr_replace($code, $value, $p, $l - $p + 1);
				break;

				case substr($code, $p, 4) == '{if ':
					$l = strpos($code, '{/if}', $p);
					$block = substr($code, $p, $l - $p + 5);
					preg_match('~\{if\s+\$(\w+)\}(.+)\{/if\}~', $block, $match);
					$var = $match[1];
					$expr = isset($vars[$var]) ? $vars[$var] : '';
					$value = $expr ? $this->processTemplate($match[2], $vars) : '';
					$code = substr_replace($code, $value, $p, $l - $p + 5);
				break;

				default:
					$l = $p + 1;
				break;
			}
		}
		return $code;
	}
	//-----------------------------------------------------------------------------
}

/**
 * Инсталлятор
 *
 * @package EresusCMS
 * @since 2.16
 */
class Installer extends EresusApplication
{
	/**
	 * Внутреннние переменные
	 *
	 * @var array
	 */
	private $vars;

	/**
	 * Дескриптор соединения с серевером FTP
	 *
	 * @var resource
	 */
	private $ftp;

	/**
	 * Корнивая диретория сайта по FTP
	 *
	 * @var string
	 */
	private $ftpSiteRoot;

	/**
	 * DSN
	 *
	 * @var string
	 */
	private $dsn;

	/**
	 * Инициализация
	 *
	 * @return Installer
	 *
	 * @since 2.16
	 */
	public function __construct()
	{
		parent::__construct();
	}
	//-----------------------------------------------------------------------------
	/**
	 * (non-PHPdoc)
	 * @see EresusApplication::main()
	 */
	public function main()
	{
		try
		{
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$this->vars = $_POST;

				if ($this->checkInput())
				{
					if ($this->connectToDB() && $this->connectToFTP())
					{
						$this->setup();
					}
				}
			}
			else
			{
				$this->vars['dbhost'] = 'localhost';
				$this->vars['ftphost'] = $_SERVER['HTTP_HOST'];
			}

			$tmpl = new EresusTemplateNC($this->getFsRoot() . '/core/templates/Installer/page.html');
			echo $tmpl->compile($this->vars);
		}
		catch (Exception $e)
		{
			header('Content-type: text/plain');
			echo $e->getMessage() . "\n" . $e->getTraceAsString();
			die;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет заполненность полей
	 *
	 * @return bool  TRUE, если все необходимые поля заполнены
	 *
	 * @since 2.16
	 */
	private function checkInput()
	{
		$valid = true;

		if (!$this->vars['dbhost'])
		{
			$this->vars['dbhost_error'] = 'Не указан хост сервера БД';
			$valid = false;
		}
		if (!$this->vars['dbuser'])
		{
			$this->vars['dbuser_error'] = 'Не указано имя пользователя';
			$valid = false;
		}
		if (!$this->vars['dbpass'])
		{
			$this->vars['dbpass_error'] = 'Не указан пароль пользователя';
			$valid = false;
		}
		if (!$this->vars['dbname'])
		{
			$this->vars['dbname_error'] = 'Не указано имя базы данных';
			$valid = false;
		}

		if (!$this->vars['ftphost'])
		{
			$this->vars['ftphost_error'] = 'Не указан хост сервера FTP';
			$valid = false;
		}
		if (!$this->vars['ftpuser'])
		{
			$this->vars['ftpuser_error'] = 'Не указано имя пользователя';
			$valid = false;
		}
		if (!$this->vars['ftppass'])
		{
			$this->vars['ftppass_error'] = 'Не указан пароль пользователя';
			$valid = false;
		}

		return $valid;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Подключается к СУБД
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	private function connectToDB()
	{
		$this->dsn = 'mysql://' . $this->vars['dbuser'] . ':' . $this->vars['dbpass'] . '@' . $this->vars['dbhost'] .
			'/' . $this->vars['dbname'];
		try
		{
			/**
			 * Подключение Doctrine
			 */
			include_once 'core/Doctrine.php';
			spl_autoload_register(array('Doctrine', 'autoload'));
			spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));
			Doctrine_Manager::connection($this->dsn, 'doctrine')->setCharset('cp1251');
			return true;
		}
		catch (Exception $e)
		{
			$this->vars['db_error'] = 'Не удалось подключиться к БД: ' . $e->getMessage();
			return false;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Подключается к FTP
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	private function connectToFTP()
	{
		$this->ftp = ftp_connect($this->vars['ftphost']);
		if ($this->ftp)
		{
			try
			{
				ftp_login($this->ftp, $this->vars['ftpuser'], $this->vars['ftppass']);
			}
			catch (Exception $e)
			{
				$this->vars['ftp_error'] = 'Ошибка подключения по FTP: ' . $e->getMessage();
				return false;
			}
		}
		else
		{
			$this->vars['ftp_error'] = 'Не удалось подключиться к серверу ' . $this->vars['ftphost'];
			return false;
		}
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Установка
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	private function setup()
	{
		$this->setupDatabase();
		$this->setupFiles();
		$this->saveConfig();
		ftp_rename($this->ftp, $this->ftpSiteRoot . '/install.php',
			$this->ftpSiteRoot . '/install_php');

		HttpResponse::redirect('./');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Подготавливает БД
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	private function setupDatabase()
	{
		require_once $this->getFsRoot() . '/core/DBAL/EresusActiveRecord.php';
		$manager = Doctrine_Manager::getInstance();
		$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
		$manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);

		/*$prefix = Core::$values['eresus.cms.dsn.prefix'];
		if ($prefix)
		{
			$manager->setAttribute(Doctrine_Core::ATTR_TBLNAME_FORMAT, $prefix . '%s');
		}*/

		Doctrine_Core::createTablesFromModels($this->getFsRoot() . '/core/Domain');

		/* TODO: Переделать через YAML */
		$user = new EresusUser();
		$user->username = 'root';
		$user->password = '';
		$user->active = true;
		$user->access = 1;
		$user->fullname = iconv('utf-8', 'cp1251', 'Главный администратор');
		$user->mail = 'root@example.org';
		$user->save();

		$section = new EresusSiteSection();
		$section->name = 'main';
		$section->owner = 0;
		$section->title = iconv('utf-8', 'cp1251', 'Главная страница');
		$section->caption = iconv('utf-8', 'cp1251', 'Главная');
		$section->active = true;
		$section->access = 5;
		$section->visible = true;
		$section->template = 'default';
		$section->type = 'default';
		$section->content = '<h1>Eresus CMS</h1>';
		$section->save();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Подготавливает файлы
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	private function setupFiles()
	{
		$rootDir = $this->getFtpSiteRoot();

		/*
		 * Выставляем права
		 */
		$this->makeWritable($rootDir . '/data');
		$this->makeWritable($rootDir . '/style');
		$this->makeWritable($rootDir . '/templates');
		$this->makeWritable($rootDir . '/var');
		$this->makeWritable($rootDir . '/cfg/settings.php');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает путь к корню сайта по FTP
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	private function getFtpSiteRoot()
	{
		$tokens = explode('/', $this->getFsRoot());
		$dirs = ftp_nlist($this->ftp, '/');
		$candidats = array_intersect($tokens, $dirs);
		foreach ($candidats as $dir)
		{
			$pos = strpos($this->getFsRoot(), '/' . $dir . '/');
			$relDir = substr($this->getFsRoot(), $pos);
			if (ftp_chdir($this->ftp, $relDir))
			{
				$this->ftpSiteRoot = $relDir;
				return $this->ftpSiteRoot;
			}
		}
		throw new Exception('Can not find site directory.');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Делает файл или директорию (рекурсивно) доступными для записи
	 *
	 * @param string $path
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	private function makeWritable($path)
	{
		if (@ftp_chdir($this->ftp, $path))
		{
			ftp_chmod($this->ftp, 0777, $path);
			$files = ftp_nlist($this->ftp, $path);
			foreach ($files as $file)
			{
				$this->makeWritable($path . '/' . $file);
			}
		}
		else
		{
			ftp_chmod($this->ftp, 0666, $path);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Сохраняет настройки в конфигурационном файле
	 *
	 *
	 * @return void
	 *
	 * @since ?.??
	 */
	private function saveConfig()
	{
		$conf = file_get_contents($this->getFsRoot() . '/cfg/main.template.php');

		$conf = preg_replace("/('eresus.cms.dsn',\s+').*('\);)/", "$1{$this->dsn}$2", $conf);
		$mode = fileperms($this->getFsRoot() . '/cfg');
		ftp_chmod($this->ftp, 0777, $this->ftpSiteRoot . '/cfg');
		file_put_contents($this->getFsRoot() . '/cfg/main.php', $conf);
		ftp_chmod($this->ftp, $mode, $this->ftpSiteRoot . '/cfg');
	}
	//-----------------------------------------------------------------------------
}
