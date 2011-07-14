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

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/DB/Record.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_DB_Record_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_DB_Record::unserializeAccessor
	 */
	public function test_unserializeAccessor()
	{
		$test = $this->getMock('Eresus_DB_Record', array('_set', '_get'));
		$test->expects($this->once())->method('_set')->with('a', array());
		$test->expects($this->once())->method('_get')->with('a', false)->
			will($this->returnValue(null));
		$test->unserializeAccessor(true, 'a');

		$test = $this->getMock('Eresus_DB_Record', array('_set', '_get'));
		$test->expects($this->once())->method('_set')->with('a', array());
		$test->expects($this->once())->method('_get')->with('a', false)->
			will($this->returnValue(''));
		$test->unserializeAccessor(true, 'a');

		$test = $this->getMock('Eresus_DB_Record', array('_set', '_get'));
		$test->expects($this->once())->method('_set')->with('a', array('a' => 'b'));
		$test->expects($this->once())->method('_get')->with('a', false)->
			will($this->returnValue('a:1:{s:1:"a";s:1:"b";}'));
		$test->unserializeAccessor(true, 'a');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_DB_Record::serializeMutator
	 */
	public function test_serializeMutator()
	{
		$test = $this->getMock('Eresus_DB_Record', array('_set'));
		$test->expects($this->once())->method('_set')->with('a', 'a:1:{s:1:"a";s:1:"b";}');
		$test->serializeMutator(array('a' => 'b'), true, 'a');
	}
	//-----------------------------------------------------------------------------

	/* */
}
