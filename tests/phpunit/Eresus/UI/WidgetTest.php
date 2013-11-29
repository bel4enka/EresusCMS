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
     * @covers Eresus\UI\Widget::getTemplateName
     * @covers Eresus\UI\Widget::setTemplateName
     */
    public function testTemplateName()
    {
        /** @var TemplateManager $tm */
        $tm = $this->getMockBuilder('Eresus\Templating\TemplateManager')
            ->disableOriginalConstructor()->getMock();
        $widget = new FooWidget($tm);
        $getTemplateName = new \ReflectionMethod('Eresus\UI\Widget', 'getTemplateName');
        $getTemplateName->setAccessible(true);
        $this->assertEquals('UI/Tests/FooWidget.html', $getTemplateName->invoke($widget));
        $widget->setTemplateName('foo.html');
        $this->assertEquals('foo.html', $getTemplateName->invoke($widget));
    }

    /**
     * @covers Eresus\UI\Widget::__construct
     * @covers Eresus\UI\Widget::getTemplateManager
     * @covers Eresus\UI\Widget::setTemplateManager
     */
    public function testTemplateManager()
    {
        /** @var TemplateManager $tm1 */
        $tm1 = $this->getMockBuilder('Eresus\Templating\TemplateManager')
            ->disableOriginalConstructor()->getMock();
        $tm2 = clone $tm1;
        $widget = new FooWidget($tm1);
        $getTemplateManager = new \ReflectionMethod('Eresus\UI\Widget', 'getTemplateManager');
        $getTemplateManager->setAccessible(true);
        $this->assertSame($tm1, $getTemplateManager->invoke($widget));
        $widget->setTemplateManager($tm2);
        $this->assertSame($tm2, $getTemplateManager->invoke($widget));
    }

    /**
     * @covers Eresus\UI\Widget::getTemplateManager
     * @expectedException \LogicException
     */
    public function testTemplateManagerNull()
    {
        $widget = $this->getMockBuilder('Eresus\UI\Widget')->setMethods(array('none'))
            ->disableOriginalConstructor()->getMock();
        $getTemplateManager = new \ReflectionMethod('Eresus\UI\Widget', 'getTemplateManager');
        $getTemplateManager->setAccessible(true);
        $getTemplateManager->invoke($widget);
    }

    /**
     * @covers Eresus\UI\Widget::getTemplate
     */
    public function testGetTemplate()
    {
        /** @var TemplateManager $tm */
        $tm = $this->getMockBuilder('Eresus\Templating\TemplateManager')
            ->disableOriginalConstructor()->getMock();
        $widget = new FooWidget($tm);
        $getTemplate = new \ReflectionMethod('Eresus\UI\Widget', 'getTemplate');
        $getTemplate->setAccessible(true);
        $t1 = $getTemplate->invoke($widget);
        $t2 = $getTemplate->invoke($widget);
        $this->assertSame($t1, $t2);
    }

    /**
     * @covers Eresus\UI\Widget::getHtml
     */
    public function testGetHtml()
    {
        $template = $this->getMock('stdClass', array('compile'));
        $widget = $this->getMockBuilder('Eresus\UI\Widget')->setMethods(array('getTemplate'))
            ->disableOriginalConstructor()->getMock();
        $widget->expects($this->any())->method('getTemplate')->will($this->returnValue($template));
        $template->expects($this->once())->method('compile')->with(array('widget' => $widget))
            ->will($this->returnValue('foo'));
        /** @var Widget $widget */
        $this->assertEquals('foo', $widget->getHtml());
    }
}

/**
 * Описываем класс статически, т. к. PHPUnit 3.7.x не позволяет создавать моки в пространствах имён
 */
class FooWidget extends Widget
{
}

