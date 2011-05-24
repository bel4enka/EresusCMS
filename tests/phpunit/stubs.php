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
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * $Id$
 */

PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

define('TESTS_SRC_ROOT', realpath(__DIR__ . '/../../main'));

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

	public function __toString()
	{
		return '';
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



define('errAccountNotActive', 'errAccountNotActive');
define('errError', 'errError');
define('errFileNotFound', 'errFileNotFound');
define('errInvalidPassword', 'errInvalidPassword');
define('errTooEarlyRelogin', 'errTooEarlyRelogin');
define('filesRoot', '/home/exmaple.org/');
define('CHARSET', 'cp1251');

class Doctrine extends MockFacade {}
class Doctrine_Core extends MockFacade
{
	const ATTR_AUTOLOAD_TABLE_CLASSES = 'ATTR_AUTOLOAD_TABLE_CLASSES';
	const ATTR_TBLNAME_FORMAT = 'ATTR_TBLNAME_FORMAT';
	const ATTR_VALIDATE = 'ATTR_VALIDATE';
	const VALIDATE_ALL = 'VALIDATE_ALL';
}


class Doctrine_Manager extends MockFacade {}
class Doctrine_Query {}
class Doctrine_Record {}
class Doctrine_Table {}
class Dwoo extends UniversalStub {}
class Dwoo_Template_File extends UniversalStub {}
class elFinder extends UniversalStub {}
class ezcMailAddress extends UniversalStub {}
class ezcMailComposer {}
class ezcMailTransport extends UniversalStub {}
class ezcMailMtaTransport extends ezcMailTransport {}



/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class EresusRuntimeException extends Exception {}

/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class ExitException extends SuccessException {}

/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.16
 */
class SuccessException extends Exception {}



/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.16
 */
class HTTP extends MockFacade
{
	public static $request;

	public static function request()
	{
		return self::$request;
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
 * @since 2.16
 */
class HttpResponse extends MockFacade {}
