<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Ядро
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
 * @package Kernel
 *
 * $Id$
 */



/**
 * Ядро
 *
 * Ядро содержит в себе:
 * 1. основные средства абстрагирования;
 * 2. минимальный набор функционала, необходимый для обработки большинства запросов
 *
 * @package Kernel
 */
class Eresus_Kernel
{
	/**
	 * Резервный буфер для отлова ошибок переполнения памяти (в Кб)
	 *
	 * @var int
	 */
	const MEMORY_OVERFLOW_BUFFER_SIZE = 64;

	/**
	 * Признак иницилизации ядра
	 *
	 * @var bool
	 */
	static private $inited = false;

	/**
	 * Выполняемое приложение
	 *
	 * @var EresusApplication
	 * @see exec, app()
	 */
	static private $app = null;

	/**
	 * Инициализация ядра
	 */
	// @codeCoverageIgnoreStart
	static public function init()
	{
		/* Разрешаем только однократный вызов этого метода */
		if (self::$inited)
		{
			return;
		}

		/* Предотвращает появление ошибок, связанных с неустановленной временной зоной */
		@$timezone = date_default_timezone_get();
		date_default_timezone_set($timezone);

		// Регистрация автозагрузчика классов
		spl_autoload_register(array('Eresus_Kernel', 'autoload'));

		self::initExceptionHandling();

		self::$inited = true;
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

	/**
	 * Инициализирует обработчики ошибок
	 */
	static private function initExceptionHandling()
	{
		/* Резервируем буфер на случай переполнения памяти */
		$GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER'] =
			str_repeat('x', self::MEMORY_OVERFLOW_BUFFER_SIZE * 1024);

		/* Меняем значения php.ini */
		ini_set('html_errors', 0); // Немного косметики

		set_error_handler(array('Eresus_Kernel', 'errorHandler'));
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Error handler installed');

		//set_exception_handler('Core::handleException');
		//Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Exception handler installed');

		/*
		 * В PHP нет стандартных методов для перехвата некоторых типов ошибок (например E_PARSE или
		 * E_ERROR), однако способ всё же есть — зарегистрировать функцию через ob_start.
		 * Но только не в режиме CLI.
		 */
		// @codeCoverageIgnoreStart
		if (! Eresus_Kernel_PHP::isCLI())
		{
			if (ob_start(array('Eresus_Kernel', 'fatalErrorHandler'), 4096))
			{
				Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Fatal error handler installed');
			}
			else
			{
				Eresus_Logger::log(
					LOG_NOTICE, __METHOD__,
					'Fatal error handler not instaled! Fatal error will be not handled!'
				);
			}
		}
		// @codeCoverageIgnoreEnd
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработчик ошибок
	 *
	 * @param int    $errno       тип ошибки
	 * @param string $errstr      описание ошибки
	 * @param string $errfile     имя файла в котором произошла ошибка
	 * @param int    $errline     строка где произошла ошибка
	 * @param array  $errcontext  контекст ошибки
	 *
	 * @return bool
	 */
	public static function errorHandler($errno, $errstr, $errfile, $errline)
	{
		/* Нулевое значение 'error_reporting' означает что был использован оператор "@" */
		if (error_reporting() == 0)
		{
			return true;
		}

		/*
		 *  Примечание: На самом деле этот метод обрабатывает только E_WARNING, E_NOTICE, E_USER_ERROR,
		 *  E_USER_WARNING, E_USER_NOTICE и E_STRICT
		 */

		/* Определяем серьёзность ошибки */
		switch ($errno)
		{
			case E_STRICT:
			case E_NOTICE:
			case E_USER_NOTICE:
				$level = LOG_NOTICE;
			break;
			case E_WARNING:
			case E_USER_WARNING:
				$level = LOG_WARNING;
			break;
			default:
				$level = LOG_ERR;
			break;
		}

		if ($level < LOG_NOTICE)
		{
			throw new ErrorException($errstr, $errno, $level, $errfile, $errline);
		}
		else
		{
			$logMessage = sprintf(
				"%s in %s:%s",
				$errstr,
				$errfile,
				$errline
			);
			Eresus_Logger::log(__FUNCTION__, $level, $logMessage);
		}

		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработчик фатальных ошибок
	 *
	 * Замечание по производительности: этот метод освобождает в начале и выделет в конце своей работы
	 * буфер в памяти для отлова ошибок переполнения памяти. Эти операции затормаживают вывод примерно
	 * на 1-2%.
	 */
	public static function fatalErrorHandler($output)
	{
		// Освобождает резервный буфер
		unset($GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER']);
		if (preg_match('/(parse|fatal) error:.*in .* on line/Ui', $output, $m))
		{
			$GLOBALS['ERESUS_CORE_FATAL_ERROR_HANDLER'] = true;
			switch(strtolower($m[1]))
			{
				case 'fatal':
					$priority = LOG_CRIT;
					$message = 'FATAL ERROR';
				break;

				case 'parse':
					$priority = LOG_EMERG;
					$message = 'PARSE ERROR';
				break;
			}

			Eresus_Logger::log(__FUNCTION__, $priority, trim($output));
			if (!Eresus_Kernel_PHP::isCLI())
			{
				header('Internal Server Error', true, 500);
				header('Content-type: text/plain', true);
			}

			return $message . "\nSee application log for more info.\n";
		}
		$GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER'] =
			str_repeat('x', self::MEMORY_OVERFLOW_BUFFER_SIZE * 1024);

		// возвращаем fase для вывода буфера
		return false;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Автозагрузка классов
	 *
	 * Работает только для классов "Eresus_*". Все символы в имени класса "_" заменяются на
	 * разделитель директорий и добавляется префикс ".php".
	 *
	 * @param string $className
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	public static function autoload($className)
	{
		/* Устаревшие классы */
		$legacy = array(

			'EresusExtensionConnector' => 'classes/EresusExtensionConnector.php',
			'EresusForm' => 'EresusForm.php',
			'WebPage' => 'classes/WebPage.php',

			/* BusinessLogic */
			'ContentPlugin' => 'BusinessLogic/ContentPlugin.php',
			'EresusAdminFrontController' => 'BusinessLogic/EresusAdminFrontController.php',

			/* Domain */
			'Plugins' => 'classes/Plugins.php',

			/* UI */
			'AdminUI' => 'UI/AdminUI.php',
			'EresusFileManager' => 'UI/EresusFileManager.php',

			/* Сторонние компоненты */
			'elFinderConnector' => '../ext-3rd/elfinder/eresus-connector.php',
			'elFinder' => '../ext-3rd/elfinder/connectors/php/elFinder.class.php',

			/* Обратная совместимость */
			'EresusAccounts' => 'lib/accounts.php',
			'PaginationHelper' => 'classes/backward/PaginationHelper.php',
		);

		if (isset($legacy[$className]))
		{
			$fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . $legacy[$className];
		}
		else
		{
			if (stripos($className, 'Eresus_') !== 0 ||
				class_exists($className, false) ||
				interface_exists($className, false))
			{
				return false;
			}

			$fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR .
				str_replace('_', DIRECTORY_SEPARATOR, substr($className, 7)) . '.php';
		}

		if (file_exists($fileName))
		{
			include $fileName;
			return true;
		}

		return false;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает true если PHP запущен на UNIX-подобной ОС
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	static function isUnixLike()
	{
		return DIRECTORY_SEPARATOR == '/';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает true если PHP запущен на Microsoft Windows
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	static function isWindows()
	{
		return strncasecmp(PHP_OS, 'WIN', 3) == 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает true если PHP запущен на MacOS
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	static function isMac()
	{
		return strncasecmp(PHP_OS, 'MAC', 3) == 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Создаёт экземпляр приложения и выполняет его
	 *
	 * Класс приложения должен содержать публичный метод main().
	 *
	 * @param string $class  Имя класса приложения.
	 * @return int  Код завершения (0 — успешное завершение)
	 *
	 * @see $app, app()
	 */
	static public function exec($class)
	{
		if (!class_exists($class))
		{
			throw new LogicException('Application class "' . $class . '" does not exists');
		}

		self::$app = new $class();

		if (!method_exists($class, 'main'))
		{
			self::$app = null;
			throw new LogicException('Method "main()" does not exists in "' . $class . '"');
		}

		try
		{
			Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'executing %s', $class);
			$exitCode = self::$app->main();
			Eresus_Logger::log(__METHOD__, LOG_DEBUG, '%s done with code: %d', $class, $exitCode);
		}
		catch (SuccessException $e)
		{
			$exitCode = 0;
		}
		catch (Exception $e)
		{
			//FIXME Заменить на self::
			//Core::handleException($e);
			$exitCode = $e->getCode() ? $e->getCode() : 0xFFFF;
		}
		self::$app = null;
		return $exitCode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает выполняемое приложение или null, если приложение не запущено
	 *
	 * @return EresusApplication
	 *
	 * @see $app, exec(), EresusApplication
	 */
	static public function app()
	{
		return self::$app;
	}
	//-----------------------------------------------------------------------------
}
