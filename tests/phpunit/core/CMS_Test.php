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
require_once TESTS_SRC_DIR . '/core/CMS.php';
require_once TESTS_SRC_DIR . '/core/classes/WebServer.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMS_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_CMS::getVersion
	 */
	public function test_getVersion()
	{
		$cms = new Eresus_CMS;
		$this->assertEquals('${product.version}', $cms->getVersion());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS::getRootDir
	 */
	public function test_getRootDir()
	{
		$cms = new Eresus_CMS;

		$this->assertEquals(TESTS_SRC_DIR, $cms->getRootDir());

		$p_rootDir = new ReflectionProperty('Eresus_CMS', 'rootDir');
		$p_rootDir->setAccessible(true);
		$p_rootDir->setValue($cms, '/home/example.org');

		$this->assertEquals('/home/example.org', $cms->getRootDir());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS::initConf
	 */
	public function test_initConf()
	{
		if (extension_loaded('suhosin') &&
			strpos(ini_get('suhosin.executor.include.whitelist'), 'vfs') === false)
		{
			$this->markTestSkipped(__METHOD__ .
				' needs "vfs" to be allowed in "suhosin.executor.include.whitelist" option');
		}

		$initConf = new ReflectionMethod('Eresus_CMS', 'initConf');
		$initConf->setAccessible(true);

		$rootDir = new ReflectionProperty('Eresus_CMS', 'rootDir');
		$rootDir->setAccessible(true);

		$cms = new Eresus_CMS();

		vfsStreamWrapper::register();
		$file = new vfsStreamFile('main.php');
		$file->setContent("<?php\nEresus_Config::set('initConf_1', 'valid');\n");
		$dir = new vfsStreamDirectory('cfg');
		$dir->addChild($file);
		vfsStreamWrapper::setRoot(new vfsStreamDirectory('htdocs'));
		vfsStreamWrapper::getRoot()->addChild($dir);
		$rootDir->setValue($cms, vfsStream::url('htdocs'));

		$initConf->invoke($cms);
		$this->assertEquals('valid', Eresus_Config::get('initConf_1'));

		$file->setContent("<?\nEresus_Config::set('initConf_2', 'valid');\n");
		$initConf->invoke($cms);
		$this->assertEquals('valid', Eresus_Config::get('initConf_2'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS::initConf
	 * @expectedException DomainException
	 */
	public function test_initConf_errors()
	{
		if (extension_loaded('suhosin') &&
			strpos(ini_get('suhosin.executor.include.whitelist'), 'vfs') === false)
		{
			$this->markTestSkipped(__METHOD__ .
				' needs "vfs" to be allowed in "suhosin.executor.include.whitelist" option');
		}

		$initConf = new ReflectionMethod('Eresus_CMS', 'initConf');
		$initConf->setAccessible(true);

		$rootDir = new ReflectionProperty('Eresus_CMS', 'rootDir');
		$rootDir->setAccessible(true);

		$cms = new Eresus_CMS();

		vfsStreamWrapper::register();
		$file = new vfsStreamFile('main.php');
		$file->setContent("<?php\nabc\n");
		$dir = new vfsStreamDirectory('cfg');
		$dir->addChild($file);
		vfsStreamWrapper::setRoot(new vfsStreamDirectory('htdocs'));
		vfsStreamWrapper::getRoot()->addChild($dir);
		$rootDir->setValue($cms, vfsStream::url('htdocs'));

		$initConf->invoke($cms);
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
		// Подменяем результат getFsRoot
		$obj->fsRoot = '/home/user/public_html';
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
		// Подменяем результат getFsRoot
		$obj->fsRoot = '/home/user/public_html/example.org';
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
		// Подменяем результат getFsRoot
		$obj->fsRoot = FS::canonicalForm('C:\Program Files\Apache Webserver\docs\example.org');
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
