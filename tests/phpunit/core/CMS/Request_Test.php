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

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/HTTP/Request.php';
require_once dirname(__FILE__) . '/../../../../main/core/URI.php';
require_once dirname(__FILE__) . '/../../../../main/core/CMS/Request.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMS_Request_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		Eresus_Config::drop('eresus.cms.http.host');
	}
	//-----------------------------------------------------------------------------
	/**
	 * @covers Eresus_CMS_Request::__construct
	 * @covers Eresus_CMS_Request::getHttpMessage
	 * @covers Eresus_CMS_Request::getRootPrefix
	 * @covers Eresus_CMS_Request::getRootURL
	 */
	public function test_construct()
	{
		$msg = new Eresus_HTTP_Request();
		$msg->setUri('http://example.org/dir1/dir2/dir3/file.ext');
		$test = new Eresus_CMS_Request($msg, '/dir1');
		$this->assertSame($msg, $test->getHttpMessage());
		$this->assertEquals('/dir1', $test->getRootPrefix());
		$this->assertEquals('http://example.org/dir1', strval($test->getRootURL()));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Request::getInstance
	 */
	public function test_getInstance()
	{
		$app = $this->getMock('stdClass', array('getRootDir'));
		Eresus_Tests::setStatic('Eresus_Kernel', $app, 'app');
		$req = Eresus_CMS_Request::getInstance();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Request::__call
	 */
	public function test_call()
	{
		$msg = new Eresus_HTTP_Request();
		$msg->setUri('http://example.org/dir1/dir2/dir3/file.ext');
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals($msg->getMethod(), $test->getMethod());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Request::__call
	 * @expectedException BadMethodCallException
	 */
	public function test_call_unexistent()
	{
		$msg = new Eresus_HTTP_Request();
		$test = new Eresus_CMS_Request($msg, '');
		$test->getUnexistent();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Request::isGET
	 * @covers Eresus_CMS_Request::isPOST
	 */
	public function test_isGET()
	{
		$msg = new Eresus_HTTP_Request();
		$msg->setMethod('GET');
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertTrue($test->isGET());
		$this->assertFalse($test->isPOST());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Request::getHost
	 */
	public function test_getHost()
	{
		$msg = new Eresus_HTTP_Request();
		$test = new Eresus_CMS_Request($msg, '');

		$msg->setHost('example.org');
		$this->assertEquals('example.org', $test->getHost());
		Eresus_Config::set('eresus.cms.http.host', 'example.com');
		$this->assertEquals('example.com', $test->getHost());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Request::isPOST
	 * @covers Eresus_CMS_Request::isGET
	 */
	public function test_isPOST()
	{
		$msg = new Eresus_HTTP_Request();
		$msg->setMethod('POST');
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertTrue($test->isPOST());
		$this->assertFalse($test->isGET());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Request::__construct
	 * @covers Eresus_CMS_Request::getPathInfo
	 */
	public function test_getPathInfo()
	{
		$msg = new Eresus_HTTP_Request();

		$msg->setUri('http://example.org/');
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('/', $test->getPathInfo());

		$msg->setUri('http://example.org/dir1/dir2/file.ext?a=b');
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('/dir1/dir2/file.ext', $test->getPathInfo());

		$msg->setUri('http://example.org/dir1/dir2/');
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('/dir1/dir2/', $test->getPathInfo());

		$msg->setUri('http://example.org/site_root/');
		$test = new Eresus_CMS_Request($msg, '/site_root');
		$this->assertEquals('/', $test->getPathInfo());

		$msg->setUri('http://example.org/site_root/dir1/dir2/file.ext?a=b');
		$test = new Eresus_CMS_Request($msg, '/site_root');
		$this->assertEquals('/dir1/dir2/file.ext', $test->getPathInfo());

		$msg->setUri('http://example.org/site_root/dir1/dir2/');
		$test = new Eresus_CMS_Request($msg, '/site_root');
		$this->assertEquals('/dir1/dir2/', $test->getPathInfo());

	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Request::__construct
	 * @covers Eresus_CMS_Request::getBasePath
	 */
	public function test_getBasePath()
	{
		$msg = new Eresus_HTTP_Request();

		$url = 'http://example.org/';
		$msg->setUri($url);
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('', $test->getBasePath(), $url);

		$url = 'http://example.org/file.ext';
		$msg->setUri($url);
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('', $test->getBasePath(), $url);

		$url = 'http://example.org/dir1/dir2/';
		$msg->setUri($url);
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('/dir1/dir2', $test->getBasePath(), $url);

		$url = 'http://example.org/dir1/dir2/dir3/file.ext';
		$msg->setUri($url);
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('/dir1/dir2/dir3', $test->getBasePath(), $url);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Request::getParam
	 * @covers Eresus_CMS_Request::getNextParam
	 */
	public function test_getParam()
	{
		$msg = new Eresus_HTTP_Request();

		$msg->setUri('http://example.org/site_root/dir1/dir2/file.ext');
		$req = new Eresus_CMS_Request($msg, '/site_root');

		$this->assertEquals('dir1', $req->getParam());
		$this->assertEquals('dir1', $req->getParam());
		$this->assertEquals('dir2', $req->getNextParam());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Request::getNextParam
	 */
	public function test_getNextParam()
	{
		$msg = new Eresus_HTTP_Request();

		$msg->setUri('http://example.org/site_root/dir1/dir2/file.ext');
		$req = new Eresus_CMS_Request($msg, '/site_root');

		$p_params = new ReflectionProperty('Eresus_CMS_Request', 'params');
		$p_params->setAccessible(true);
		$p_params->setValue($msg, null);

		$this->assertEquals('dir2', $req->getNextParam());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Request::splitParams
	 */
	public function test_splitParams()
	{
		$m_splitParams = new ReflectionMethod('Eresus_CMS_Request', 'splitParams');
		$m_splitParams->setAccessible(true);

		$p_params = new ReflectionProperty('Eresus_CMS_Request', 'params');
		$p_params->setAccessible(true);

		$msg = new Eresus_HTTP_Request();

		$msg->setUri('http://example.org/site_root/dir1/dir2/file.ext');
		$req = new Eresus_CMS_Request($msg, '/site_root');

		$m_splitParams->invoke($req);
		$this->assertEquals(array('dir1', 'dir2'), $p_params->getValue($req));

		$msg->setUri('http://example.org/site_root/');
		$req = new Eresus_CMS_Request($msg, '/site_root');

		$m_splitParams->invoke($req);
		$this->assertEquals(array(), $p_params->getValue($req));
	}
	//-----------------------------------------------------------------------------

	/* */
}
