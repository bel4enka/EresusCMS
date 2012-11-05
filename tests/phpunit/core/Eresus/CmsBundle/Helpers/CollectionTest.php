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

namespace Tests\Eresus\CmsBundle\Helpers;

use Eresus\CmsBundle\Helpers\Collection;

require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus\CmsBundle\Helpers\Collection::__construct
     */
    public function testConstruct()
    {
        new Collection(array(1, 2, 3));
    }

    /**
     * @covers Eresus\CmsBundle\Helpers\Collection::__construct
     * @expectedException InvalidArgumentException
     */
    public function testConstructNotArray()
    {
        new Collection(1);
    }

    /**
     * @covers Eresus\CmsBundle\Helpers\Collection::offsetExists
     * @covers Eresus\CmsBundle\Helpers\Collection::checkOffsetType
     */
    public function testOffsetExists()
    {
        $test = new Collection(array(1));
        $this->assertTrue(isset($test[0]), 'Case 1');
        $this->assertFalse(isset($test[1]), 'Case 2');

        $test = new Collection(array('a' => 'b'));
        $this->assertTrue(isset($test['a']), 'Case 3');
        $this->assertFalse(isset($test['b']), 'Case 4');
    }

    /**
     * @covers Eresus\CmsBundle\Helpers\Collection::checkOffsetType
     * @expectedException InvalidArgumentException
     */
    public function testOffsetExistsNonScalar()
    {
        $test = new Collection();
        isset($test[new \stdClass()]);
    }

    /**
     * @covers Eresus\CmsBundle\Helpers\Collection::offsetGet
     */
    public function testOffsetGet()
    {
        $test = new Collection(array(12, 34, 56));
        $this->assertEquals(34, $test[1]);
    }

    /**
     * @covers Eresus\CmsBundle\Helpers\Collection::offsetSet
     */
    public function testOffsetSet()
    {
        $test = new Collection();
        $test['a'] = 'b';
        $this->assertEquals('b', $test['a']);
    }

    /**
     * @covers Eresus\CmsBundle\Helpers\Collection::offsetSet
     */
    public function testAppend()
    {
        $test = new Collection(array('a'));
        $test []= 'b';

        $this->assertEquals('b', $test[1]);
    }

    /**
     * @covers Eresus\CmsBundle\Helpers\Collection::offsetGet
     */
    public function testGetUnexistent()
    {
        $test = new Collection();
        $this->assertNull($test['unexistent']);
    }

    /**
     * @covers Eresus\CmsBundle\Helpers\Collection::offsetGet
     * @covers Eresus\CmsBundle\Helpers\Collection::setDefaultValue
     */
    public function testGetDefaultValue()
    {
        $test = new Collection();
        $test->setDefaultValue(true);
        $this->assertTrue($test['unexistent']);
    }

    /**
     * @covers Eresus\CmsBundle\Helpers\Collection::offsetUnset
     */
    public function testOffsetUnset()
    {
        $test = new Collection(array('a' => 'b'));
        $this->assertEquals('b', $test['a']);
        unset($test['a']);
        $this->assertNull($test['a']);
    }

    /**
     * @covers Eresus\CmsBundle\Helpers\Collection::offsetGet
     * @covers Eresus\CmsBundle\Helpers\Collection::offsetSet
     * @covers Eresus\CmsBundle\Helpers\Collection::setDefaultValue
     */
    public function testAddToUnexistentArray()
    {
        $test = new Collection();
        $test->setDefaultValue(array());
        $test['a']['b'] = 'c';
        $this->assertEquals('c', $test['a']['b']);
    }

    /**
     * @covers Eresus\CmsBundle\Helpers\Collection::count
     */
    public function testCount()
    {
        $test = new Collection(array('a', 'b', 'c'));
        $this->assertEquals(3, count($test));
    }

    /**
     * @covers Eresus\CmsBundle\Helpers\Collection::serialize
     * @covers Eresus\CmsBundle\Helpers\Collection::unserialize
     */
    public function testSerializable()
    {
        $test1 = new Collection(array('a', 'b', 'c'));
        $s = serialize($test1);
        $test2 = unserialize($s);
        $this->assertEquals('b', $test2[1]);
    }
}

