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

require_once dirname(__FILE__) . '/../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/Config.php';
require_once TESTS_SRC_DIR . '/core/Logger.php';
require_once TESTS_SRC_DIR . '/core/Template.php';

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
		ini_set('error_log', tempnam($TMP, 'eresus-cms-'));
		Eresus_Tests::setStatic('Eresus_Template', null, 'dwoo');
		Eresus_Tests::setStatic('Eresus_Template', array(), 'globals');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		ini_set('error_log', $this->error_log);
		Eresus_Tests::setStatic('Eresus_Template', null, 'dwoo');
		Eresus_Tests::setStatic('Eresus_Template', array(), 'globals');
		Eresus_Config::drop('dwoo.templateDir');
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
		Eresus_Config::set('dwoo.templateDir', '/path/to/templates');
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
		Eresus_Config::set('dwoo.compileDir', '/path/to/cache');
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
		Eresus_Config::set('dwoo.templateDir', TESTS_SRC_DIR . '/admin/themes/default');
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
			will($this->returnCallback(function(){throw new Dwoo_Exception;}));

		$this->assertEquals('[template error]', $tmpl->compile());
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
		Eresus_Config::set('dwoo.templateDir', TESTS_SRC_DIR . '/admin/themes/default');

		$this->assertInstanceOf('Eresus_Template', Eresus_Template::fromFile('auth.html'));
		$this->assertNull(Eresus_Template::fromFile('unexistent'));
	}
	//-----------------------------------------------------------------------------

	/* */
}
