<?php
/**
 * ${product.title} ${product.version}
 *
 * Дополнительные средства ядра для работы с PHP
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
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
 * Дополнительные средства ядра для работы с PHP
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_Kernel_PHP
{
	/**
	 * Список директорий open_basedir
	 *
	 * @var array
	 */
	private static $open_basedir;

	/**
	 * Проверяет, находится ли путь в списке open_basedir
	 *
	 * Если опция {@link http://php.net/open_basedir open_basedir} не установлена, всегда возвращает
	 * true.
	 *
	 * @param string $path  проверяемый путь
	 *
	 * @return bool true если $path находится среди разрешений open_basedir
	 *
	 * @since 2.16
	 */
	public static function inOpenBaseDir($path)
	{
		// Вторым аргументом в целях тестирования можно переопределить значение open_basedir
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
}
