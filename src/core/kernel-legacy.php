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
 */

use Symfony\Component\HttpFoundation\RedirectResponse;
use Eresus\CmsBundle\HTTP\Request;
use Eresus\CmsBundle\Extensions\Registry;
use Eresus\CmsBundle\Extensions\VendorRegistry;

/**
 * Название системы
 * @var string
 */
define('CMSNAME', 'Eresus');
define('CMSVERSION', '${product.version}'); # Версия системы
define('CMSLINK', 'http://eresus.ru/'); # Веб-сайт

/* Уровни доступа */
define('ROOT',   1); # Главный администратор
define('ADMIN',  2); # Администратор
define('EDITOR', 3); # Редактор
define('USER',   4); # Пользователь
define('GUEST',  5); # Гость (не зарегистрирован)

/**
 * Возвращает разметку сообщения о пользовательской ошибке
 *
 * @param string $text     Текст сообщения
 * @param string $caption  Заголовок окна сообщения
 *
 * @return string  HTML
 *
 * @see InfoBox()
 * @see ErrorMessage()
 * @since 2.00
 */
function ErrorBox($text, $caption = errError)
{
	$result =
		(empty($caption)?'':"<div class=\"errorBoxCap\">".$caption."</div>\n").
		"<div class=\"errorBox\">\n".
		$text.
		"</div>\n";
	return $result;
}

function ErrorMessage($message)
{
	Eresus_CMS::getLegacyKernel()->session['msg']['errors'][] = $message;
}

/**
 * Функция проверяет права пользователя на соответствие заданной маске
 *
 * @param int $level
 *
 * @return bool
 */
function UserRights($level)
{
	$user = Eresus_CMS::getLegacyKernel()->user;
	return (
		(
			($user['auth']) &&
			($user['access'] <= $level) &&
			($user['access'] != 0)
		) ||
		($level == GUEST)
	);
}

/**
 * Собирает настройки из массива в строку
 *
 * @param array $options
 *
 * @return string
 */
function encodeOptions($options)
{
	$result = serialize($options);
	return $result;
}

/**
 * Функция разбивает записанные в строковом виде опции на массив
 *
 * @param string $options
 * @param array  $defaults
 *
 * @return array
 */
function decodeOptions($options, $defaults = array())
{
	if (empty($options))
	{
		$result = $defaults;
	}
	else
	{
		@$result = unserialize($options);
		if (gettype($result) != 'array')
		{
			$result = $defaults;
		}
		else
		{
			if (count($defaults))
			{
				foreach ($defaults as $key => $value)
				{
					if (!array_key_exists($key, $result))
					{
						$result[$key] = $value;
					}
				}
			}
		}
	}
	return $result;
}

/**
 * Возвращает значение аргумента запроса HTTP
 *
 * Проверяет, был ли передан GET или POST аргумент $arg (в {@link Eresus::$request}), и, если он был
 * передан, возвращает его значение. В противном случае возвращается null (до 2.10 функция
 * возвращала false).
 *
 * Необязательный аргумент $filter (добавлен в 2.10) позволяет отфильтровать значение аргумента.
 * В качестве фильтра может быть использовано регулярное выражение (PCRE) или одно из ключевых слов:
 *
 * - int, integer – целое число (используется {@link intval() intval()})
 * - float – вещественное число (используется {@link floatval() floatval()})
 * - word – только буквы и цифры
 *
 * @param string $arg     Имя аргумента
 * @param mixed  $filter  Фильтр, применяемый к значению аргумента
 *
 * @return mixed  значение аргумента или null если такой аргумент не был передан в запросе
 *
 * @since 2.01
 * @deprecated используйте Eresus_HTTP_Request
 */
function arg($arg, $filter = null)
{
	/** @var Request $request */
	$request = Eresus_Kernel::get('request');
	$arg = $request->get($arg);

	if ($arg !== false && !is_null($filter))
	{
		switch ($filter)
		{
			case 'dbsafe':
			    break;
			case 'int':
			case 'integer':
			    $arg = intval($arg);
			    break;
			case 'float':
				$arg = floatval($arg);
			    break;
			case 'word':
				$arg = preg_replace('/\W/', '', $arg);
			    break;
			default:
				$arg = preg_replace($filter, '', $arg);
			    break;
		}
	}
	return $arg;
}

