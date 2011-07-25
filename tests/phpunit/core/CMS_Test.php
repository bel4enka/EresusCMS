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

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/Config.php';
require_once dirname(__FILE__) . '/../../../main/core/Kernel.php';
require_once dirname(__FILE__) . '/../../../main/core/CMS/Request.php';
require_once dirname(__FILE__) . '/../../../main/core/HTTP/Request.php';
require_once dirname(__FILE__) . '/../../../main/core/URI.php';
require_once dirname(__FILE__) . '/../../../main/core/WebServer.php';
require_once dirname(__FILE__) . '/../../../main/core/DB/ORM.php';
#require_once dirname(__FILE__) . '/../../../main/core/Auth.php';
#require_once dirname(__FILE__) . '/../../../main/core/Template.php';
require_once dirname(__FILE__) . '/../../../main/core/CMS.php';

require_once 'vfsStream/vfsStream.php';
$vfsStream = new ReflectionClass('vfsStream');
$dir = dirname($vfsStream->getFileName());
PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist($dir);

/**
 * @package Eresus
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

		$autoloaders = spl_autoload_functions();
		foreach ($autoloaders as $autoloader)
		{
			if ($autoloader !== 'phpunit_autoload')
			{
				spl_autoload_unregister($autoloader);
			}
		}
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
		$mock = $this->getMockBuilder('Eresus_CMS')->setMethods(array('fake'))->
			disableOriginalConstructor()->getMock();

		$this->assertEquals(TESTS_SRC_ROOT, $mock->getRootDir());

		$p_rootDir = new ReflectionProperty('Eresus_CMS', 'rootDir');
		$p_rootDir->setAccessible(true);
		$p_rootDir->setValue($mock, '/home/example.org');

		$this->assertEquals('/home/example.org', $mock->getRootDir());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS::getSite
	 */
	public function test_getSite()
	{
		$mock = $this->getMockBuilder('Eresus_CMS')->setMethods(array('fake'))->
			disableOriginalConstructor()->getMock();

		$p_rootDir = new ReflectionProperty('Eresus_CMS', 'rootDir');
		$p_rootDir->setAccessible(true);
		$p_rootDir->setValue($mock, '/home/example.org');

		$p_app = new ReflectionProperty('Eresus_Kernel', 'app');
		$p_app->setAccessible(true);
		$p_app->setValue('Eresus_Kernel', $mock);

		Doctrine_Core::setMock(null);
		$this->assertInstanceOf('UniversalStub', $mock->getSite());
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
	 * @covers Eresus_CMS::initDB
	 */
	public function test_initDB()
	{
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
		Eresus_Config::set('eresus.cms.dsn.prefix', 'prefix_');

		$rootDir->setValue($cms, vfsStream::url('htdocs'));
		$initDB->invoke($cms);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS::initDB
	 * @expectedException DomainException
	 */
	public function test_initDB_no_dsn()
	{
		if (extension_loaded('suhosin') &&
			strpos(ini_get('suhosin.executor.include.whitelist'), 'vfs') === false)
		{
			$this->markTestSkipped(__METHOD__ .
				' needs "vfs" to be allowed in "suhosin.executor.include.whitelist" option');
		}

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

		Eresus_Config::drop('eresus.cms.dsn');

		$rootDir->setValue($cms, vfsStream::url('htdocs'));
		$initDB->invoke($cms);
	}
	//-----------------------------------------------------------------------------

	/* */
}
