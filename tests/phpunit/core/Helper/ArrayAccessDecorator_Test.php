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
 * @package Tests
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../../../../main/core/Helper/ArrayAccessDecorator.php';

/**
 * @package Tests
 */
class Eresus_Helper_ArrayAccess_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Helper_ArrayAccessDecorator::__construct
	 */
	public function test_construct()
	{
		$test = new Eresus_Helper_ArrayAccessDecorator(new stdClass());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_ArrayAccessDecorator::__construct
	 * @expectedException InvalidArgumentException
	 */
	public function test_construct_invalid()
	{
		$test = new Eresus_Helper_ArrayAccessDecorator(array());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_ArrayAccessDecorator::offsetExists
	 * @covers Eresus_Helper_ArrayAccessDecorator::checkOffsetType
	 */
	public function test_offsetExists()
	{
		$a = new stdClass();
		$a->a = true;
		$test = new Eresus_Helper_ArrayAccessDecorator($a);
		$this->assertTrue(isset($test['a']), 'Case 1');
		$this->assertFalse(isset($test['b']), 'Case 2');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_ArrayAccessDecorator::checkOffsetType
	 * @expectedException InvalidArgumentException
	 */
	public function test_offsetExists_invalid()
	{
		$a = new stdClass();
		$test = new Eresus_Helper_ArrayAccessDecorator($a);
		isset($test[1]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_ArrayAccessDecorator::offsetGet
	 */
	public function test_offsetGet()
	{
		$a = new stdClass();
		$a->a = 123;
		$test = new Eresus_Helper_ArrayAccessDecorator($a);
		$this->assertEquals(123, $test['a']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_ArrayAccessDecorator::offsetSet
	 */
	public function test_offsetSet()
	{
		$a = new stdClass();
		$a->a = 123;
		$test = new Eresus_Helper_ArrayAccessDecorator($a);
		$test['a'] = 321;
		$this->assertEquals(321, $test['a']);
	}
	//-----------------------------------------------------------------------------

	/* */
}
