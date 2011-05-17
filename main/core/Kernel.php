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

		self::$inited = true;
	}
	// @codeCoverageIgnoreEnd
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
		if (stripos($className, 'Eresus_') !== 0 ||
			class_exists($className, false) ||
			interface_exists($className, false))
		{
			return false;
		}

		$fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR .
			str_replace('_', DIRECTORY_SEPARATOR, substr($className, 7)) . '.php';

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
	 * @param string $class  Имя класса приложения. Класс должен быть унаследован от
	 *                       {@link EresusApplication} FIXME Создать класс Eresus_Application
	 * @return int  Код завершения (0 — успешное завершение)
	 *
	 * @see $app, app(), EresusApplication
	 */
	static public function exec($class)
	{
		if (!class_exists($class))
		{
			throw new LogicException('Application class "' . $class . '" does not exists');
		}

		if (!is_subclass_of($class, 'EresusApplication'))
		{
			throw new LogicException('Application "' . $class .
				'" must be descendant of EresusApplication');
		}

		self::$app = new $class();

		try
		{
			EresusLogger::log(__METHOD__, LOG_DEBUG, 'executing %s', $class);
			$exitCode = self::$app->main();
			EresusLogger::log(__METHOD__, LOG_DEBUG, '%s done with code: %d', $class, $exitCode);
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