/**
 * Перемещает файл, загруженный по HTTP
 *
 * Перемещает загруженный по HTTP файл в указанную директорию, под указанным или автоматически
 * определяемым именем (см. $filename). Для перемещения используется функция
 * {@link move_uploaded_file() move_uploaded_file()}, таким образом на функцию upload
 * распространяются те же исключения open_basedir.
 *
 * После успешного перемещения:
 *
 * 1. Если установлена опция опция filesOwnerSetOnUpload, то функция пытается установить владельца
 *    для этого файла, заданного опцией filesOwnerDefault. Только в версиях до 2.14!
 * 2. Если установлена опция опция filesModeSetOnUpload, то функция пытается установить права для
 *    этого файла, заданного опцией filesModeDefault.
 *
 * Если в $filename указана только директория (значение $filename заканчивается слэшем «/»), то
 * файл будет помещён в указанную директорию, а имя его будет выбрано автоматически, исходя из
 * следующих правил:
 *
 * 1. Использовать имя из $_FILES[$name]['name'].
 * 2. Если включена опция filesTranslitNames, имя будет транслитерировано при помощи функции
 *    {@link translit()}.
 * 3. Если файл с таким именем уже существует и аргумент $overwrite равен false, то перед последней
 *    точкой в имени файла будет подставляться последовательно увеличиваемое число до тех пор, пока
 *    не будет получено уникальное имя файла.
 *
 * Аргумент $overwrite имеет смысл только если $filename содержит имя директории и не содержит имени
 * файла.
 *
 * @param string $name       имя элемента массива $_FILES, содержащего информацию о нужном файле
 * @param string $filename   новое имя файла
 * @param bool   $overwrite  перезаписывать или нет существующий файл
 *
 * @return bool|string
 *
 * @since 2.00
 */
function upload($name, $filename, $overwrite = true)
{
	$result = false;
	if (substr($filename, -1) == '/')
	{
		$filename .= option('filesTranslitNames') ?
			Translit($_FILES[$name]['name']) :
			$_FILES[$name]['name'];
		if (file_exists($filename) &&
			((is_string($overwrite) && $filename != $overwrite ) || (is_bool($overwrite) && !$overwrite))
		)
		{
			$i = strrpos($filename, '.');
			$fname = substr($filename, 0, $i);
			$fext = substr($filename, $i);
			$i = 1;
			while (is_file($fname.$i.$fext))
			{
				$i++;
			}
			$filename = $fname.$i.$fext;
		}
	}
	switch ($_FILES[$name]['error'])
	{
		case UPLOAD_ERR_OK:
			if (is_uploaded_file($_FILES[$name]['tmp_name']))
			{
				$moved = @move_uploaded_file($_FILES[$name]['tmp_name'], $filename);
				if ($moved)
				{
					if (option('filesModeSetOnUpload'))
					{
						$mode = option('filesModeDefault');
						$mode = empty($mode) ? 0666 : octdec($mode);
						@chmod($filename, $mode);
					}
					$result = $filename;
				}
				else
				{
					ErrorMessage(sprintf(errFileMove, $_FILES[$name]['name'], $filename));
				}
			}
		break;
		case UPLOAD_ERR_INI_SIZE:
			ErrorMessage(sprintf(errUploadSizeINI, $_FILES[$name]['name']));
		break;
		case UPLOAD_ERR_FORM_SIZE:
			ErrorMessage(sprintf(errUploadSizeFORM, $_FILES[$name]['name']));
		break;
		case UPLOAD_ERR_PARTIAL:
			ErrorMessage(sprintf(errUploadPartial, $_FILES[$name]['name']));
		break;
		case UPLOAD_ERR_NO_FILE:
			if (strlen($_FILES[$name]['name']))
			{
				ErrorMessage(sprintf(errUploadNoFile, $_FILES[$name]['name']));
			}
		break;
	}
	return $result;
}

