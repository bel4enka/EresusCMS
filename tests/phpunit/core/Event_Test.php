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
require_once TESTS_SRC_ROOT . '/core/Event.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Event_Test extends PHPUnit_Framework_TestCase
{
	/**
	* @see PHPUnit_Framework_TestCase::tearDown()
	*/
	protected function tearDown()
	{
		Eresus_Tests::setStatic('Eresus_Event_Manager', null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Event::__construct
	 * @expectedException InvalidArgumentException
	 */
	public function test_construct_invalid_name()
	{
		$event = $this->getMockForAbstractClass('Eresus_Event', array(null, $this));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Event::__construct
	 * @expectedException InvalidArgumentException
	 */
	public function test_construct_invalid_sender()
	{
		$event = $this->getMockForAbstractClass('Eresus_Event', array('testEvent', null));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Event::__construct
	 * @covers Eresus_Event::getName
	 * @covers Eresus_Event::getSender
	 */
	public function test_generic()
	{
		$event = $this->getMockForAbstractClass('Eresus_Event', array('testEvent', $this));
		$this->assertEquals('testEvent', $event->getName());
		$this->assertSame($this, $event->getSender());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Event::dispatch
	 */
	public function test_dispatch()
	{
		$manager = $this->getMock('stdClass', array('dispatch'));
		Eresus_Tests::setStatic('Eresus_Event_Manager', $manager);

		$event = $this->getMockForAbstractClass('Eresus_Event', array('testEvent', $this));

		$manager->expects($this->once())->method('dispatch')->with($event);

		$event->dispatch();
	}
	//-----------------------------------------------------------------------------

	/* */
}
