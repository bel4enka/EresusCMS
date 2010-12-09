<?php
/**
 * ${product.title}
 *
 * @version ${product.version}
 *
 * PhpUnit Tests
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EresusCMS
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

if (class_exists('PHP_CodeCoverage_Filter', false))
{
	PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);
}
else
{
	PHPUnit_Util_Filter::addFileToFilter(__FILE__);
}



/**
 * Универсальная заглушка
 *
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.16
 */
class UniversalStub implements ArrayAccess
{
	public function __get($a)
	{
		return $this;
	}
	//-----------------------------------------------------------------------------

	public function __call($a, $b)
	{
		return $this;
	}
	//-----------------------------------------------------------------------------

	public function offsetExists($offset)
	{
		return true;
	}
	//-----------------------------------------------------------------------------

	public function offsetGet($offset)
	{
		return $this;
	}
	//-----------------------------------------------------------------------------

	public function offsetSet($offset, $value)
	{
		;
	}
	//-----------------------------------------------------------------------------

	public function offsetUnset($offset)
	{
		;
	}
	//-----------------------------------------------------------------------------
}



/**
 * Фасад к моку для эмуляции статичных методов
 *
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.16
 */
class MockFacade
{
	/**
	 * Мок
	 *
	 * @var object
	 */
	private static $mock;

	/**
	 * Устанавливает мок
	 *
	 * @param object $mock
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public static function setMock($mock)
	{
		self::$mock = $mock;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Вызывает метод мока
	 *
	 * @param string $method
	 * @param array  $args
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public static function __callstatic($method, $args)
	{
		if (self::$mock && method_exists(self::$mock, $method))
		{
			return call_user_func_array(array(self::$mock, $method), $args);
		}

		return new UniversalStub();
	}
	//-----------------------------------------------------------------------------
}



define('errInvalidPassword', 'errInvalidPassword');
define('errAccountNotActive', 'errAccountNotActive');
define('errTooEarlyRelogin', 'errTooEarlyRelogin');
define('filesRoot', '/home/exmaple.org/');



class Doctrine_Core extends MockFacade {}
class Doctrine_Manager extends MockFacade {}
class Doctrine_Query {}
class Doctrine_Record {}
class Doctrine_Table {}
class EresusLogger extends MockFacade {}

/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class FS extends MockFacade
{
	public static function canonicalForm($filename)
	{
		/* Convert slashes */
    $filename = str_replace('\\', '/', $filename);

    /* Prepend drive letter with slash if needed */
    if (substr($filename, 1, 1) == ':')
      $filename = '/' . $filename;

    return $filename;
	}
	//-----------------------------------------------------------------------------

	public static function isFile($filename)
	{
    return is_file($filename);
	}
	//-----------------------------------------------------------------------------
}


/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class EresusRuntimeException extends Exception
{
}

/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class EresusApplication
{
	public $fsRoot;

	public function getFsRoot()
	{
		return $this->fsRoot;
	}
	//-----------------------------------------------------------------------------
}

/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class HttpRequest
{
	public $localRoot;

	public function setLocalRoot($value)
	{
		$this->localRoot = $value;
	}
	//-----------------------------------------------------------------------------

	public function getLocalRoot()
	{
		return $this->localRoot;
	}
	//-----------------------------------------------------------------------------

	public function getScheme()
	{
		return 'http';
	}
	//-----------------------------------------------------------------------------

	public function getHost()
	{
		return 'example.org';
	}
	//-----------------------------------------------------------------------------
}

/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class TemplateSettings
{
	public static function setGlobalValue($a, $b)
	{
		;
	}
	//-----------------------------------------------------------------------------
}

