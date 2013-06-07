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
 * @package Eresus_CMS
 * @subpackage Tests
 * @author Михаил Красильников <mk@eresus.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../stubs.php';
require_once TESTS_SRC_DIR . '/core/Kernel.php';
require_once TESTS_SRC_DIR . '/core/FS/Tool.php';
require_once TESTS_SRC_DIR . '/core/Application.php';
require_once TESTS_SRC_DIR . '/core/framework/core/kernel.php';
require_once TESTS_SRC_DIR . '/core/framework/core/WWW/HTTP/HttpRequest.php';
require_once TESTS_SRC_DIR . '/core/CMS.php';
require_once TESTS_SRC_DIR . '/core/classes/WebServer.php';

/**
 * @package Eresus_CMS
 * @subpackage Tests
 */
class Eresus_CMS_Test extends PHPUnit_Framework_TestCase
{
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
        // Подменяем результат getFsRoot
        $fsRoot = new ReflectionProperty('Eresus_CMS', 'fsRoot');
        $fsRoot->setAccessible(true);
		$fsRoot->setValue($obj, '/home/user/public_html');

        $httpRequest = new HttpRequest();
		$request = new ReflectionProperty('Eresus_CMS', 'request');
		$request->setAccessible(true);
		$request->setValue($obj, $httpRequest);

		$detectWebRoot = new ReflectionMethod('Eresus_CMS', 'detectWebRoot');
		$detectWebRoot->setAccessible(true);
		$detectWebRoot->invoke($obj);

        $localRoot = new ReflectionProperty('HttpRequest', 'localRoot');
        $localRoot->setAccessible(true);
		$this->assertEquals('', $localRoot->getValue($httpRequest));
	}

	/**
	 * @covers Eresus_CMS::detectWebRoot
	 */
	public function test_detectWebRoot_notRoot()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		/* Подменяем DOCUMENT_ROOT */
		$webServer = WebServer::getInstance();
		$documentRoot = new ReflectionProperty('WebServer', 'documentRoot');
		$documentRoot->setAccessible(true);
		$documentRoot->setValue($webServer, '/home/user/public_html');

		$obj = new Eresus_CMS;
		// Подменяем результат getFsRoot
        $fsRoot = new ReflectionProperty('Eresus_CMS', 'fsRoot');
        $fsRoot->setAccessible(true);
        $fsRoot->setValue($obj, '/home/user/public_html/example.org');

		$httpRequest = new HttpRequest();
		$request = new ReflectionProperty('Eresus_CMS', 'request');
		$request->setAccessible(true);
		$request->setValue($obj, $httpRequest);

		$detectWebRoot = new ReflectionMethod('Eresus_CMS', 'detectWebRoot');
		$detectWebRoot->setAccessible(true);
		$detectWebRoot->invoke($obj);

        $localRoot = new ReflectionProperty('HttpRequest', 'localRoot');
        $localRoot->setAccessible(true);
        $this->assertEquals('/example.org', $localRoot->getValue($httpRequest));
	}

	/**
	 * @covers Eresus_CMS::getPage
	 */
	public function test__getPage()
	{	
		$p_page = new ReflectionProperty("Eresus_CMS", "page");
		$p_page->setAccessible(true);
		
		$eresus = new Eresus_CMS();
		$p_page->setValue($eresus,'foo');
		
		$this->assertEquals('foo', $eresus->getPage());
	}
	
	/* */
}
