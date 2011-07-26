<?php
/**
 * ${product.title} ${product.version}
 *
 * Модульные тесты
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
 * @subpackage Tests
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/URI.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_URI_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_URI::setScheme
	 * @covers Eresus_URI::getScheme
	 */
	public function test_getsetScheme()
	{
		$test = new Eresus_URI();

		// Простая схема
		$test->setScheme('http');
		$this->assertEquals('http', $test->getScheme());

		// С цифрами
		$test->setScheme('h323');
		$this->assertEquals('h323', $test->getScheme());

		// С точкой
		$test->setScheme('soap.beep');
		$this->assertEquals('soap.beep', $test->getScheme());

		// С минусом
		$test->setScheme('view-source');
		$this->assertEquals('view-source', $test->getScheme());

		// С плюсом
		$test->setScheme('a+b');
		$this->assertEquals('a+b', $test->getScheme());

	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_URI::setScheme
	 * @expectedException InvalidArgumentException
	 */
	public function test_setScheme_invalid()
	{
		$test = new Eresus_URI();
		$test->setScheme('+http');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_URI::setUserinfo
	 * @covers Eresus_URI::getUserinfo
	 */
	public function test_getsetUserinfo()
	{
		$test = new Eresus_URI();

		$test->setUserinfo('username');
		$this->assertEquals('username', $test->getUserinfo());

		$test->setUserinfo('username:password');
		$this->assertEquals('username:password', $test->getUserinfo());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_URI::setUserinfo
	 * @expectedException InvalidArgumentException
	 */
	public function test_setUserinfo_invalid()
	{
		$test = new Eresus_URI();
		$test->setUserinfo('#');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_URI::setHost
	 * @covers Eresus_URI::getHost
	 */
	public function test_getsetHost()
	{
		$test = new Eresus_URI();

		$test->setHost('example.org');
		$this->assertEquals('example.org', $test->getHost());

		$test->setHost('EXAMPLE.ORG');
		$this->assertEquals('example.org', $test->getHost());

		$test->setHost('127.0.0.1');
		$this->assertEquals('127.0.0.1', $test->getHost());

		$test->setHost('::1');
		$this->assertEquals('::1', $test->getHost());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_URI::setPort
	 * @covers Eresus_URI::getPort
	 */
	public function test_getsetPort()
	{
		$test = new Eresus_URI();

		$test->setPort(80);
		$this->assertEquals(80, $test->getPort());

		$test->setPort('8080');
		$this->assertEquals('8080', $test->getPort());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_URI::setPort
	 * @expectedException InvalidArgumentException
	 */
	public function test_setPort_invalid()
	{
		$test = new Eresus_URI();
		$test->setPort('abc');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_URI::setPath
	 * @covers Eresus_URI::getPath
	 */
	public function test_getsetPath()
	{
		$test = new Eresus_URI();

		$test->setPath('/some/path');
		$this->assertEquals('/some/path', $test->getPath());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_URI::setQuery
	 * @covers Eresus_URI::getQuery
	 */
	public function test_getsetQuery()
	{
		$test = new Eresus_URI();

		$test->setQuery('a=b&c=d');
		$this->assertEquals('b', $test->getQuery()->get('a'));
		$this->assertEquals('d', $test->getQuery()->get('c'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_URI::setFragment
	 * @covers Eresus_URI::getFragment
	 */
	public function test_getsetFragment()
	{
		$test = new Eresus_URI();

		$test->setFragment('bar');
		$this->assertEquals('bar', $test->getFragment());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_URI::replace
	 */
	public function test_replace()
	{
		$uri = new Eresus_URI('foo://some$info@example.org:123/some/path?a=b&c=d#bar');

		$uri->replace(array(
			'scheme' => 'http',
			'userinfo' => 'user:pass',
			'host' => 'example.com',
			'port' => 80,
			'path' => '/',
			'query' => 'key=value',
			'fragment' => 'foo'
		));
		$this->assertEquals('http', $uri->getScheme());
		$this->assertEquals('example.com', $uri->getHost());
		$this->assertEquals('user:pass', $uri->getUserinfo());
		$this->assertEquals(80, $uri->getPort());
		$this->assertEquals('/', $uri->getPath());
		$this->assertEquals('key=value', strval($uri->getQuery()));
		$this->assertEquals('foo', $uri->getFragment());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_URI::__construct
	 * @covers Eresus_URI::parseURI
	 */
	public function test_parseURI()
	{
		$uri = new Eresus_URI('foo://some$info@example.org:123/some/path?a=b&c=d#bar');

		$this->assertEquals('foo', $uri->getScheme());
		$this->assertEquals('example.org', $uri->getHost());
		$this->assertEquals('some$info', $uri->getUserinfo());
		$this->assertEquals(123, $uri->getPort());
		$this->assertEquals('/some/path', $uri->getPath());
		$this->assertEquals('a=b&c=d', strval($uri->getQuery()));
		$this->assertEquals('bar', $uri->getFragment());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_URI::__toString
	 */
	public function test_toString()
	{
		$uri = new Eresus_URI();
		$uri->setScheme('foo');
		$this->assertEquals('foo:', strval($uri));

		$uri = new Eresus_URI();
		$uri->setScheme('foo');
		$uri->setUserinfo('some$info');
		$uri->setHost('example.org');
		$uri->setPort(123);
		$uri->setPath('/some/path');
		$uri->setQuery('a=b&c=d');
		$uri->setFragment('bar');
		$this->assertEquals('foo://some$info@example.org:123/some/path?a=b&c=d#bar', strval($uri));

		$uri = new Eresus_URI();
		$uri->setScheme('urn');
		$uri->setPath('example:animal:ferret:nose');
		$this->assertEquals('urn:example:animal:ferret:nose', strval($uri));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_URI::setScheme
	 */
	public function test_unset_parts()
	{
		$uri = new Eresus_URI('foo://some$info@example.org:123/some/path?a=b&c=d#bar');
		$this->assertEquals('foo://some$info@example.org:123/some/path?a=b&c=d#bar', strval($uri));

		$uri->setFragment(null);
		$this->assertEquals('foo://some$info@example.org:123/some/path?a=b&c=d', strval($uri));

		$uri->setQuery(null);
		$this->assertEquals('foo://some$info@example.org:123/some/path', strval($uri));

		$uri->setPath(null);
		$this->assertEquals('foo://some$info@example.org:123', strval($uri));

		$uri->setPort(null);
		$this->assertEquals('foo://some$info@example.org', strval($uri));

		$uri->setUserinfo(null);
		$this->assertEquals('foo://example.org', strval($uri));

		$uri->setHost(null);
		$this->assertEquals('foo:', strval($uri));

		$uri->setScheme(null);
		$this->assertEquals('', strval($uri));
	}
	//-----------------------------------------------------------------------------

	/* */
}
