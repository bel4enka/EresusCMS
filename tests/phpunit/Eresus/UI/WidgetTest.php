<?php
/**
 * Тесты класса Eresus\UI\Widget
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
 */

namespace Eresus\UI\Tests;

use Eresus\Templating\TemplateManager;
use Eresus\UI\Widget;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * Тесты класса Eresus\UI\Widget
 */
class WidgetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus\UI\Widget::__toString
     */
    public function testToString()
    {
        $widget = $this->getMockBuilder('Eresus\UI\Widget')->disableOriginalConstructor()
            ->setMethods(array('getHtml'))->getMock();
        $widget->expects($this->once())->method('getHtml')->will($this->returnValue('foo'));
        /** @var Widget $widget */
        $this->assertEquals('foo', strval($widget));

        $widget = $this->getMockBuilder('Eresus\UI\Widget')->disableOriginalConstructor()
            ->setMethods(array('getHtml'))->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject $widget */
        $widget->expects($this->once())->method('getHtml')
            ->will($this->throwException(new \Exception('bar')));
        /** @var Widget $widget */
        $this->assertEquals('Exception: bar', strval($widget));
    }

    /**
     * @covers Eresus\UI\Widget::getTemplateName
     */
    public function testGetTemplateName()
    {
        /** @var TemplateManager $tm */
        $tm = $this->getMockBuilder('Eresus\Templating\TemplateManager')
            ->disableOriginalConstructor()->getMock();
        $widget = new FooWidget($tm);
        $getTemplateName = new \ReflectionMethod('Eresus\UI\Widget', 'getTemplateName');
        $getTemplateName->setAccessible(true);
        $this->assertEquals('UI/Tests/FooWidget.html', $getTemplateName->invoke($widget));
    }
}

/**
 * Описываем класс статически, т. к. PHPUnit 3.7.x не позволяет создавать моки в пространствах имён
 */
class FooWidget extends Widget
{
}

