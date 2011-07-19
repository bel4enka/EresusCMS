<?php
/**
 * ${product.title}
 *
 * Модульные тесты
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
 * @subpackage Tests
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/WebServer.php';
require_once dirname(__FILE__) . '/../../../../main/core/Kernel.php';
require_once dirname(__FILE__) . '/../../../../main/core/URI.php';
require_once dirname(__FILE__) . '/../../../../main/core/HTTP/Request.php';
require_once dirname(__FILE__) . '/../../../../main/core/HTTP/Request/Arguments.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_HTTP_Request_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_HTTP_Request::setHttpVersion
	 * @covers Eresus_HTTP_Request::getHttpVersion
	 */
	public function test_getsetHttpVersion()
	{
		$test = new Eresus_HTTP_Request();

		$this->assertTrue($test->setHttpVersion('1.0'));
		$this->assertEquals('1.0', $test->getHttpVersion());

		$this->assertTrue($test->setHttpVersion('1.1'));
		$this->assertEquals('1.1', $test->getHttpVersion());

		$this->assertFalse($test->setHttpVersion('1.2'));
		$this->assertEquals('1.1', $test->getHttpVersion());

	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request::setMethod
	 * @covers Eresus_HTTP_Request::getMethod
	 */
	public function test_setfetMethod()
	{
		$test = new Eresus_HTTP_Request();

		$this->assertTrue($test->setMethod('GET'), 'GET');
		$this->assertEquals('GET', $test->getMethod());

		$this->assertTrue($test->setMethod('POST'), 'POST');
		$this->assertEquals('POST', $test->getMethod());

		$this->assertFalse($test->setMethod('XXX'), 'XXX');
		$this->assertEquals('POST', $test->getMethod());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request::setScheme
	 * @covers Eresus_HTTP_Request::getScheme
	 */
	public function test_getsetScheme()
	{
		$test = new Eresus_HTTP_Request();

		$test->setScheme('http');
		$this->assertEquals('http', $test->getScheme());

		$test->setScheme('https');
		$this->assertEquals('https', $test->getScheme());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request::setScheme
	 * @expectedException InvalidArgumentException
	 */
	public function test_setScheme_invalid()
	{
		$test = new Eresus_HTTP_Request();
		$test->setScheme('ftp');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request::setHost
	 * @covers Eresus_HTTP_Request::getHost
	 */
	public function test_getsetHost()
	{
		$test = new Eresus_HTTP_Request();

		$test->setHost('example.org');
		$this->assertEquals('example.org', $test->getHost());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request::setUri
	 * @covers Eresus_HTTP_Request::getUri
	 */
	public function test_setgetUri()
	{
		$test = new Eresus_HTTP_Request();

		$test->setUri('http://example.org/');
		$this->assertEquals('http://example.org/', $test->getUri());

		$test->setHost('example.com');
		$this->assertEquals('http://example.com/', $test->getUri());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request::setUri
	 * @expectedException InvalidArgumentException
	 */
	public function test_setUri_non_string()
	{
		$test = new Eresus_HTTP_Request();

		$test->setUri(array());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request::getPath
	 */
	public function test_getPath()
	{
		$test = new Eresus_HTTP_Request();

		$test->setUri('http://example.org/some/path/to/file?a=b');
		$this->assertEquals('/some/path/to/file', $test->getPath());

		$test->setUri('http://example.org/some/path/');
		$this->assertEquals('/some/path/', $test->getPath());

		$test->setUri('/some/path/');
		$this->assertEquals('/some/path/', $test->getPath());

		$test->setUri('some/path/to/file');
		$this->assertEquals('some/path/to/file', $test->getPath());

	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request::getHeader
	 */
	public function test_getHeader()
	{
		$test = new Eresus_HTTP_Request();

		$p_headers = new ReflectionProperty('Eresus_HTTP_Request', 'headers');
		$p_headers->setAccessible(true);
		$p_headers->setValue($test, array('Host' => 'example.org'));

		$this->assertEquals('example.org', $test->getHeader('Host'));
		$this->assertNull($test->getHeader('X-Unknown'));

	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request::getQuery
	 */
	public function test_getQuery()
	{
		$test = new Eresus_HTTP_Request();

		$this->assertInstanceOf('Eresus_HTTP_Request_Arguments', $test->getQuery());

	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request::getPost
	 */
	public function test_getPost()
	{
		$test = new Eresus_HTTP_Request();

		$this->assertInstanceOf('Eresus_HTTP_Request_Arguments', $test->getPost());

	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request::fromEnv
	 */
	public function test_fromEnv()
	{
		$test = Eresus_HTTP_Request::fromEnv();

		$this->assertEquals('GET', $test->getMethod());
		$this->assertEquals('1.0', $test->getHttpVersion());
		$this->assertEquals('http://localhost/', $test->getUri());

		//-------------------

		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_SERVER['HTTPS'] = 'on';
		$_SERVER['HTTP_HOST'] = 'example.org';
		$_SERVER['REQUEST_METHOD'] = 'HEAD';
		$_SERVER['REQUEST_URI'] = '/dir1/dir2/file.ext?p1=v1&p2=v2';

		$test = Eresus_HTTP_Request::fromEnv();

		$this->assertEquals('HEAD', $test->getMethod());
		$this->assertEquals('1.1', $test->getHttpVersion());
		$this->assertEquals('https://example.org/dir1/dir2/file.ext?p1=v1&p2=v2', $test->getUri());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request::fromEnv
	 * @expectedException RuntimeException
	 */
	public function test_fromEnv_unexistentClass()
	{
		Eresus_HTTP_Request::fromEnv('Unexistent');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request::fromEnv
	 * @expectedException InvalidArgumentException
	 */
	public function test_fromEnv_invalidClass()
	{
		Eresus_HTTP_Request::fromEnv('stdClass');
	}
	//-----------------------------------------------------------------------------

	/* */
}
