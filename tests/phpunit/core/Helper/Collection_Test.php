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

require_once dirname(__FILE__) . '/../../../../main/core/Helper/Collection.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_Helper_Collection_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Helper_Collection::__construct
	 */
	public function test_construct()
	{
		$test = new Eresus_Helper_Collection(array(1, 2, 3));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::__construct
	 * @expectedException InvalidArgumentException
	 */
	public function test_construct_not_array()
	{
		$test = new Eresus_Helper_Collection(1);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::offsetExists
	 * @covers Eresus_Helper_Collection::checkOffsetType
	 */
	public function test_offsetExists()
	{
		$test = new Eresus_Helper_Collection(array(1));
		$this->assertTrue(isset($test[0]), 'Case 1');
		$this->assertFalse(isset($test[1]), 'Case 2');

		$test = new Eresus_Helper_Collection(array('a' => 'b'));
		$this->assertTrue(isset($test['a']), 'Case 3');
		$this->assertFalse(isset($test['b']), 'Case 4');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::checkOffsetType
	 * @expectedException InvalidArgumentException
	 */
	public function test_offsetExists_nonScalar()
	{
		$test = new Eresus_Helper_Collection();
		isset($test[new stdClass()]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::offsetGet
	 */
	public function test_offsetGet()
	{
		$test = new Eresus_Helper_Collection(array(12, 34, 56));
		$this->assertEquals(34, $test[1]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::offsetSet
	 */
	public function test_offsetSet()
	{
		$test = new Eresus_Helper_Collection();
		$test['a'] = 'b';
		$this->assertEquals('b', $test['a']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::offsetSet
	 */
	public function test_append()
	{
		$test = new Eresus_Helper_Collection(array('a'));
		$test []= 'b';

		$this->assertEquals('b', $test[1]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::offsetGet
	 */
	public function test_get_unexistent()
	{
		$test = new Eresus_Helper_Collection();
		$this->assertNull($test['unexistent']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::offsetGet
	 * @covers Eresus_Helper_Collection::setDefaultValue
	 */
	public function test_get_defaultValue()
	{
		$test = new Eresus_Helper_Collection();
		$test->setDefaultValue(true);
		$this->assertTrue($test['unexistent']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::offsetUnset
	 */
	public function test_offsetUnset()
	{
		$test = new Eresus_Helper_Collection(array('a' => 'b'));
		$this->assertEquals('b', $test['a']);
		unset($test['a']);
		$this->assertNull($test['a']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::offsetGet
	 * @covers Eresus_Helper_Collection::offsetSet
	 * @covers Eresus_Helper_Collection::setDefaultValue
	 */
	public function test_addToUnexistentArray()
	{
		$test = new Eresus_Helper_Collection();
		$test->setDefaultValue(array());
		$test['a']['b'] = 'c';
		$this->assertEquals('c', $test['a']['b']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::count
	 */
	public function test_count()
	{
		$test = new Eresus_Helper_Collection(array('a', 'b', 'c'));
		$this->assertEquals(3, count($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::serialize
	 * @covers Eresus_Helper_Collection::unserialize
	 */
	public function test_serializable()
	{
		$test1 = new Eresus_Helper_Collection(array('a', 'b', 'c'));
		$s = serialize($test1);
		$test2 = unserialize($s);
		$this->assertEquals('b', $test2[1]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::current
	 * @covers Eresus_Helper_Collection::key
	 * @covers Eresus_Helper_Collection::next
	 * @covers Eresus_Helper_Collection::rewind
	 * @covers Eresus_Helper_Collection::valid
	 */
	public function test_Iterator()
	{
		$test = new Eresus_Helper_Collection(array(2, 3, 4));
		$count = 1;
		foreach ($test as $key => $value)
		{
			$this->assertEquals($count-1, $key, 'key in pass ' . $count);
			$this->assertEquals($count + 1, $value, 'value in pass ' . $count);
			$count++;
		}
		$this->assertEquals(4, $count, 'Invalid count');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helper_Collection::find
	 */
	public function test_find()
	{
		$a = array('a' => 1, 'b' => 2);
		$b = array('a' => 2, 'b' => 3);
		$test = new Eresus_Helper_Collection(array($a, $b));
		$x = $test->find(array('a' => 1));
		$this->assertEquals(1 , $x['a'], 'Array');

		$a = new stdClass();
		$a->a = 1;
		$a->b = 2;
		$b = new stdClass();
		$b->a = 2;
		$b->b = 3;
		$test = new Eresus_Helper_Collection(array($a, $b));
		$this->assertEquals($a , $test->find(array('a' => 1)), 'Object');
	}
	//-----------------------------------------------------------------------------

	/* */
}
