<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Дополнения PHP
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
 * $Id: CMS.php 1577 2011-05-17 14:12:30Z mk $
 */

/**
 * Интерфейс к интерпретатору PHP
 *
 * Часть функций взята из {@link http://limb-project.com/ Limb3 project}
 *
 * <b>Внимание!</b> Этот класс не должен использовать другие части Eresus
 *
 * @package Kernel
 * @since 2.16
 */
class Eresus_Kernel_PHP
{
	/**
	 * Список директорий open_basedir
	 *
	 * @var array
	 */
	protected static $open_basedir;

	/**
	 * Для тестирования
	 *
	 * @var bool
	 * @ignore
	 */
	private static $override_isCLI = null;

	/**
	 * Возвращает true, если используется CLI SAPI
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	static function isCLI()
	{
		if (self::$override_isCLI !== null)
		{
			return self::$override_isCLI;
		}

		return PHP_SAPI == 'cli';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает true, если используется CGI SAPI
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	static function isCGI()
	{
		return strncasecmp(PHP_SAPI, 'CGI', 3) == 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает true, если используется SAPI модуля веб-сервера
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	static function isModule()
	{
		return !self::isCGI() && isset($_SERVER['GATEWAY_INTERFACE']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет, находится ли путь в списке open_basedir
	 *
	 * Если опция open_basedir не установлена, всегда возвращает true.
	 *
	 * @param string $path  Путь для проверки
	 * @return bool
	 *
	 * @since 2.16
	 */
	public static function inOpenBaseDir($path)
	{
		// The second argument can be passed for testing purpose
		$open_basedir = func_num_args() > 1 ? func_get_arg(1) : ini_get('open_basedir');

		if ($open_basedir == false)
		{
			return true;
		}

		if (! self::$open_basedir)
		{
			self::$open_basedir = explode(PATH_SEPARATOR, $open_basedir);
		}

		if (substr($path, 0, 1) == '.')
		{
			$path = getcwd() . substr($path, 1);
		}

		foreach (self::$open_basedir as $dir)
		{
			if (substr($path, 0, strlen($dir)) == $dir)
			{
				return true;
			}
		}

		return false;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет, загружен ли указанный класс или интерфейс
	 *
	 * Этот метод не инициирует автозагрузку
	 *
	 * @param string $name  Имя класса или интерфейса
	 * @return bool
	 *
	 * @since 2.16
	 */
	static public function classExists($name)
	{
		return class_exists($name, false) || interface_exists($name, false);
	}
	//-----------------------------------------------------------------------------
}