/**
 * Возвращает значение глобального параметра
 *
 * Предоставляет доступ к параметрам, устанавливаемым в разделе конфигурации сайта:
 *
 * - siteName (string) — Название сайта
 * - siteTitle (string) — Глобальный заголовок сайта
 * - siteTitleReverse (bool) — Выводить ли составляющие заголовка в обратном порядке (от частного к
 *   общему)
 * - siteTitleDivider (string) — Разделитель составляющих заголовка
 * - siteKeywords (string) — Глобальные ключевые слова сайта
 * - siteDescription (string) — Глобальное описание сайта
 * - mailFromAddr (string) — E-mail по умолчанию для поля From отправляемых писем
 * - mailFromName (string) — Имя по умолчанию для поля From отправляемых писем
 * - mailFromOrg (string) — Название организации по умолчанию для поля From отправляемых писем
 * - mailReplyTo (string) — Значение по умолчанию для поля Reply-to отправляемых писем
 * - mailCharset (string) — Кодировка отправляемых писем
 * - mailFromSign (string) — Подпись для отправляемых писем
 * - sendNotifyTo (string) — E-mail-адреса для административных извещений
 * - filesOwnerSetOnUpload (bool) — Менять ли владельца загружаемых файлов?
 * - filesOwnerDefault (string) — Владелец загружаемых файлов
 * - filesModeSetOnUpload (bool) — Менять ли права на загружаемые файлы?
 * - filesModeDefault (string) — Права на загружаемые файлы
 * - filesTranslitNames (bool) — Транслитерировать ли имена загружаемых файлов
 * - contentTypeDefault (string) — Тип контента по умолчанию
 * - pageTemplateDefault (string) — Шаблон страницы по умолчанию
 * - clientPagesAtOnce (int) — Количество отображаемых страниц в переключателях страниц
 *
 * @param $name  имя параметра
 *
 * @return mixed
 */
function option($name)
{
	$result = defined($name) ? constant($name) : '';
	return $result;
}

function Translit($s) #: String
{
	$s = strtr($s, $GLOBALS['translit_table']);
	$s = str_replace(
		array(' ','/','?'),
		array('_','-','7'),
		$s
	);
	$s = preg_replace('/(\s|_)+/', '$1', $s);
	return $s;
}

/**
 * Основной класс приложения
 *
 * @since 2.10
 * @package Eresus
 */
class Eresus
{
	/**
	 * Конфигурация
	 *
	 * @var array
	 */
	public $conf = array(
		'lang' => 'ru',
		'timezone' => '',
		'db' => array(
			'engine'   => 'mysql',
			'host'     => 'localhost',
			'user'     => '',
			'password' => '',
			'name'     => '',
			'prefix'   => '',
		),
		'session' => array(
			'timeout' => 30,
		),
		'extensions' => array(),
		'backward' => array(
			'TPlugin' => false,
			'TContentPlugin' => false,
			'TListContentPlugin' => false,
		),
		'debug' => array(
			'enable' => false,
			'mail' => true,
		),
	);

	/**
	 * Данные сессии
	 *
	 * Это свойство представляет собой ассоциативный массив, хранящий данные текущей сессии:
	 *
	 * - time (int) — Время последней активности пользователя в этой сессии
	 * - request (array) — Используется функциями {@link saveRequest()} и {@link restoreRequest()}
	 * - msg (array) — Очередь сообщений, используемая функциями {@link ErrorMessage()} и
	 * {@link InfoMessage()}
	 *
	 * @var array
	 * @since 2.10
	 */
	public $session;

	/**
	 * Интерфейс к расширениям системы
	 *
	 * @var VendorRegistry
	 */
	public $extensions;

	/**
	 * Интерфейс к БД
	 * @var MySQL
	 * @since 2.10
	 * @deprecated с 3.00, используйте DB
	 */
	public $db;

	/**
	 * Учётная запись пользователя
	 *
	 * @var array
	 * @since 2.10
	 */
	public $user;

	/**
	 * Хост сайта
	 *
	 * @var string
	 * @since 2.10
	 * @deprecated с 3.00, используйте Eresus::$request
	 */
	public $host;

	/**
	 * Относительный URL сайта относительно корня сервера
	 *
	 * Если сайт, расположен в корне (т. е. по адресу http://example.org/), то path будет равен «/».
	 *
	 * Если сайт, расположен в поддиректории, например по адресу http://example.org/site/, то path
	 * будет равен «/site/».
	 *
	 * @var string
	 * @since 2.10
	 * @deprecated с 3.00, используйте Eresus::$request
	 */
	public $path;

	/**
	 * Полный корневой URL сайта
	 *
	 * @var string
	 * @since 2.10
	 */
	public $root;

	/**
	 * Полный URL к директории данных
	 *
	 * @var string
	 * @since 2.10
	 */
	public $data;

	/**
	 * Полный URL к директории стилей
	 *
	 * @var string
	 * @since 2.10
	 */
	public $style;

