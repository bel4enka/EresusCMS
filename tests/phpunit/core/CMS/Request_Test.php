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

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/HTTP/Request.php';
require_once dirname(__FILE__) . '/../../../../main/core/URI.php';
require_once dirname(__FILE__) . '/../../../../main/core/CMS/Request.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_CMS_Request_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_CMS_Request::__construct
	 * @covers Eresus_CMS_Request::getHttpMessage
	 */
	public function test_construct()
	{
		$msg = new Eresus_HTTP_Request();
		$msg->setUri('http://example.org/dir1/dir2/dir3/file.ext');
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertSame($msg, $test->getHttpMessage());
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
	 */
	public function test_getParam()
	{
		$msg = new Eresus_HTTP_Request();

		$msg->setUri('http://example.org/site_root/dir1/dir2/file.ext');
		$req = new Eresus_CMS_Request($msg, '/site_root');

		$this->assertEquals('dir1', $req->getParam());
		$this->assertEquals('dir1', $req->getParam());
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
