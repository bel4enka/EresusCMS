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

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/Kernel.php';
require_once dirname(__FILE__) . '/../../../main/core/CMS.php';
require_once dirname(__FILE__) . '/../../../main/core/CMS/Service.php';
require_once dirname(__FILE__) . '/../../../main/core/Config.php';
require_once dirname(__FILE__) . '/../../../main/core/WebServer.php';
require_once dirname(__FILE__) . '/../../../main/core/Service/Auth.php';
require_once dirname(__FILE__) . '/../../../main/core/Template.php';
require_once dirname(__FILE__) . '/../../../main/core/HTTP/Toolkit.php';
require_once dirname(__FILE__) . '/../../../main/core/DB/ORM.php';

require_once 'vfsStream/vfsStream.php';
$vfsStream = new ReflectionClass('vfsStream');
$dir = dirname($vfsStream->getFileName());
PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist($dir);

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_CMS_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		$app = new ReflectionProperty('Eresus_Kernel', 'app');
		$app->setAccessible(true);
		$app->setValue('Eresus_Kernel', null);
		Eresus_Config::drop('eresus.cms.dsn');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS::getVersion
	 */
	public function test_getVersion()
	{
		$mock = $this->getMockBuilder('Eresus_CMS')->setMethods(array('fake'))->
			disableOriginalConstructor()->getMock();

		$this->assertEquals('${product.version}', $mock->getVersion());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS::getRootDir
	 */
	public function test_getRootDir()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$mock = $this->getMockBuilder('Eresus_CMS')->setMethods(array('fake'))->
			disableOriginalConstructor()->getMock();

		$p_rootDir = new ReflectionProperty('Eresus_CMS', 'rootDir');
		$p_rootDir->setAccessible(true);
		$p_rootDir->setValue($mock, '/home/example.org');

		$this->assertEquals('/home/example.org', $mock->getRootDir());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS::getDataDir
	 */
	public function test_getDataDir()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$mock = $this->getMockBuilder('Eresus_CMS')->setMethods(array('getRootDir'))->
			disableOriginalConstructor()->getMock();
		$mock->expects($this->once())->method('getRootDir')->
			will($this->returnValue('/home/example.org'));

		$this->assertEquals('/home/example.org/data', $mock->getDataDir());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS::initDB
	 */
	public function test_initDB()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		if (extension_loaded('suhosin') &&
			strpos(ini_get('suhosin.executor.include.whitelist'), 'vfs') === false)
		{
			$this->markTestSkipped(__METHOD__ .
				' needs "vfs" to be allowed in "suhosin.executor.include.whitelist" option');
		}

		$Doctrine_Core = $this->getMock('stdClass', array('loadModels'));
		$Doctrine_Core->expects($this->once())->method('loadModels')->
			will($this->returnCallback(
				function ($path)
				{
					if (!is_dir($path))
					{
						throw new PHPUnit_Framework_AssertionFailedError(
							'Doctrine_Core::loadModels passed invalid path: ' . $path);
					}
				}
			));

		Doctrine_Core::setMock($Doctrine_Core);

		$initDB = new ReflectionMethod('Eresus_CMS', 'initDB');
		$initDB->setAccessible(true);

		$rootDir = new ReflectionProperty('Eresus_CMS', 'rootDir');
		$rootDir->setAccessible(true);

		$cms = new Eresus_CMS();

		vfsStreamWrapper::register();
		$file = new vfsStreamFile('Doctrine.php');
		$dir = new vfsStreamDirectory('core');
		$dir->addChild($file);
		vfsStreamWrapper::setRoot(new vfsStreamDirectory('htdocs'));
		vfsStreamWrapper::getRoot()->addChild($dir);

		Eresus_Config::set('eresus.cms.dsn', 'null://');

		$rootDir->setValue($cms, vfsStream::url('htdocs'));
		$initDB->invoke($cms);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS::initSite
	 */
	public function test_initSite()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}
		return;

		$initSite = new ReflectionMethod('Eresus_CMS', 'initSite');
		$initSite->setAccessible(true);

		$p_container = new ReflectionProperty('Eresus_CMS', 'container');
		$p_container->setAccessible(true);

		$request = $this->getMock('stdClass', array('getHost', 'getRequestUri'));
		$request->expects($this->any())->method('getHost')->will($this->returnValue('example.org'));
		$request->expects($this->any())->method('getRequestUri')->
			will($this->returnValue('http://example.org/site_root/some_path/file.ext'));

		$site = $this->getMock('stdClass', array('setHost', 'setRoot'));
		$site->expects($this->once())->method('setHost')->with('example.org');
		$site->expects($this->once())->method('setRoot')->with('/site_root');

		$dbTable = $this->getMock('stdClass', array('findOneByDql'));
		$dbTable->expects($this->once())->method('findOneByDql')->will($this->returnValue($site));

		$orm = $this->getMock('stdClass', array('getTable'));
		$orm->expects($this->once())->method('getTable')->will($this->returnValue($dbTable));

		$p_app = new ReflectionProperty('Eresus_Kernel', 'app');
		$p_app->setAccessible(true);

		$cms = new Eresus_CMS();
		$p_app->setValue('Eresus_Kernel', $cms);
		$p_container->setValue($cms, array('request' => $request, 'orm' => $orm));

		$initSite->invoke($cms);
	}
	//-----------------------------------------------------------------------------

	/* */
}
