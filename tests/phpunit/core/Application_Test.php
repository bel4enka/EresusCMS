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
require_once TESTS_SRC_DIR . '/core/classes/WebServer.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Application_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Application::getVersion
	 */
	public function test_getVersion()
	{
		$cms = $this->getMockForAbstractClass('Eresus_Application');
		$this->assertEquals('${product.version}', $cms->getVersion());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Application::getRootDir
	 */
	public function test_getRootDir()
	{
		$cms = $this->getMockForAbstractClass('Eresus_Application');

		$this->assertEquals(TESTS_SRC_DIR, $cms->getRootDir());

		$p_rootDir = new ReflectionProperty('Eresus_Application', 'rootDir');
		$p_rootDir->setAccessible(true);
		$p_rootDir->setValue($cms, '/home/example.org');

		$this->assertEquals('/home/example.org', $cms->getRootDir());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Application::initConf
	 */
	public function test_initConf()
	{
		if (extension_loaded('suhosin') &&
			strpos(ini_get('suhosin.executor.include.whitelist'), 'vfs') === false)
		{
			$this->markTestSkipped(__METHOD__ .
				' needs "vfs" to be allowed in "suhosin.executor.include.whitelist" option');
		}

		$initConf = new ReflectionMethod('Eresus_Application', 'initConf');
		$initConf->setAccessible(true);

		$rootDir = new ReflectionProperty('Eresus_Application', 'rootDir');
		$rootDir->setAccessible(true);

		$cms = $this->getMockForAbstractClass('Eresus_Application');

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
	 * @covers Eresus_Application::initConf
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

		$initConf = new ReflectionMethod('Eresus_Application', 'initConf');
		$initConf->setAccessible(true);

		$rootDir = new ReflectionProperty('Eresus_Application', 'rootDir');
		$rootDir->setAccessible(true);

		$cms = $this->getMockForAbstractClass('Eresus_Application');

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
	 * @covers Eresus_Application::initDebugTools
	 */
	public function test_initDebugTools()
	{
		$cms = $this->getMockForAbstractClass('Eresus_Application');

		$m_initDebugTools = new ReflectionMethod('Eresus_Application', 'initDebugTools');
		$m_initDebugTools->setAccessible(true);

		Eresus_Config::set('eresus.cms.debug', true);
		$m_initDebugTools->invoke($cms);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Application::initTimezone
	 */
	public function test_initTimezone()
	{
		$cms = $this->getMockForAbstractClass('Eresus_Application');

		$m_initTimezone = new ReflectionMethod('Eresus_Application', 'initTimezone');
		$m_initTimezone->setAccessible(true);

		Eresus_Config::set('eresus.cms.timezone', 'Europe/Moscow');
		$m_initTimezone->invoke($cms);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Application::initLocale
	 */
	public function test_initLocale()
	{
		$cms = $this->getMockForAbstractClass('Eresus_Application');

		$m_initLocale = new ReflectionMethod('Eresus_Application', 'initLocale');
		$m_initLocale->setAccessible(true);

		$container = new sfServiceContainerBuilder();
		Eresus_Tests::setStatic('Eresus_Kernel', $container, 'sc');

		Eresus_Config::set('eresus.cms.locale.default', 'ru_RU');
		$m_initLocale->invoke($cms);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Application::initTemplateEngine
	 */
	public function test_initTemplateEngine()
	{
		$cms = $this->getMockForAbstractClass('Eresus_Application');

		$m_initTemplateEngine = new ReflectionMethod('Eresus_Application', 'initTemplateEngine');
		$m_initTemplateEngine->setAccessible(true);

		//Eresus_Config::set('eresus.cms.TemplateEngine.default', 'ru_RU');
		$m_initTemplateEngine->invoke($cms);
	}
	//-----------------------------------------------------------------------------

	/* */
}
