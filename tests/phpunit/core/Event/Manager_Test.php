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
 * $Id: URI_Test.php 1745 2011-07-26 21:59:06Z mk $
 */

require_once dirname(__FILE__) . '/../../stubs.php';
require_once TESTS_SRC_ROOT . '/core/Event/Manager.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Event_Manager_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		Eresus_Tests::setStatic('Eresus_Event_Manager', null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		Eresus_Tests::setStatic('Eresus_Event_Manager', null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Event_Manager::getInstance
	 */
	public function test_getInstance()
	{
		$test = Eresus_Event_Manager::getInstance();
		$this->assertSame($test, Eresus_Event_Manager::getInstance());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Event_Manager::register
	 * @expectedException InvalidArgumentException
	 */
	public function test_register_invalid_handler()
	{
		$manager = Eresus_Event_Manager::getInstance();
		$manager->register(null, 'testEvent');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Event_Manager::register
	 * @expectedException InvalidArgumentException
	 */
	public function test_register_invalid_name()
	{
		$manager = Eresus_Event_Manager::getInstance();
		$manager->register(function () {}, null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Event_Manager::register
	 */
	public function test_register()
	{
		$manager = Eresus_Event_Manager::getInstance();

		$hnd = $manager->register(function () {}, 'testEvent1');
		$this->assertEquals(0, $hnd);

		$hnd = $manager->register(function () {}, 'testEvent2');
		$this->assertEquals(1, $hnd);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Event_Manager::dispatch
	 */
	public function test_dispatch()
	{
		$mock = $this->getMock('stdClass', array('handler1', 'handler2', 'handler3'));
		$mock->expects($this->once())->method('handler1');
		$mock->expects($this->once())->method('handler2');
		$mock->expects($this->never())->method('handler3');

		$manager = Eresus_Event_Manager::getInstance();

		$manager->register(array($mock, 'handler1'), 'testEvent1');
		$manager->register(array($mock, 'handler2'), 'testEvent1');
		$manager->register(array($mock, 'handler3'), 'testEvent2');

		$event = $this->getMockForAbstractClass('Eresus_Event', array('testEvent1', $this));
		$manager->dispatch($event);
	}
	//-----------------------------------------------------------------------------

	/* */
}
