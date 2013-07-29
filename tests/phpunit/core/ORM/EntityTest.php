<?php
/**
 * Тесты класса Eresus_ORM_Entity
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
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

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_ORM_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus_ORM_Entity::__construct
     * @covers Eresus_ORM_Entity::getProperty
     * @covers Eresus_ORM_Entity::setProperty
     * @covers Eresus_ORM_Entity::__get
     * @covers Eresus_ORM_Entity::__set
     */
    public function testOverview()
    {
        $entity = $this->getMockBuilder('Eresus_ORM_Entity')->disableOriginalConstructor()->
            setMethods(array('getFoo', 'setFoo', 'getTable'))->getMock();
        $entity->expects($this->once())->method('getFoo')->will($this->returnValue('baz'));
        $entity->expects($this->once())->method('setFoo')->with('baz');
        $entity->expects($this->any())->method('getTable')
            ->will($this->returnValue(new \Mekras\TestDoubles\UniversalStub()));
        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()->getMock();
        $attrs = array('foo' => 'bar');

        /** @var Eresus_ORM_Entity $entity */
        $entity->__construct($plugin, $attrs);

        $attrsProp = new ReflectionProperty('Eresus_ORM_Entity', 'attrs');
        $attrsProp->setAccessible(true);
        $this->assertEquals($attrs, $attrsProp->getValue($entity));

        $this->assertEquals('bar', $entity->getProperty('foo'));
        $this->assertNull($entity->getProperty('bar'));
        $entity->setProperty('bar', 'foo');
        $this->assertEquals('foo', $entity->getProperty('bar'));

        $this->assertEquals('baz', $entity->foo);
        $entity->foo = 'baz';
        $this->assertEquals('foo', $entity->bar);
        $entity->bar = 'foo';
    }

    /**
     * @covers Eresus_ORM_Entity::getTable
     */
    public function testGetTable()
    {
        $plugin = $this->getMockBuilder('Eresus_Plugin')->setMethods(array('getOrmClassPrefix'))
            ->disableOriginalConstructor()->getMock();
        $plugin->expects($this->any())->method('getOrmClassPrefix')
            ->will($this->returnValue('Eresus_ORM_EntityTest_GetTable'));
        $entity = $this->getMockForAbstractClass('Eresus_ORM_Entity', array($plugin),
            'Eresus_ORM_EntityTest_GetTable_Entity_Foo');

        $tables = new ReflectionProperty('Eresus_ORM', 'tables');
        $tables->setAccessible(true);
        $tables->setValue('Eresus_ORM',
            array('Eresus_ORM_EntityTest_GetTable_Entity_Table_Foo' => true));

        /** @var Eresus_ORM_Entity $entity */
        $this->assertTrue($entity->getTable());
    }

    /**
     * @covers Eresus_ORM_Entity::getProperty
     */
    public function testGetProperty()
    {
        $table = $this->getMock('stdClass', array('getColumns'));
        $table->expects($this->any())->method('getColumns')
            ->will($this->returnValue(array(
                'intProp' => array('type' => 'integer'),
                'entityProp' => array('type' => 'entity', 'class' => 'stdClass'),
            )));

        $otherTable = $this->getMock('stdClass', array('find'));
        $otherTable->expects($this->once())->method('find')
            ->will($this->returnValue(new stdClass()));


        $entity = $this->getMockBuilder('Eresus_ORM_Entity')->disableOriginalConstructor()
            ->setMethods(array('getTable', 'getTableByEntityClass'))->getMock();
        $entity->expects($this->any())->method('getTable')->will($this->returnValue($table));
        $entity->expects($this->any())->method('getTableByEntityClass')
            ->will($this->returnValue($otherTable));

        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()->getMock();
        $attrs = array('intProp' => 123, 'entityProp' => 1);
        /** @var Eresus_ORM_Entity $entity */
        $entity->__construct($plugin, $attrs);
        $this->assertNull($entity->getProperty('nullProp'));
        $this->assertEquals(123, $entity->getProperty('intProp'));
        $this->assertInternalType('object', $entity->getProperty('entityProp'));
    }

    /**
     * @covers Eresus_ORM_Entity::setProperty
     */
    public function testSetProperty()
    {
        $table = $this->getMock('stdClass', array('getColumns', 'getPrimaryKey'));
        $table->expects($this->any())->method('getColumns')
            ->will($this->returnValue(array(
                'foo' => array('type' => 'entity', 'class' => 'stdClass'),
            )));
        $table->expects($this->any())->method('getPrimaryKey')->will($this->returnValue('id'));
        $entity = $this->getMockBuilder('Eresus_ORM_Entity')->disableOriginalConstructor()
            ->setMethods(array('getTable'))->getMock();
        $entity->expects($this->any())->method('getTable')->will($this->returnValue($table));

        $attrsProperty = new ReflectionProperty('Eresus_ORM_Entity', 'attrs');
        $attrsProperty->setAccessible(true);

        $obj = new stdClass();
        $obj->id = 123;
        /** @var Eresus_ORM_Entity $entity */
        $entity->setProperty('foo', $obj);
        $this->assertEquals(array('foo' => 123), $attrsProperty->getValue($entity));
    }

    /**
     * @covers Eresus_ORM_Entity::getPrimaryKey
     */
    public function testGetPrimaryKey()
    {
        $table = $this->getMock('stdClass', array('getPrimaryKey'));
        $table->expects($this->any())->method('getPrimaryKey')->will($this->returnValue('id'));

        $entity = $this->getMockBuilder('Eresus_ORM_Entity')->disableOriginalConstructor()
            ->setMethods(array('getTable', '__get'))->getMock();
        $entity->expects($this->any())->method('getTable')->will($this->returnValue($table));
        $entity->expects($this->any())->method('__get')->will($this->returnValue(123));
        /** @var Eresus_ORM_Entity $entity */
        $this->assertEquals(123, $entity->getPrimaryKey());
    }

    /**
     * @covers Eresus_ORM_Entity::getTableByEntityClass
     */
    public function testGetTableByEntityClass()
    {
        $getTableByEntityClass = new ReflectionMethod('Eresus_ORM_Entity', 'getTableByEntityClass');
        $getTableByEntityClass->setAccessible(true);

        $entity = $this->getMockBuilder('Eresus_ORM_Entity')->disableOriginalConstructor()
            ->getMock();

        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()
            ->setMethods(array('getOrmClassPrefix'))->getMock();
        $plugin->expects($this->any())->method('getOrmClassPrefix')
            ->will($this->returnValue('Foo'));

        $plugins = $this->getMock('stdClass', array('load'));
        $plugins->expects($this->any())->method('load')->will($this->returnValue($plugin));
        Eresus_Tests::setStatic('Eresus_Plugin_Registry', $plugins);

        $tables = new ReflectionProperty('Eresus_ORM', 'tables');
        $tables->setAccessible(true);
        $tables->setValue(array('Foo_Entity_Table_Bar' => 'table!'));

        $this->assertEquals('table!', $getTableByEntityClass->invoke($entity, 'Foo_Entity_Bar'));
    }
}

