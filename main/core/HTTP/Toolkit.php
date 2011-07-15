<?php
/**
 * ${product.title}
 *
 * Дополнительный инструментарий HTTP
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
 * Дополнительный инструментарий HTTP
 *
 * @package Eresus
 */
class Eresus_HTTP_Toolkit
{
	/**
	 * Заменить каждую часть первого URL соответствующей частью из второго URL
	 *
	 * @var int
	 */
	const URL_REPLACE = 1;

	/**
	 * Объединить относительные пути
	 *
	 * @var int
	 */
	const URL_JOIN_PATH = 2;

	/**
	 * Объеднить строки запроса
	 *
	 * @var int
	 */
	const URL_JOIN_QUERY = 4;

	/**
	 * Удалить имя пользователя
	 *
	 * @var int
	 */
	const URL_STRIP_USER = 8;

	/**
	 * Удалить пароль
	 *
	 * @var int
	 */
	const URL_STRIP_PASS = 16;

	/**
	 * Удалить авторизационную информацию
	 *
	 * @var int
	 */
	const URL_STRIP_AUTH = 32;

	/**
	 * Удалить номер порта
	 *
	 * @var int
	 */
	const URL_STRIP_PORT = 64;

	/**
	 * Полностью удалить путь
	 *
	 * @var int
	 */
	const URL_STRIP_PATH = 128;

	/**
	 * Удалить строку запроса
	 *
	 * @var int
	 */
	const URL_STRIP_QUERY = 256;

	/**
	 * Удалить фрагмент (#идентификатор)
	 *
	 * @var int
	 */
	const URL_STRIP_FRAGMENT = 512;

	/**
	 * Удалить всё кроме схемы и хоста
	 *
	 * @var int
	 */
	const URL_STRIP_ALL = 1024;

	/**
	 * Строит URL
	 *
	 * Части второго URL будут объединены с первым в соответствии с флагами.
	 *
	 * @param	 mixed $url    URL (или его части) в виде строки или ассоциативного массива, как
	 *                       возвращает {@link parse_url() parse_url()}
	 * @param	 mixed $parts  То же, что и для первого аргумента
	 * @param	 int   $flags  Битовая маска из констант URL_*. По умолчанию {@link URL_REPLACE}
	 *
	 * @author tycoonmaster(at)gmail(dot)com
	 * @author Михаил Красильников <mihalych@vsepofigu.ru>
	 */
	public static function buildURL($url, $parts = array(), $flags = self::URL_REPLACE)
	{
		$keys = array('user','pass','port','path','query','fragment');

		/* HTTP::URL_STRIP_ALL becomes all the HTTP::URL_STRIP_Xs */
		if ($flags & self::URL_STRIP_ALL)
		{
			$flags |= self::URL_STRIP_USER;
			$flags |= self::URL_STRIP_PASS;
			$flags |= self::URL_STRIP_PORT;
			$flags |= self::URL_STRIP_PATH;
			$flags |= self::URL_STRIP_QUERY;
			$flags |= self::URL_STRIP_FRAGMENT;
		}
		/* HTTP::URL_STRIP_AUTH becomes HTTP::URL_STRIP_USER and HTTP::URL_STRIP_PASS */
		else if ($flags & self::URL_STRIP_AUTH)
		{
			$flags |= self::URL_STRIP_USER;
			$flags |= self::URL_STRIP_PASS;
		}

		// Parse the original URL
		$parse_url = parse_url($url);

		// Scheme and Host are always replaced
		if (isset($parts['scheme']))
		{
			$parse_url['scheme'] = $parts['scheme'];
		}
		if (isset($parts['host']))
		{
			$parse_url['host'] = $parts['host'];
		}

		// (If applicable) Replace the original URL with it's new parts
		if ($flags & self::URL_REPLACE)
		{
			foreach ($keys as $key)
			{
				if (isset($parts[$key]))
				{
					$parse_url[$key] = $parts[$key];
				}
			}
		}
		else
		{
			// Join the original URL path with the new path
			if (isset($parts['path']) && ($flags & self::URL_JOIN_PATH))
			{
				if (isset($parse_url['path']))
				{
					$parse_url['path'] = rtrim(str_replace(basename($parse_url['path']), '',
						$parse_url['path']), '/') . '/' . ltrim($parts['path'], '/');
				}
				else
				{
					$parse_url['path'] = $parts['path'];
				}
			}

			// Join the original query string with the new query string
			if (isset($parts['query']) && ($flags & self::URL_JOIN_QUERY))
			{
				if (isset($parse_url['query']))
				{
					$parse_url['query'] .= '&' . $parts['query'];
				}
				else
				{
					$parse_url['query'] = $parts['query'];
				}
			}
		}

		// Strips all the applicable sections of the URL
		// Note: Scheme and Host are never stripped
		foreach ($keys as $key)
		{
			if ($flags & (int) constant('Eresus_HTTP_Toolkit::URL_STRIP_' . strtoupper($key)))
			{
				unset($parse_url[$key]);
			}
		}

		return
			((isset($parse_url['scheme'])) ? $parse_url['scheme'] . '://' : '') .
			((isset($parse_url['user'])) ? $parse_url['user'] . ((isset($parse_url['pass'])) ? ':' .
			$parse_url['pass'] : '') .'@' : '') .
			((isset($parse_url['host'])) ? $parse_url['host'] : '') .
			((isset($parse_url['port'])) ? ':' . $parse_url['port'] : '') .
			((isset($parse_url['path'])) ? $parse_url['path'] : '') .
			((isset($parse_url['query'])) ? '?' . $parse_url['query'] : '') .
			((isset($parse_url['fragment'])) ? '#' . $parse_url['fragment'] : '');
	}
	//-----------------------------------------------------------------------------
}
//-----------------------------------------------------------------------------

