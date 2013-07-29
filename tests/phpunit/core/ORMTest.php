<?php
/**
 * Тесты класса Eresus_ORM
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

require_once __DIR__ . '/../bootstrap.php';

/**
 * @package Eresus
 * @subpackage ORM
 */
class Eresus_ORMTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus_ORM::getTable
     * @expectedException InvalidArgumentException
     */
    public function testGetTableInvalidPlugin()
    {
        Eresus_ORM::getTable(null, 'Foo');
    }

    /**
     * @covers Eresus_ORM::getTable
     */
    public function testGetTableNewPlugin()
    {
        $uid = uniqid();
        $this->getMockBuilder('Eresus_ORM_Table')
            ->setMockClassName("Plugin_{$uid}_Entity_Table_{$uid}")
            ->setMethods(array('setTableDefinition'))->disableOriginalConstructor()->getMock();
        /** @var Eresus_Plugin $plugin */
        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()
            ->setMockClassName("Plugin_{$uid}")->getMock();
        $this->assertInstanceOf("Plugin_{$uid}_Entity_Table_{$uid}",
            Eresus_ORM::getTable($plugin, $uid));
    }

    /**
     * @covers Eresus_ORM::getTable
     */
    public function testGetTableOldPlugin()
    {
        $uid = uniqid();
        $this->getMockBuilder('Eresus_ORM_Table')
            ->setMockClassName("Plugin_{$uid}_Entity_Table_{$uid}")
            ->setMethods(array('setTableDefinition'))->disableOriginalConstructor()->getMock();
        /** @var TContentPlugin $plugin */
        $plugin = $this->getMockBuilder('TContentPlugin')->disableOriginalConstructor()
            ->setMockClassName("Plugin_{$uid}")->getMock();
        $this->assertInstanceOf("Plugin_{$uid}_Entity_Table_{$uid}",
            Eresus_ORM::getTable($plugin, $uid));
    }
}

