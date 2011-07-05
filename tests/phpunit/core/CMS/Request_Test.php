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
require_once dirname(__FILE__) . '/../../../../main/core/HTTP/Message.php';
require_once dirname(__FILE__) . '/../../../../main/core/HTTP/Toolkit.php';
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
		$msg = new Eresus_HTTP_Message();
		$msg->setType(Eresus_HTTP_Message::TYPE_REQUEST);
		$msg->setRequestUri('http://example.org/dir1/dir2/dir3/file.ext');
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
		$msg = new Eresus_HTTP_Message();
		$msg->setType(Eresus_HTTP_Message::TYPE_REQUEST);

		$msg->setRequestUri('http://example.org/');
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('/', $test->getPathInfo());

		$msg->setRequestUri('http://example.org/dir1/dir2/file.ext?a=b');
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('/dir1/dir2/file.ext', $test->getPathInfo());

		$msg->setRequestUri('http://example.org/dir1/dir2/');
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('/dir1/dir2/', $test->getPathInfo());

		$msg->setRequestUri('http://example.org/site_root/');
		$test = new Eresus_CMS_Request($msg, '/site_root');
		$this->assertEquals('/', $test->getPathInfo());

		$msg->setRequestUri('http://example.org/site_root/dir1/dir2/file.ext?a=b');
		$test = new Eresus_CMS_Request($msg, '/site_root');
		$this->assertEquals('/dir1/dir2/file.ext', $test->getPathInfo());

		$msg->setRequestUri('http://example.org/site_root/dir1/dir2/');
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
		$msg = new Eresus_HTTP_Message();
		$msg->setType(Eresus_HTTP_Message::TYPE_REQUEST);

		$url = 'http://example.org/';
		$msg->setRequestUri($url);
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('', $test->getBasePath(), $url);

		$url = 'http://example.org/file.ext';
		$msg->setRequestUri($url);
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('', $test->getBasePath(), $url);

		$url = 'http://example.org/dir1/dir2/';
		$msg->setRequestUri($url);
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('/dir1/dir2', $test->getBasePath(), $url);

		$url = 'http://example.org/dir1/dir2/dir3/file.ext';
		$msg->setRequestUri($url);
		$test = new Eresus_CMS_Request($msg, '');
		$this->assertEquals('/dir1/dir2/dir3', $test->getBasePath(), $url);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Request::getFolder
	 */
	public function test_getFolder()
	{
		$msg = new Eresus_HTTP_Message();
		$msg->setType(Eresus_HTTP_Message::TYPE_REQUEST);

		$msg->setRequestUri('http://example.org/site_root/dir1/dir2/file.ext');
		$req = new Eresus_CMS_Request($msg, '/site_root');

		$this->assertEquals('dir1', $req->getFolder());
		$this->assertEquals('dir1', $req->getFolder());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Request::splitFolders
	 */
	public function test_splitFolders()
	{
		$m_splitFolders = new ReflectionMethod('Eresus_CMS_Request', 'splitFolders');
		$m_splitFolders->setAccessible(true);

		$p_folders = new ReflectionProperty('Eresus_CMS_Request', 'folders');
		$p_folders->setAccessible(true);

		$msg = new Eresus_HTTP_Message();
		$msg->setType(Eresus_HTTP_Message::TYPE_REQUEST);

		$msg->setRequestUri('http://example.org/site_root/dir1/dir2/file.ext');
		$req = new Eresus_CMS_Request($msg, '/site_root');

		$m_splitFolders->invoke($req);
		$this->assertEquals(array('dir1', 'dir2'), $p_folders->getValue($req));

		$msg->setRequestUri('http://example.org/site_root/');
		$req = new Eresus_CMS_Request($msg, '/site_root');

		$m_splitFolders->invoke($req);
		$this->assertEquals(array(), $p_folders->getValue($req));
	}
	//-----------------------------------------------------------------------------

	/* */
}
