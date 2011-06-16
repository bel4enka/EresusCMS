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
require_once dirname(__FILE__) . '/../../../../main/core/DB/Record.php';
require_once dirname(__FILE__) . '/../../../../main/core/WebServer.php';
require_once dirname(__FILE__) . '/../../../../main/core/CMS.php';
require_once dirname(__FILE__) . '/../../../../main/core/Config.php';
require_once dirname(__FILE__) . '/../../../../main/core/Kernel.php';
require_once dirname(__FILE__) . '/../../../../main/core/HTTP/Message.php';
require_once dirname(__FILE__) . '/../../../../main/core/Model/Site.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_Model_Site_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Model_Site::detectRootURL
	 * @covers Eresus_Model_Site::getRootURL
	 */
	public function test_detectRootURL()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		/* Подменяем DOCUMENT_ROOT */
		$webServer = Eresus_WebServer::getInstance();
		$documentRoot = new ReflectionProperty('Eresus_WebServer', 'documentRoot');
		$documentRoot->setAccessible(true);
		$documentRoot->setValue($webServer, '/home/user/public_html');

		$Eresus_CMS = $this->getMock('stdClass', array('getRootDir', 'getRequest'));
		$Eresus_CMS->expects($this->any())->method('getRootDir')->
			will($this->returnValue('/home/user/public_html'));

		$obj = new Eresus_Model_Site;

		$app = new ReflectionProperty('Eresus_Kernel', 'app');
		$app->setAccessible(true);
		$app->setValue('Eresus_Kernel', $Eresus_CMS);

		$_SERVER['HTTP_HOST'] = 'example.org';

		$this->assertEquals('http://example.org/', $obj->getRootURL());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Model_Site::detectRootURL
	 */
	public function test_detectRootURL_notRoot()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		/* Подменяем DOCUMENT_ROOT */
		$webServer = Eresus_WebServer::getInstance();
		$documentRoot = new ReflectionProperty('Eresus_WebServer', 'documentRoot');
		$documentRoot->setAccessible(true);
		$documentRoot->setValue($webServer, '/home/user/public_html');

		$obj = new Eresus_Model_Site;

		$Eresus_CMS = $this->getMock('stdClass', array('getRootDir', 'getRequest'));
		$Eresus_CMS->expects($this->any())->method('getRootDir')->
			will($this->returnValue('/home/user/public_html/site'));

		$app = new ReflectionProperty('Eresus_Kernel', 'app');
		$app->setAccessible(true);
		$app->setValue('Eresus_Kernel', $Eresus_CMS);

		$_SERVER['HTTP_HOST'] = 'example.org';

		$this->assertEquals('http://example.org/site/', $obj->getRootURL());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Model_Site::detectRootURL
	 */
	public function test_detectRootURL_windows()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		/* Подменяем DOCUMENT_ROOT */
		$webServer = Eresus_WebServer::getInstance();
		$documentRoot = new ReflectionProperty('Eresus_WebServer', 'documentRoot');
		$documentRoot->setAccessible(true);
		$documentRoot->setValue($webServer, 'C:/Program Files/Apache Webserver/docs');

		$obj = new Eresus_Model_Site;

		$Eresus_CMS = $this->getMock('stdClass', array('getRootDir', 'getRequest'));
		$Eresus_CMS->expects($this->any())->method('getRootDir')->
			will($this->returnValue('C:/Program Files/Apache Webserver/docs/site'));

		$app = new ReflectionProperty('Eresus_Kernel', 'app');
		$app->setAccessible(true);
		$app->setValue('Eresus_Kernel', $Eresus_CMS);

		$_SERVER['HTTP_HOST'] = 'example.org';

		$this->assertEquals('http://example.org/site/', $obj->getRootURL());
	}
	//-----------------------------------------------------------------------------

	/* */
}
