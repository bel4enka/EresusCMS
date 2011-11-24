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
 * @package Eresus
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/Application.php';
require_once TESTS_SRC_DIR . '/core/CMS.php';
require_once TESTS_SRC_DIR . '/core/classes/WebServer.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMS_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @api
	 * @covers Eresus_CMS::getVersion
	 */
	public function test_getVersion()
	{
		$cms = new Eresus_CMS;
		$this->assertEquals('${product.version}', $cms->getVersion());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @api
	 * @covers Eresus_CMS::getRootDir
	 */
	public function test_getRootDir()
	{
		$cms = new Eresus_CMS;

		$this->assertEquals(TESTS_SRC_DIR, $cms->getRootDir());

		$p_rootDir = new ReflectionProperty('Eresus_Application', 'rootDir');
		$p_rootDir->setAccessible(true);
		$p_rootDir->setValue($cms, '/home/example.org');

		$this->assertEquals('/home/example.org', $cms->getRootDir());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS::detectWebRoot
	 */
	public function test_detectWebRoot()
	{
		/* Подменяем DOCUMENT_ROOT */
		$webServer = WebServer::getInstance();
		$documentRoot = new ReflectionProperty('WebServer', 'documentRoot');
		$documentRoot->setAccessible(true);
		$documentRoot->setValue($webServer, '/home/user/public_html');

		$obj = new Eresus_CMS;
		// Подменяем результат getRootDir
		$p_rootDir = new ReflectionProperty('Eresus_Application', 'rootDir');
		$p_rootDir->setAccessible(true);
		$p_rootDir->setValue($obj, '/home/user/public_html');
		$httpRequest = new HttpRequest();

		$request = new ReflectionProperty('Eresus_CMS', 'request');
		$request->setAccessible(true);
		$request->setValue($obj, $httpRequest);

		$detectWebRoot = new ReflectionMethod('Eresus_CMS', 'detectWebRoot');
		$detectWebRoot->setAccessible(true);
		$detectWebRoot->invoke($obj);

		$this->assertEquals('', $httpRequest->localRoot);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS::detectWebRoot
	 */
	public function test_detectWebRoot_notRoot()
	{
		/* Подменяем DOCUMENT_ROOT */
		$webServer = WebServer::getInstance();
		$documentRoot = new ReflectionProperty('WebServer', 'documentRoot');
		$documentRoot->setAccessible(true);
		$documentRoot->setValue($webServer, '/home/user/public_html');

		$obj = new Eresus_CMS;
		// Подменяем результат getRootDir
		$p_rootDir = new ReflectionProperty('Eresus_Application', 'rootDir');
		$p_rootDir->setAccessible(true);
		$p_rootDir->setValue($obj, '/home/user/public_html/example.org');
		$httpRequest = new HttpRequest();

		$request = new ReflectionProperty('Eresus_CMS', 'request');
		$request->setAccessible(true);
		$request->setValue($obj, $httpRequest);

		$detectWebRoot = new ReflectionMethod('Eresus_CMS', 'detectWebRoot');
		$detectWebRoot->setAccessible(true);
		$detectWebRoot->invoke($obj);

		$this->assertEquals('/example.org', $httpRequest->localRoot);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS::detectWebRoot
	 */
	public function test_detectWebRoot_windows()
	{
		/* Подменяем DOCUMENT_ROOT */
		$webServer = WebServer::getInstance();
		$documentRoot = new ReflectionProperty('WebServer', 'documentRoot');
		$documentRoot->setAccessible(true);
		$documentRoot->setValue($webServer, FS::canonicalForm('C:\Program Files\Apache Webserver\docs'));

		$obj = new Eresus_CMS;
		// Подменяем результат getRootDir
		$p_rootDir = new ReflectionProperty('Eresus_Application', 'rootDir');
		$p_rootDir->setAccessible(true);
		$p_rootDir->setValue($obj, FS::canonicalForm('C:\Program Files\Apache Webserver\docs\example.org'));
		$httpRequest = new HttpRequest();

		$request = new ReflectionProperty('Eresus_CMS', 'request');
		$request->setAccessible(true);
		$request->setValue($obj, $httpRequest);

		$detectWebRoot = new ReflectionMethod('Eresus_CMS', 'detectWebRoot');
		$detectWebRoot->setAccessible(true);
		$detectWebRoot->invoke($obj);

		$this->assertEquals('/example.org', $httpRequest->localRoot);
	}
	//-----------------------------------------------------------------------------

	/* */
}
