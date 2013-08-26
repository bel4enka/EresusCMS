<?php
/**
 * Тесты класса Eresus_ORM_Driver_MySQL
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


require_once __DIR__ . '/../../../bootstrap.php';

/**
 * Тесты класса Eresus_ORM_Driver_MySQL
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_ORM_Driver_MySQLTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus_ORM_Driver_MySQL::createTable
     *
     * @see http://bugs.eresus.ru/view.php?id=876
     */
    public function testCreateTable()
    {
        $driver = $this->getMock('Eresus_ORM_Driver_MySQL', array('getFieldDefinition'));
        $driver->expects($this->any())->method('getFieldDefinition')->will($this->returnValue('F'));

        $handler = $this->getMock('stdClass', array('exec'));
        $handler->expects($this->once())->method('exec')->with('CREATE TABLE prefix_foo ' .
            '(f1 INT(10), PRIMARY KEY (id), KEY idx1 (f1)) ENGINE InnoDB DEFAULT CHARSET=utf8');
        /** @var ezcDbHandler $handler */
        $handler->options = new stdClass;
        $handler->options->tableNamePrefix = 'prefix_';
        Eresus_DB::setHandler($handler);

        $table = $this->getMockBuilder('Eresus_ORM_Table')->disableOriginalConstructor()
            ->setMethods(array('setTableDefinition', 'getName', 'getColumns', 'getIndexes'))
            ->getMock();
        $table->expects($this->any())->method('getName')->will($this->returnValue('foo'));
        $table->expects($this->any())->method('getColumns')
            ->will($this->returnValue(array('f1' => array('type' => 'integer'))));
        $table->expects($this->any())->method('getIndexes')
            ->will($this->returnValue(array('idx1' => array('fields' => array('f1')))));

        /** @var Eresus_ORM_Driver_MySQL $driver */
        /** @var Eresus_ORM_Table $table */
        $driver->createTable($table);
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::dropTable
     */
    public function testDropTable()
    {
        $driver = new Eresus_ORM_Driver_MySQL();

        $handler = $this->getMock('stdClass', array('exec'));
        $handler->expects($this->once())->method('exec')->with('DROP TABLE prefix_foo');
        /** @var ezcDbHandler $handler */
        $handler->options = new stdClass;
        $handler->options->tableNamePrefix = 'prefix_';
        Eresus_DB::setHandler($handler);

        $table = $this->getMockBuilder('Eresus_ORM_Table')->disableOriginalConstructor()
            ->setMethods(array('setTableDefinition', 'getName'))->getMock();
        $table->expects($this->any())->method('getName')->will($this->returnValue('foo'));
        /** @var Eresus_ORM_Driver_MySQL $driver */
        /** @var Eresus_ORM_Table $table */
        $driver->dropTable($table);
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::pdoFieldValue
     * @expectedException InvalidArgumentException
     * @dataProvider pdoFieldValueInvalidDataProvider
     */
    public function testPdoFieldValueInvalid($type)
    {
        $driver = new Eresus_ORM_Driver_MySQL;
        $driver->pdoFieldValue(true, $type);
    }

    /**
     * Поставщик данных для {@link testPdoFieldValueInvalid()}
     */
    public function pdoFieldValueInvalidDataProvider()
    {
        return array(array('date'), array('datetime'), array('time'), array('entity'));
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::pdoFieldValue
     */
    public function testPdoFieldValue()
    {
        $driver = new Eresus_ORM_Driver_MySQL;

        $datetime = new DateTime('01-02-03 12:34:56');
        $this->assertEquals($datetime->getTimestamp(),
            $driver->pdoFieldValue($datetime, 'timestamp'));
        $timestamp = time();
        $this->assertEquals($timestamp, $driver->pdoFieldValue($timestamp, 'timestamp'));
        $this->assertEquals('2001-02-03', $driver->pdoFieldValue($datetime, 'date'));
        $this->assertEquals('12:34:56', $driver->pdoFieldValue($datetime, 'time'));
        $this->assertSame(0, $driver->pdoFieldValue(false, 'boolean'));
        $this->assertNull($driver->pdoFieldValue(null, 'time'));
        $entity = $this->getMockBuilder('Eresus_ORM_Entity')->disableOriginalConstructor()
            ->setMethods(array('getPrimaryKey'))->getMock();
        $entity->expects($this->any())->method('getPrimaryKey')->will($this->returnValue(123));
        $this->assertEquals(123, $driver->pdoFieldValue($entity, 'entity'));
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::getFieldDefinition
     * @expectedException InvalidArgumentException
     */
    public function testGetFieldDefinitionNoType()
    {
        $driver = new Eresus_ORM_Driver_MySQL();
        $getFieldDefinition = new ReflectionMethod('Eresus_ORM_Driver_MySQL', 'getFieldDefinition');
        $getFieldDefinition->setAccessible(true);
        $getFieldDefinition->invoke($driver, array());
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::getFieldDefinition
     * @expectedException InvalidArgumentException
     */
    public function testGetFieldDefinitionBadType()
    {
        $driver = new Eresus_ORM_Driver_MySQL();
        $getFieldDefinition = new ReflectionMethod('Eresus_ORM_Driver_MySQL', 'getFieldDefinition');
        $getFieldDefinition->setAccessible(true);
        $getFieldDefinition->invoke($driver, array('type' => 'foo'));
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::getFieldDefinition
     */
    public function testGetFieldDefinition()
    {
        $driver = new Eresus_ORM_Driver_MySQL();
        $getFieldDefinition = new ReflectionMethod('Eresus_ORM_Driver_MySQL', 'getFieldDefinition');
        $getFieldDefinition->setAccessible(true);
        $this->assertEquals('INT(10)',
            $getFieldDefinition->invoke($driver, array('type' => 'integer')));
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::getDefinitionForDefault
     */
    public function testGetDefinitionForDefault()
    {
        $method = new ReflectionMethod('Eresus_ORM_Driver_MySQL', 'getDefinitionForDefault');
        $method->setAccessible(true);
        $driver = new Eresus_ORM_Driver_MySQL();

        $this->assertEquals('', $method->invoke($driver, array()));
        $this->assertEquals(' DEFAULT NULL', $method->invoke($driver, array('default' => null)));
        $this->assertEquals(' DEFAULT \'foo\'', $method->invoke($driver, array(
            'type' => 'string',
            'default' => 'foo'
        )));
        $this->assertEquals(' DEFAULT 123', $method->invoke($driver, array(
            'type' => 'int',
            'default' => 123
        )));
        $this->assertEquals(' DEFAULT 0', $method->invoke($driver, array(
            'type' => 'boolean',
            'default' => false
        )));
        $this->assertEquals(' DEFAULT 1', $method->invoke($driver, array(
            'type' => 'boolean',
            'default' => true
        )));
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::getDefinitionForBoolean
     */
    public function testGetDefinitionForBoolean()
    {
        $method = new ReflectionMethod('Eresus_ORM_Driver_MySQL', 'getDefinitionForBoolean');
        $method->setAccessible(true);
        $driver = new Eresus_ORM_Driver_MySQL();

        $this->assertEquals('BOOL', $method->invoke($driver, array('type' => 'boolean')));
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::getDefinitionForDate
     */
    public function testGetDefinitionForDate()
    {
        $method = new ReflectionMethod('Eresus_ORM_Driver_MySQL', 'getDefinitionForDate');
        $method->setAccessible(true);
        $driver = new Eresus_ORM_Driver_MySQL();

        $this->assertEquals('DATE', $method->invoke($driver, array()));
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::getDefinitionForDatetime
     */
    public function testGetDefinitionForDatetime()
    {
        $method = new ReflectionMethod('Eresus_ORM_Driver_MySQL', 'getDefinitionForDatetime');
        $method->setAccessible(true);
        $driver = new Eresus_ORM_Driver_MySQL();

        $this->assertEquals('DATETIME', $method->invoke($driver, array()));
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::getDefinitionForEntity
     */
    public function testGetDefinitionForEntity()
    {
        $method = new ReflectionMethod('Eresus_ORM_Driver_MySQL', 'getDefinitionForEntity');
        $method->setAccessible(true);
        $driver = new Eresus_ORM_Driver_MySQL();

        $this->assertEquals('INT(10) UNSIGNED', $method->invoke($driver, array()));
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::getDefinitionForFloat
     */
    public function testGetDefinitionForFloat()
    {
        $method = new ReflectionMethod('Eresus_ORM_Driver_MySQL', 'getDefinitionForFloat');
        $method->setAccessible(true);
        $driver = new Eresus_ORM_Driver_MySQL();

        $this->assertEquals('FLOAT', $method->invoke($driver, array()));
        $this->assertEquals('DOUBLE', $method->invoke($driver, array('length' => 2147483647)));
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::getDefinitionForInteger
     */
    public function testGetDefinitionForInteger()
    {
        $method = new ReflectionMethod('Eresus_ORM_Driver_MySQL', 'getDefinitionForInteger');
        $method->setAccessible(true);
        $driver = new Eresus_ORM_Driver_MySQL();

        $this->assertEquals('INT(10)', $method->invoke($driver, array()));
        $this->assertEquals('INT(20)', $method->invoke($driver, array('length' => 20)));
        $this->assertEquals('INT(10) AUTO_INCREMENT',
            $method->invoke($driver, array('autoincrement' => true)));
        $this->assertEquals('INT(10) UNSIGNED',
            $method->invoke($driver, array('unsigned' => true)));
        $this->assertEquals('INT(10) UNSIGNED AUTO_INCREMENT',
            $method->invoke($driver, array('autoincrement' => true, 'unsigned' => true)));
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::getDefinitionForString
     */
    public function testGetDefinitionForString()
    {
        $method = new ReflectionMethod('Eresus_ORM_Driver_MySQL', 'getDefinitionForString');
        $method->setAccessible(true);
        $driver = new Eresus_ORM_Driver_MySQL();

        $this->assertEquals('TEXT', $method->invoke($driver, array()));
        $this->assertEquals('VARCHAR(255)', $method->invoke($driver, array('length' => 255)));
        $this->assertEquals('TEXT',	$method->invoke($driver, array('length' => 65535)));
        $this->assertEquals('LONGTEXT',	$method->invoke($driver, array('length' => 65536)));
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::getDefinitionForTime
     */
    public function testGetDefinitionForTime()
    {
        $method = new ReflectionMethod('Eresus_ORM_Driver_MySQL', 'getDefinitionForTime');
        $method->setAccessible(true);
        $driver = new Eresus_ORM_Driver_MySQL();

        $this->assertEquals('TIME', $method->invoke($driver, array()));
    }

    /**
     * @covers Eresus_ORM_Driver_MySQL::getDefinitionForTimestamp
     */
    public function testGetDefinitionForTimestamp()
    {
        $method = new ReflectionMethod('Eresus_ORM_Driver_MySQL', 'getDefinitionForTimestamp');
        $method->setAccessible(true);
        $driver = new Eresus_ORM_Driver_MySQL();

        $this->assertEquals('INT(10) UNSIGNED', $method->invoke($driver, array()));
    }
}

