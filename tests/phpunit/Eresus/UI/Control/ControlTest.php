<?php
/**
 * Тесты класса Eresus\UI\Control\Control
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

namespace Eresus\UI\Control\Tests;

use Eresus\Templating\TemplateManager;
use Eresus\UI\Control\Control;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * Тесты класса Eresus\UI\Control\Control
 */
class ControlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus\UI\Control\Control::getActionName
     * @covers Eresus\UI\Control\Control::getType
     */
    public function testGetActionName()
    {
        /** @var TemplateManager $tm */
        $tm = $this->getMockBuilder('Eresus\Templating\TemplateManager')
            ->disableOriginalConstructor()->getMock();
        $control = new FooControl($tm);
        $this->assertEquals('foo', $control->getActionName());
        $this->assertEquals('foo', $control->getType());
    }

    /**
     * @covers Eresus\UI\Control\Control::getLabel
     * @covers Eresus\UI\Control\Control::setLabel
     */
    public function testLabel()
    {
        /** @var TemplateManager $tm */
        $tm = $this->getMockBuilder('Eresus\Templating\TemplateManager')
            ->disableOriginalConstructor()->getMock();
        $control = new FooControl($tm);
        $this->assertEquals('Foo', $control->getLabel());
        $control->setLabel('Bar');
        $this->assertEquals('Bar', $control->getLabel());
    }

    /**
     * @covers Eresus\UI\Control\Control::getActionUrl
     * @covers Eresus\UI\Control\Control::setActionUrl
     * @covers Eresus\UI\Control\Control::setUrlBuilder
     */
    public function testActionUrl()
    {
        /** @var TemplateManager $tm */
        $tm = $this->getMockBuilder('Eresus\Templating\TemplateManager')
            ->disableOriginalConstructor()->getMock();
        $control = new FooControl($tm);
        $this->assertEquals('foo', $control->getActionUrl());

        $control = new FooControl($tm);
        $control->setActionUrl('http://example.org/foo');
        $this->assertEquals('http://example.org/foo', $control->getActionUrl());
        $urlBuilder = $this->getMockForAbstractClass(
            'Eresus\UI\Control\UrlBuilder\UrlBuilderInterface',
            array('getActionUrl'));
        $urlBuilder->expects($this->once())->method('getActionUrl')->with('foo')
            ->will($this->returnValue('#foo'));

        $control = new FooControl($tm);
        $control->setUrlBuilder($urlBuilder);
        $this->assertEquals('#foo', $control->getActionUrl());
    }

    /**
     * @covers Eresus\UI\Control\Control::getStyle
     * @covers Eresus\UI\Control\Control::setStyle
     */
    public function testStyle()
    {
        /** @var TemplateManager $tm */
        $tm = $this->getMockBuilder('Eresus\Templating\TemplateManager')
            ->disableOriginalConstructor()->getMock();
        $control = new FooControl($tm);
        $this->assertEquals(Control::STYLE_LINK, $control->getStyle());
        $control->setStyle(Control::STYLE_BUTTON);
        $this->assertEquals(Control::STYLE_BUTTON, $control->getStyle());
    }

    /**
     * @covers Eresus\UI\Control\Control::getIconUrl
     */
    public function testGetIconUrl()
    {
        /** @var TemplateManager $tm */
        $tm = $this->getMockBuilder('Eresus\Templating\TemplateManager')
            ->disableOriginalConstructor()->getMock();
        $control = new FooControl($tm);
        $this->assertNull($control->getIconUrl());
    }

    /**
     * @covers Eresus\UI\Control\Control::getHint
     */
    public function testHint()
    {
        /** @var TemplateManager $tm */
        $tm = $this->getMockBuilder('Eresus\Templating\TemplateManager')
            ->disableOriginalConstructor()->getMock();
        $control = new FooControl($tm);
        $this->assertEquals('', $control->getHint());
    }

    /**
     * @covers Eresus\UI\Control\Control::getClientHandler
     */
    public function testGetClientHandler()
    {
        /** @var TemplateManager $tm */
        $tm = $this->getMockBuilder('Eresus\Templating\TemplateManager')
            ->disableOriginalConstructor()->getMock();
        $control = new FooControl($tm);
        $this->assertNull($control->getClientHandler());
    }
}

/**
 * Описываем класс статически, т. к. PHPUnit 3.7.x не позволяет создавать моки в пространствах имён
 */
class FooControl extends Control
{
}

