<?php
/**
 * ${product.title}
 *
 * Ядро
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
 * Исключительная ситуация, не связанная с ошибкой
 *
 * @package Eresus
 * @since 2.17
 */
class Eresus_SuccessException extends Exception {}


/**
 * Исключительная ситуация, не связанная с ошибкой, требующая завершения приложения
 *
 * @package Eresus
 * @since 2.17
 */
class Eresus_ExitException extends Eresus_SuccessException {}


/**
 * Ядро CMS
 *
 * Основные функции ядра
 * 1. запуск {@link Eresus_CMS основного класса приложения};
 * 2. перехват ошибок и исключений;
 * 3. {@link autoload() автозагрузка классов};
 * 4. получение основных сведений о системе.
 *
 * @package Eresus
 * @since 2.17
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
	 * Контейнер служб
	 *
	 * @var sfServiceContainerBuilder
	 */
	private static $sc;

	/**
	 * Для тестирования
	 *
	 * @var bool
	 * @ignore
	 */
	private static $override_isCLI = null;

	/**
	 * Инициализация ядра
	 *
	 * Этот метод:
	 * 1. устанавливает временну́ю зону;
	 * 2. регистрирует {@link autoload() автозагрузчик классов};
	 * 3. регистрирует {@link initExceptionHandling() перехватчики ошибок}.
	 *
	 * @return void
	 *
	 * @since 2.17
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

		// Устанавливаем кодировку по умолчанию для опрераций mb_*
		mb_internal_encoding('utf-8');

		// Регистрация автозагрузчика классов
		spl_autoload_register(array('Eresus_Kernel', 'autoload'));

		self::initExceptionHandling();

		/* Отключение закавычивания передаваемых данных */
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			set_magic_quotes_runtime(0);
		}

		require_once dirname(__FILE__) .
			'/symfony/dependency-injection/sfServiceContainerAutoloader.php';
		sfServiceContainerAutoloader::register();
		self::$sc = new sfServiceContainerBuilder();

		self::$inited = true;
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

	/**
	 * Инициализирует обработчики ошибок
	 *
	 * Этот метод:
	 * 1. резервирует в памяти буфер, освобождаемый для обработки ошибок нехватки памяти;
	 * 2. отключает HTML-оформление стандартных сообщенй об ошибках;
	 * 3. регистрирует {@link errorHandler()};
	 * 4. регистрирует {@link fatalErrorHandler()}.
	 *
	 * @return void
	 *
	 * @since 2.17
	 * @uses Eresus_Logger::log()
	 */
	static private function initExceptionHandling()
	{
		/* Резервируем буфер на случай переполнения памяти */
		$GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER'] =
			str_repeat('x', self::MEMORY_OVERFLOW_BUFFER_SIZE * 1024);

		/* Меняем значения php.ini */
		ini_set('html_errors', 0); // Немного косметики

		set_error_handler(array('Eresus_Kernel', 'errorHandler'));
		//Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Error handler installed');

		//set_exception_handler('Eresus_Kernel::handleException');
		//Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Exception handler installed');

		/*
		 * В PHP нет стандартных методов для перехвата некоторых типов ошибок (например E_PARSE или
		 * E_ERROR), однако способ всё же есть — зарегистрировать функцию через ob_start.
		 * Но только не в режиме CLI.
		 */
		// @codeCoverageIgnoreStart
		if (! self::isCLI())
		{
			if (ob_start(array('Eresus_Kernel', 'fatalErrorHandler'), 4096))
			{
				//Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Fatal error handler installed');
			}
			else
			{
				/*Eresus_Logger::log(
					LOG_NOTICE, __METHOD__,
					'Fatal error handler not instaled! Fatal error will be not handled!'
				);*/
			}
		}
		// @codeCoverageIgnoreEnd
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработчик ошибок
	 *
	 * Обработчик ошибок, устанавливаемый через {@link set_error_handler() set_error_handler()} в
	 * методе {@link initExceptionHandling()}. Все ошибки важнее E_NOTICE превращаются в исключения
	 * {@link http://php.net/ErrorException ErrorException}, остальные передаются
	 * {@link Eresus_Logger::log()}.
	 *
	 * @param int    $errno    тип ошибки
	 * @param string $errstr   описание ошибки
	 * @param string $errfile  имя файла, в котором произошла ошибка
	 * @param int    $errline  строка, где произошла ошибка
	 *
	 * @return bool
	 *
	 * @since 2.17
	 * @uses Eresus_Logger::log()
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
			//Eresus_Logger::log(__FUNCTION__, $level, $logMessage);
		}

		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработчик фатальных ошибок
	 *
	 * Этот обработчик пытается перехватывать сообщения о фатальных ошибках, недоступных при
	 * использовании {@link set_error_handler() set_error_handler()}. Это делается через обработчик
	 * {@link ob_start() ob_start()}, устанавливаемый в методе {@link initExceptionHandling()}.
	 *
	 * <i>Замечание по производительности</i>: этот метод освобождает в начале и выделет в конце
	 * своей работы буфер в памяти для отлова ошибок переполнения памяти. Эти операции затормаживают
	 * вывод примерно на 1-2%.
	 *
	 * @param string $output  содержимое буфера вывода
	 *
	 * @return string|false
	 *
	 * @since 2.17
	 * @uses Eresus_Logger::log
	 */
	public static function fatalErrorHandler($output)
	{
		// Освобождает резервный буфер
		unset($GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER']);
		if (preg_match('/(parse|fatal) error:.*in .* on line/Ui', $output, $m))
		{
			$GLOBALS['ERESUS_CORE_FATAL_ERROR_HANDLER'] = true;
			switch (strtolower($m[1]))
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

			//Eresus_Logger::log(__FUNCTION__, $priority, trim($output));
			if (!self::isCLI())
			//@codeCoverageIgnoreStart
			{
				header('Internal Server Error', true, 500);
				header('Content-type: text/plain', true);
			}
			//@codeCoverageIgnoreEnd

			return $message . "\nSee application log for more info.\n";
		}
		$GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER'] =
			str_repeat('x', self::MEMORY_OVERFLOW_BUFFER_SIZE * 1024);

		// возвращаем false для вывода буфера
		return false;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Автозагрузчик классов
	 *
	 * Работает только для классов «Eresus_*». Из имени класса удаляется префикс «Eresus_», все
	 * символы в имени класса «_» заменяются на разделитель директорий, добавляется суффикс «.php».
	 *
	 * Таким образом класс «Eresus_HTTP_Request» будет искаться в файле «core/HTTP/Request.php».
	 *
	 * Устанавливается через {@link spl_autoload_register() spl_autoload_register()} в методе
	 * {@link init()}.
	 *
	 * @param string $className
	 *
	 * @throws LogicException если класс не найден
	 *
	 * @return bool
	 *
	 * @since 2.17
	 * @uses classExists()
	 */
	public static function autoload($className)
	{
		/*
		 * Классы Eresus
		 */
		if (stripos($className, 'Eresus_') === 0)
		{
			$fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR .
				str_replace('_', DIRECTORY_SEPARATOR, substr($className, 7)) . '.php';

			if (file_exists($fileName))
			{
				include $fileName;
				return self::classExists($className);
			}
			/*
			 * Doctrine при загрузке сущностей ищет необязательный класс с суффиксом «Table».
			 * Отсутствие такого класса не является ошибкой. Отсутствие любого другого класса расцениваем
			 * как логическую ошибку.
			 */
			elseif (substr($className, -5) !== 'Table')
			{
				throw new LogicException('Class "' . $className . '" not found');
			}
		}

		/*
		 * Классы Botobor
		 */
		if (stripos($className, 'Botobor') === 0)
		{
			$fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'libbotobor' . DIRECTORY_SEPARATOR .
				'libbotobor.php';

			if (file_exists($fileName))
			{
				include $fileName;
				return self::classExists($className);
			}
		}

		return false;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает true если PHP запущен на UNIX-подобной ОС
	 *
	 * @return bool
	 *
	 * @since 2.17
	 */
	static function isUnixLike()
	{
		return DIRECTORY_SEPARATOR == '/';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает true если PHP запущен на Microsoft® Windows™
	 *
	 * @return bool
	 *
	 * @since 2.17
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
	 * @since 2.17
	 */
	static function isMac()
	{
		return strncasecmp(PHP_OS, 'MAC', 3) == 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает true, если используется
	 * {@link http://php.net/manual/en/features.commandline.php CLI}
	 * {@link http://php.net/manual/en/function.php-sapi-name.php SAPI}
	 *
	 * @return bool
	 *
	 * @since 2.17
	 */
	static function isCLI()
	{
		//@codeCoverageIgnoreStart
		if (self::$override_isCLI !== null)
		{
			return self::$override_isCLI;
		}
		//@codeCoverageIgnoreEnd

		return PHP_SAPI == 'cli';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает true, если используется CGI
	 * {@link http://php.net/manual/en/function.php-sapi-name.php SAPI}
	 *
	 * @return bool
	 *
	 * @since 2.17
	 */
	static function isCGI()
	{
		return strncasecmp(PHP_SAPI, 'CGI', 3) == 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает true, если используется
	 * {@link http://php.net/manual/en/function.php-sapi-name.php SAPI} модуля веб-сервера
	 *
	 * @return bool
	 *
	 * @since 2.17
	 */
	static function isModule()
	{
		return !self::isCGI() && isset($_SERVER['GATEWAY_INTERFACE']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет, объявлен ли указанный класс или интерфейс
	 *
	 * Этот метод не инициирует автозагрузку.
	 *
	 * @param string $name  имя класса или интерфейса
	 * @return bool true если класс или интерфейс $name объявлен
	 *
	 * @since 2.17
	 */
	static public function classExists($name)
	{
		return class_exists($name, false) || interface_exists($name, false);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Создаёт экземпляр приложения и выполняет его
	 *
	 * Класс приложения должен содержать публичный метод main(), который будет вызван после создания
	 * экземпляра класса.
	 *
	 * @param string $class  имя класса приложения
	 *
	 * @throws LogicException если класс $class не найден или не содержит метода «main()»
	 *
	 * @return int  код завершения (0 — успешное завершение)
	 *
	 * @since 2.17
	 * @uses Eresus_Logger::log()
	 */
	static public function exec($class)
	{
		if (!class_exists($class))
		{
			throw new LogicException('Application class "' . $class . '" does not exists');
		}

		$app = new $class();

		if (!method_exists($class, 'main'))
		{
			throw new LogicException('Method "main()" does not exists in "' . $class . '"');
		}

		try
		{
			//Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'executing %s', $class);
			self::sc()->setService('app', $app);
			$exitCode = $app->main();
			//Eresus_Logger::log(__METHOD__, LOG_DEBUG, '%s done with code: %d', $class, $exitCode);
		}
		catch (Eresus_SuccessException $e)
		{
			$exitCode = 0;
		}
		catch (Exception $e)
		{
			//self::handleException($e);
			$exitCode = $e->getCode() ? $e->getCode() : 0xFFFF;
		}
		return $exitCode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает контейнер служб
	 *
	 * @return sfServiceContainerBuilder
	 *
	 * @since 2.17
	 */
	public static function sc()
	{
		return self::$sc;
	}
	//-----------------------------------------------------------------------------
}