	/**
	 * Корневая директория
	 * @var string
	 * @since 2.10
	 */
	public $froot;

	/**
	 * Директория данных
	 * @var string
	 * @since 2.10
	 */
	public $fdata;

	/**
	 * Директория стилей
	 * @var string
	 * @since 2.10
	 */
	public $fstyle;

	/**
	 * Запрос HTTP
	 *
	 * Это свойство представляет собой ассоциативный массив, хранящий параметры текущего HTTP-запроса:
	 *
	 * - method (string) — HTTP-метод — GET или POST
	 * - scheme (string) — HTTP-схема: HTTP или HTTPS
	 * - url (string) — Полный URL запроса, за исключением идентификатора сессии (если он присутствует
	 *   в URL)
	 * - link (string) Заготовка для создания URL с параметрами GET. Пример:
	 *   <code>$url = $Eresus->request['link'].'type=monitor&diag=17';</code> В $url будет нечто
	 *   вроде 'http://example.org/exec.php?type=monitor&diag=17'
	 * - referer (string) — URL, откуда был совершён переход
	 * - arg (array) — Ассоциативный массив аргументов запроса (GET + POST)
	 * - path (string) — Строка, содержащая путь к текущей виртуальной директории. Например, для
	 *   запроса 'http://example.org/virt/dir/exec.php?id=1' path будет 'http://example.org/virt/dir/'
	 * - file (string) — Имя запрошенного файла. Например «exec.php» для вышеприведённого адреса
	 * - params (array) — Массив, представляющий собой набор всех виртуальных директорий URL, после
	 *   корневого URL сайта
	 *
	 * @var array
	 * @since 2.10
	 */
	public $request;

	/**
	 * Инициирует сессии
	 */
	private function init_session()
	{
		session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
		session_name('sid');
		session_start();
		$this->session = &$_SESSION['session'];
		if (!isset($this->session['msg']))
		{
			$this->session['msg'] = array('error' => array(), 'information' => array());
		}
		$this->user = &$_SESSION['user'];

		# Обратная совместимость
		$GLOBALS['session'] = &$_SESSION['session'];
		$GLOBALS['user'] = &$_SESSION['user'];
	}

	/**
	 * Определяет файловые пути
	 *
	 * @return void
	 */
	protected function init_resolve()
	{
		if (is_null($this->froot))
		{
			$this->froot = FS::nativeForm(Eresus_Kernel::app()->getFsRoot() . '/');
		}

		$this->fdata = $this->froot . 'data' . DIRECTORY_SEPARATOR;
		$this->fstyle = $this->froot . 'style' . DIRECTORY_SEPARATOR;

		if (is_null($this->path))
		{
			$s = $this->froot;
			$s = substr(dirname($_SERVER['SCRIPT_FILENAME']), strlen($_SERVER['DOCUMENT_ROOT']));
			$s = FS::canonicalForm($s);
			if (strlen($s) == 0 || substr($s, -1) != '/')
			{
				$s .= '/';
			}
			if (substr($s, 0, 1) != '/')
			{
				$s = '/' . $s;
			}
			$this->path = $s;
		}

		/**
		 * Обратная совместимость
		 * @var string
		 * @deprecated since 2.14
		 */
		define('filesRoot', $this->froot);

		/**
		 * Обратная совместимость
		 * @var string
		 * @deprecated since 2.14
		 */
		define('dataFiles', $this->fdata);
	}

	/**
	 * Читает настройки
	 *
	 * @throws RuntimeException
	 */
	private function init_settings()
	{
		$filename = $this->froot.'cfg/settings.php';
		if (is_file($filename))
		{
			include($filename);
		}
		else
		{
			throw new RuntimeException("Settings file '$filename' not found!");
		}
	}

	/**
	 * Первичный разбор запроса
	 *
	 * @access  private
	 */
	function init_request()
	{
		/** @var Request $request */
		$request = Eresus_Kernel::get('request');
		$this->request = array(
			'method' => $request->getMethod(),
			'scheme' => $request->getScheme(),
			'host' => $request->getHost(),
			'port' => $request->getPort(),
			'user' => isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '',
			'pass' => isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '',
			'path' => $request->getPath(),
			'query' => $request->getQueryString(),
			'fragment' => '',
			'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
			'url' => $request->getUri(),
			'file' => $request->getFilename(),
			'link' => $request->getPathInfo() . '?',
		);

		$this->host = $request->getHost();
		$this->root = $request->getScheme() . '://' . $request->getHost() . $request->getBasePath() .
			'/';
		$this->data = $this->root . '/data/';
		$this->style = $this->root . '/style';

		// Сбор аргументов вызова
		$this->request['arg'] = array_merge($request->query->all(), $request->request->all());
		// Разбивка параметров вызова скрипта
		$this->request['params'] = explode('/', $request->getLocalUrl());
	}

