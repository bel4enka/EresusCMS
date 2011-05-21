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
require_once dirname(__FILE__) . '/../../../main/core/Kernel/PHP.php';
require_once dirname(__FILE__) . '/../../../main/core/CMS.php';
require_once dirname(__FILE__) . '/../../../main/core/CMS/Service.php';
require_once dirname(__FILE__) . '/../../../main/core/Config.php';
require_once dirname(__FILE__) . '/../../../main/core/WebServer.php';
require_once dirname(__FILE__) . '/../../../main/core/Service/Auth.php';
require_once dirname(__FILE__) . '/../../../main/core/Template.php';

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
	 * @covers Eresus_CMS::app
	 */
	public function test_app()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$obj = new stdClass();
		$app = new ReflectionProperty('Eresus_Kernel', 'app');
		$app->setAccessible(true);
		$app->setValue('Eresus_Kernel', $obj);

		$this->assertSame($obj, Eresus_CMS::app());
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
	 * @covers Eresus_CMS::getFrontController
	 */
	public function test_getFrontController()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$test = new stdClass();

		$obj = new Eresus_CMS;
		$frontController = new ReflectionProperty('Eresus_CMS', 'frontController');
		$frontController->setAccessible(true);
		$frontController->setValue($obj, $test);

		$this->assertSame($test, $obj->getFrontController());
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
	 * @covers Eresus_CMS::initSession
	 */
	public function test_initSession()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$initSession = new ReflectionMethod('Eresus_CMS', 'initSession');
		$initSession->setAccessible(true);

		ini_set('session.save_path', '/tmp');
		$cms = new Eresus_CMS();

		$initSession->invoke($cms);
	}
	//-----------------------------------------------------------------------------

	/* */
}
