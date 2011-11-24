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
 * $Id: Record_Test.php 1983 2011-11-23 06:37:58Z mk $
 */

require_once dirname(__FILE__) . '/../../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/Console/Command.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Console_Command_Test extends PHPUnit_Extensions_OutputTestCase
{
	/**
	 * @covers Eresus_Console_Command::__construct
	 * @expectedException LogicException
	 */
	public function test_construct_error()
	{
		$mock = $this->getMockForAbstractClass('Eresus_Console_Command',
			array(new sfServiceContainerBuilder()));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Console_Command::__construct
	 */
	public function test_construct()
	{
		$mock = $this->getMockForAbstractClass('Eresus_Console_Command', array(), '', false);
		$p_name = new ReflectionProperty('Eresus_Console_Command', 'name');
		$p_name->setAccessible(true);
		$p_name->setValue($mock, 'foo');
		$mock->__construct(new sfServiceContainerBuilder());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Console_Command::getName
	 * @covers Eresus_Console_Command::setName
	 */
	public function test_get_set_name()
	{
		$mock = $this->getMockForAbstractClass('Eresus_Console_Command', array(), '', false);
		$m_setName = new ReflectionMethod('Eresus_Console_Command', 'setName');
		$m_setName->setAccessible(true);
		$m_setName->invoke($mock, 'foo');
		$this->assertEquals('foo', $mock->getName());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Console_Command::getDescription
	 * @covers Eresus_Console_Command::setDescription
	 */
	public function test_get_set_Description()
	{
		$mock = $this->getMockForAbstractClass('Eresus_Console_Command', array(), '', false);
		$m_setDescription = new ReflectionMethod('Eresus_Console_Command', 'setDescription');
		$m_setDescription->setAccessible(true);
		$m_setDescription->invoke($mock, 'foo');
		$this->assertEquals('foo', $mock->getDescription());
	}
	//-----------------------------------------------------------------------------

	/**
	* @covers Eresus_Console_Command::out
	*/
	public function test_out()
	{
		$mock = $this->getMockForAbstractClass('Eresus_Console_Command', array(), '', false);
		$m_out = new ReflectionMethod('Eresus_Console_Command', 'out');
		$m_out->setAccessible(true);
		$this->expectOutputString("foo\nbar\n");
		$m_out->invoke($mock, 'foo', 'bar');
	}
	//-----------------------------------------------------------------------------

	/* */
}