	/**
	 * Инициализация локали
	 *
	 * @throws RuntimeException
	 */
	private function init_locale()
	{
		global $locale;

		$locale['lang'] = $this->conf['lang'];
		$locale['prefix'] = '';

		# Подключение строковых данных
		$filename = $this->froot.'lang/'.$locale['lang'].'.php';
		if (is_file($filename))
		{
			include($filename);
		}
		else
		{
			throw new RuntimeException("Locale file '$filename' not found!");
		}
	}

	/**
	 * Инициализация расширений
	 */
	function init_extensions()
	{
		$filename = $this->froot.'cfg/extensions.php';
		if (is_file($filename))
		{
			include_once($filename);
		}

		$this->extensions = new VendorRegistry();
	}

	/**
	 * Подключение к источнику данных
	 *
	 * @access private
	 */
	function init_datasource()
	{
		include __DIR__ . '/mysql.php';
		$this->db = new MySQL;
		$this->db->init($this->conf['db']['host'], $this->conf['db']['user'],
			$this->conf['db']['password'], $this->conf['db']['name'], $this->conf['db']['prefix']);
	}

	/**
	 * Проверка сессии
	 *
	 * @access private
	 */
	function check_session()
	{
		if (isset($this->session['time']))
		{
			if (
				(time() - $this->session['time'] > $this->conf['session']['timeout']*3600) &&
				($this->user['auth'])
			)
			{
				$this->logout(false);
			}
			else
			{
				$this->session['time'] = time();
			}
		}
	}

	/**
	 * Проверка на логин/логаут
	 *
     * @return Response|null
	 */
	function check_loginout()
	{
		switch (arg('action'))
		{
			case 'login':
				$this->login(arg('user'), $this->password_hash(arg('password')), arg('autologin', 'int'));
				return new RedirectResponse($this->request['url']);
			    break;
			case 'logout':
				$this->logout(true);
				return new RedirectResponse($this->root.'admin/');
			    break;
		}
        return null;
	}

	/**
	 * Попытка cookie-логина
	 */
	function check_cookies()
	{
		if (!$this->user['auth'] && isset($_COOKIE['eresus_login']))
		{
			if (!$this->login($_COOKIE['eresus_login'], $_COOKIE['eresus_key'], true, true))
			{
				$this->clear_login_cookies();
			}
		}
	}

	/**
	 * Обновление данных о пользователе
	 */
	function reset_login()
	{
		$this->user['auth'] = isset($this->user['auth']) ? $this->user['auth'] : false;
		if ($this->user['auth'])
		{
			$item = $this->db->selectItem('users', "`id`='".$this->user['id']."'");
			if (!is_null($item))
			{
				# Если такой пользователь есть...
				if ($item['active'])
				{
					# Если учетная запись активна...
					$this->user['name'] = $item['name'];
					$this->user['mail'] = $item['mail'];
					$this->user['access'] = $item['access'];
					$this->user['profile'] = decodeOptions($item['profile']);
				}
				else
				{
					ErrorMessage(sprintf(ERR_ACCOUNT_NOT_ACTIVE, $item['login']));
					$this->logout();
				}
			}
			else
			{
				$this->logout();
			}
		}
		else
		{
			$this->user['access'] = GUEST;
		}
	}

	/**
	 * Инициализация системы
     *
     * @return Response|null
	 */
	public function init()
	{
		// Отключение закавычивания передаваемых данных
		if (!PHP::checkVersion('5.3'))
		{
			set_magic_quotes_runtime(0);
		}
		if ($this->conf['timezone'])
		{
			date_default_timezone_set($this->conf['timezone']);
		}
		# Определение путей
		$this->init_resolve();
		# Инициализация сессии
		$this->init_session();
		# Изменяем путь поиска подключаемых файлов
		set_include_path(dirname(__FILE__) . '/lib' . PATH_SEPARATOR . get_include_path());
		# Читаем настройки
		$this->init_settings();
		# Первичный разбор запроса
		$this->init_request();
		# Настройка локали
		$this->init_locale();
		# Инициализация расширений
		$this->init_extensions();
		# Подключение к источнику данных
		$this->init_datasource();
		# Проверка сессии
		$this->check_session();
		# Проверка логина/логаута
		$response = $this->check_loginout();
        if ($response)
        {
            return $response;
        }
		# Попытка cookie-логина
		$this->check_cookies();
		# Обновление данных о пользователе
		$this->reset_login();
        return null;
	}

