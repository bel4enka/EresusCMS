<?php
/**
 * ${product.title} ${product.version}
 *
 * Модульные тесты
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
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

namespace Tests\Eresus\CmsBundle;

use Eresus\CmsBundle\WebPage;

require_once __DIR__ . '/../../../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/Eresus/CmsBundle/WebPage.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class WebPageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Проверяем WebPage::linkScripts
     *
     * @covers Eresus\CmsBundle\WebPage::linkScripts
     * @covers Eresus\CmsBundle\WebPage::renderHeadSection
     * @covers Eresus\CmsBundle\WebPage::renderBodySection
     */
    public function testLinkScripts()
    {
        if (version_compare(PHP_VERSION, '5.3', '<'))
        {
            $this->markTestSkipped('PHP 5.3 required');
        }

        $page = new WebPage();
        $page->linkScripts('head.js');
        $page->linkScripts('body.js', 'defer');

        $renderHeadSection = new \ReflectionMethod('Eresus\CmsBundle\WebPage', 'renderHeadSection');
        $renderHeadSection->setAccessible(true);
        $this->assertEquals('<script type="text/javascript" src="head.js"></script>',
            $renderHeadSection->invoke($page));

        $renderBodySection = new \ReflectionMethod( 'Eresus\CmsBundle\WebPage', 'renderBodySection');
        $renderBodySection->setAccessible(true);
        $this->assertEquals('<script type="text/javascript" src="body.js" defer></script>',
            $renderBodySection->invoke($page));
    }

    /**
     * Проверяем WebPage::addScripts
     *
     * @covers Eresus\CmsBundle\WebPage::addScripts
     * @covers Eresus\CmsBundle\WebPage::renderHeadSection
     * @covers Eresus\CmsBundle\WebPage::renderBodySection
     */
    public function testAddScripts()
    {
        $page = new WebPage();
        $page->addScripts('var head;');
        $page->addScripts('var body;', 'body');

        $this->assertEquals(
            "<script type=\"text/javascript\">//<!-- <![CDATA[\nvar head;\n//]] --></script>",
            $page->renderHeadSection());

        $renderBodySection = new \ReflectionMethod( 'Eresus\CmsBundle\WebPage', 'renderBodySection');
        $renderBodySection->setAccessible(true);
        $this->assertEquals(
            "<script type=\"text/javascript\">//<!-- <![CDATA[\nvar body;\n//]] --></script>",
            $renderBodySection->invoke($page)
        );
    }

    /**
     * @covers Eresus\CmsBundle\WebPage::setMetaHeader
     */
    public function testSetMetaHeader()
    {
        $p_head = new \ReflectionProperty( 'Eresus\CmsBundle\WebPage', 'head');
        $p_head->setAccessible(true);

        $page = new WebPage();
        $page->setMetaHeader('foo', 'bar');

        $head = $p_head->getValue($page);

        $this->assertArrayHasKey('foo', $head['meta-http']);
        $this->assertEquals('bar', $head['meta-http']['foo']);
    }

    /**
     * @covers Eresus\CmsBundle\WebPage::setMetaTag
     */
    public function testSetMetaTag()
    {
        $p_head = new \ReflectionProperty( 'Eresus\CmsBundle\WebPage', 'head');
        $p_head->setAccessible(true);

        $page = new WebPage();
        $page->setMetaTag('bar', 'foo');

        $head = $p_head->getValue($page);

        $this->assertArrayHasKey('bar', $head['meta-tags']);
        $this->assertEquals('foo', $head['meta-tags']['bar']);
    }

    /**
     * @covers Eresus\CmsBundle\WebPage::linkStyles
     */
    public function testLinkStyles()
    {
        $p_head = new \ReflectionProperty( 'Eresus\CmsBundle\WebPage', 'head');
        $p_head->setAccessible(true);

        $page = new WebPage();
        $page->linkStyles('foo', 'bar');

        $head = $p_head->getValue($page);

        $this->assertCount(1, $head['link']);
        $this->assertEquals('foo', $head['link'][0]['href']);
        $this->assertEquals('bar', $head['link'][0]['media']);

        $page->linkStyles('bar', 'foo');
        $head = $p_head->getValue($page);

        $page->linkStyles('foo', 'bar');
        $head = $p_head->getValue($page);

        $this->assertCount(2, $head['link']);

    }

    /**
     * @covers Eresus\CmsBundle\WebPage::addStyles
     */
    public function testAddStyles()
    {
        $p_head = new \ReflectionProperty( 'Eresus\CmsBundle\WebPage', 'head');
        $p_head->setAccessible(true);

        $page = new WebPage();
        $page->addStyles(' foo', 'bar');
        $head = $p_head->getValue($page);

        $this->assertCount(1, $head['style']);
        $this->assertEquals('		foo', $head['style'][0]['content']);
        $this->assertEquals('bar', $head['style'][0]['media']);

        $page->addStyles('bar', 'foo');
        $head = $p_head->getValue($page);

        $this->assertCount(2, $head['style']);
        $this->assertEquals("	bar", $head['style'][1]['content']);
        $this->assertEquals('foo', $head['style'][1]['media']);
    }
}

