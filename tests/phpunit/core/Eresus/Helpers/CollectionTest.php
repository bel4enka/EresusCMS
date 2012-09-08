<?php
/**
 * ${product.title}
 *
 * Тесты
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
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
 */

require_once __DIR__ . '/../../../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/Eresus/Helpers/Collection.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_Helpers_CollectionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Helpers_Collection::__construct
	 */
	public function test_construct()
	{
		$test = new Eresus_Helpers_Collection(array(1, 2, 3));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helpers_Collection::__construct
	 * @expectedException InvalidArgumentException
	 */
	public function test_construct_not_array()
	{
		$test = new Eresus_Helpers_Collection(1);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helpers_Collection::offsetExists
	 * @covers Eresus_Helpers_Collection::checkOffsetType
	 */
	public function test_offsetExists()
	{
		$test = new Eresus_Helpers_Collection(array(1));
		$this->assertTrue(isset($test[0]), 'Case 1');
		$this->assertFalse(isset($test[1]), 'Case 2');

		$test = new Eresus_Helpers_Collection(array('a' => 'b'));
		$this->assertTrue(isset($test['a']), 'Case 3');
		$this->assertFalse(isset($test['b']), 'Case 4');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helpers_Collection::checkOffsetType
	 * @expectedException InvalidArgumentException
	 */
	public function test_offsetExists_nonScalar()
	{
		$test = new Eresus_Helpers_Collection();
		isset($test[new stdClass()]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helpers_Collection::offsetGet
	 */
	public function test_offsetGet()
	{
		$test = new Eresus_Helpers_Collection(array(12, 34, 56));
		$this->assertEquals(34, $test[1]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helpers_Collection::offsetSet
	 */
	public function test_offsetSet()
	{
		$test = new Eresus_Helpers_Collection();
		$test['a'] = 'b';
		$this->assertEquals('b', $test['a']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helpers_Collection::offsetSet
	 */
	public function test_append()
	{
		$test = new Eresus_Helpers_Collection(array('a'));
		$test []= 'b';

		$this->assertEquals('b', $test[1]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helpers_Collection::offsetGet
	 */
	public function test_get_unexistent()
	{
		$test = new Eresus_Helpers_Collection();
		$this->assertNull($test['unexistent']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helpers_Collection::offsetGet
	 * @covers Eresus_Helpers_Collection::setDefaultValue
	 */
	public function test_get_defaultValue()
	{
		$test = new Eresus_Helpers_Collection();
		$test->setDefaultValue(true);
		$this->assertTrue($test['unexistent']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helpers_Collection::offsetUnset
	 */
	public function test_offsetUnset()
	{
		$test = new Eresus_Helpers_Collection(array('a' => 'b'));
		$this->assertEquals('b', $test['a']);
		unset($test['a']);
		$this->assertNull($test['a']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helpers_Collection::offsetGet
	 * @covers Eresus_Helpers_Collection::offsetSet
	 * @covers Eresus_Helpers_Collection::setDefaultValue
	 */
	public function test_addToUnexistentArray()
	{
		$test = new Eresus_Helpers_Collection();
		$test->setDefaultValue(array());
		$test['a']['b'] = 'c';
		$this->assertEquals('c', $test['a']['b']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helpers_Collection::count
	 */
	public function test_count()
	{
		$test = new Eresus_Helpers_Collection(array('a', 'b', 'c'));
		$this->assertEquals(3, count($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Helpers_Collection::serialize
	 * @covers Eresus_Helpers_Collection::unserialize
	 */
	public function test_serializable()
	{
		$test1 = new Eresus_Helpers_Collection(array('a', 'b', 'c'));
		$s = serialize($test1);
		$test2 = unserialize($s);
		$this->assertEquals('b', $test2[1]);
	}
	//-----------------------------------------------------------------------------

	/* */
}