	/**
	 * Хеширует пароль
	 *
	 * @param string $password  Пароль
	 * @return string  Хеш
	 */
	function password_hash($password)
	{
		$result = md5($password);
		if (!$this->conf['backward']['weak_password'])
		{
			$result = md5($result);
		}
		return $result;
	}

	/**
	 * Устанавливает авторизационные куки
	 *
	 * @param string $login
	 * @param string $key
	 */
	private function set_login_cookies($login, $key)
	{
		setcookie('eresus_login', $login, time()+2592000, $this->path);
		setcookie('eresus_key', $key, time()+2592000, $this->path);
	}

	/**
	 * Удаляет авторизационные куки
	 *
	 */
	function clear_login_cookies()
	{
		setcookie('eresus_login', '', time()-3600, $this->path);
		setcookie('eresus_key', '', time()-3600, $this->path);
	}

	/**
	 * Авторизация пользователя
	 *
	 * @param string $unsafeLogin  Имя пользователя
	 * @param string $key		   Ключ учётной записи
	 * @param bool   $auto		   Сохранить авторизационные данные на компьютере посетителя
	 * @param bool   $cookie       Авторизация при помощи cookie
     *
	 * @return bool Результат
	 */
	public function login($unsafeLogin, $key, $auto = false, $cookie = false)
	{
        $result = false;

		$login = preg_replace('/[^a-z0-9_\-\.\@]/', '', $unsafeLogin);

		if ($login != $unsafeLogin)
		{
			ErrorMessage(ERR_PASSWORD_INVALID);
			return false;
		}

        /** @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine */
        $doctrine = Eresus_Kernel::get('doctrine');
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $doctrine->getManager();
        /** @var \Eresus\CmsBundle\Entity\Account $account */
        $account = $em->getRepository('CmsBundle:Account')->findOneByLogin($login);

		// Если такой пользователь есть...
		if (null !== $account)
		{
			// Если учетная запись активна...
			if ($account->active)
			{
				$noBruteForcing = time() - $account->lastLoginTime > $account->loginErrors;
				if ($noBruteForcing || $this->conf['debug']['enable'])
				{
					// Если пароль верен...
					if ($key == $account->hash)
					{
						if ($auto)
						{
							$this->set_login_cookies($login, $key);
						}
						else
						{
							$this->clear_login_cookies();
						}
						$setVisitTime = (! isset($this->user['id'])) || (! (bool) $this->user['id']);
						$this->user = $account->toArray();
                        $this->user['auth'] = true;
						if ($setVisitTime)
						{
                            // Записываем время последнего входа
							$account->lastVisit = new DateTime();;
						}
						$account->lastLoginTime = time();
						$account->loginErrors = 0;
						$this->session['time'] = time(); # Инициализируем время последней активности сессии.
						$result = true;
					}
					else
					{
						// Если пароль не верен...
						if (!$cookie)
						{
							ErrorMessage(ERR_PASSWORD_INVALID);
							$account->lastLoginTime = time();
							$account->loginErrors++;
						}
					}
				}
				else
				{
					// Если авторизация проведена слишком рано
					ErrorMessage(sprintf(ERR_LOGIN_FAILED_TOO_EARLY, $account->loginErrors));
					$account->lastLoginTime = time();
				}
			}
			else
			{
				ErrorMessage(sprintf(ERR_ACCOUNT_NOT_ACTIVE, $login));
			}
		}
		else
		{
			ErrorMessage(ERR_PASSWORD_INVALID);
		}
        $em->flush();
		return $result;
	}

	/**
	 * Завершение сеанса работы с системой
	 *
	 * @param bool $clearCookies
	 */
	public function logout($clearCookies=true)
	{
		$this->user['id'] = null;
		$this->user['auth'] = false;
		$this->user['access'] = GUEST;
		if ($clearCookies)
		{
			$this->clear_login_cookies();
		}
	}
}
