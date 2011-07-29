<?php
/**
 * ${product.title}
 *
 * @version ${product.version}
 *
 * PhpUnit Tests
 *
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * @package Templates
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../stubs.php';
require_once TESTS_SRC_ROOT . '/core/Config.php';
require_once TESTS_SRC_ROOT . '/core/Logger.php';
require_once TESTS_SRC_ROOT . '/core/Template.php';

class Eresus_Template_Test extends PHPUnit_Framework_TestCase
{
	private $error_log;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		$this->error_log = ini_get('error_log');
		$TMP = isset($_ENV['TMP']) ? $_ENV['TMP'] : '/tmp';
		ini_set('error_log', tempnam($TMP, 'eresus-core-'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		ini_set('error_log', $this->error_log);
		$p_dwoo = new ReflectionProperty('Eresus_Template', 'dwoo');
		$p_dwoo->setAccessible(true);
		$p_dwoo->setValue('Eresus_Template', null);
		Eresus_Config::drop('core.template.templateDir');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template::__construct
	 */
	public function test_construct()
	{
		$p_dwoo = new ReflectionProperty('Eresus_Template', 'dwoo');
		$p_dwoo->setAccessible(true);
		$p_dwoo->setValue('Eresus_Template', null);
		Eresus_Config::set('core.template.charset', 'windows-1251');
		$tmpl = new Eresus_Template();
		$this->assertInstanceOf('Dwoo', $p_dwoo->getValue('Eresus_Template'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template::detectTemplateDir
	 */
	public function test_detectTemplateDir()
	{
		$m_detectTemplateDir = new ReflectionMethod('Eresus_Template', 'detectTemplateDir');
		$m_detectTemplateDir->setAccessible(true);
		Eresus_Config::set('core.template.templateDir', '/path/to/templates');
		$tmpl = new Eresus_Template();
		$this->assertEquals('/path/to/templates',  $m_detectTemplateDir->invoke($tmpl));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template::detectCompileDir
	 */
	public function test_detectCompileDir()
	{
		$m_detectCompileDir = new ReflectionMethod('Eresus_Template', 'detectCompileDir');
		$m_detectCompileDir->setAccessible(true);
		Eresus_Config::set('core.template.compileDir', '/path/to/cache');
		$tmpl = new Eresus_Template();
		$this->assertEquals('/path/to/cache',  $m_detectCompileDir->invoke($tmpl));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template::loadFromFile
	 */
	public function test_loadFromFile()
	{
		$m_loadFromFile = new ReflectionMethod('Eresus_Template', 'loadFromFile');
		$m_loadFromFile->setAccessible(true);
		$p_file = new ReflectionProperty('Eresus_Template', 'file');
		$p_file->setAccessible(true);
		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT . '/core/templates');
		$tmpl = new Eresus_Template();

		$m_loadFromFile->invoke($tmpl, 'auth.html');
		$this->assertInstanceOf('Dwoo_Template_File', $p_file->getValue($tmpl));

		$m_loadFromFile->invoke($tmpl, 'unexistent');
		$this->assertNull($p_file->getValue($tmpl));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template::compile
	 * @expectedException InvalidArgumentException
	 */
	public function test_unexistent()
	{
		$test = new Eresus_Template();
		$test->compile();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template::compile
	 */
	public function test_compile()
	{
		$tmpl = new Eresus_Template();

		$p_file = new ReflectionProperty('Eresus_Template', 'file');
		$p_file->setAccessible(true);
		$p_file->setValue($tmpl, true);

		$dwoo = $this->getMock('stdClass', array('get'));
		$dwoo->expects($this->any())->method('get')->with(true, array('globals' => array()))->
			will($this->returnValue(true));

		$p_dwoo = new ReflectionProperty('Eresus_Template', 'dwoo');
		$p_dwoo->setAccessible(true);
		$p_dwoo->setValue('Eresus_Template', $dwoo);

		$this->assertTrue($tmpl->compile());

		$dwoo->expects($this->once())->method('get')->with(true, array('globals' => array()));

		$dwoo->expects($this->any())->method('get')->
			will($this->returnCallback(function(){throw new Exception;}));

		$this->assertEmpty($tmpl->compile());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template::setGlobalValue
	 * @covers Eresus_Template::getGlobalValue
	 * @covers Eresus_Template::removeGlobalValue
	 */
	public function testSetGetRemove()
	{
		Eresus_Template::setGlobalValue('test', 'testValue');
		$this->assertEquals('testValue', Eresus_Template::getGlobalValue('test'));
		Eresus_Template::removeGlobalValue('test');
		$this->assertNull(Eresus_Template::getGlobalValue('test'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template::fromFile
	 */
	public function test_fromFile()
	{
		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT . '/core/templates');

		$this->assertInstanceOf('Eresus_Template', Eresus_Template::fromFile('auth.html'));
		$this->assertNull(Eresus_Template::fromFile('unexistent'));
	}
	//-----------------------------------------------------------------------------

	/* */
}
