<?php
/**
 * Тесты класса Eresus_Plugin
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

require_once dirname(__FILE__) . '/../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/Plugin.php';
require_once TESTS_SRC_DIR . '/core/Plugin/Templates.php';

/**
 * Тесты класса Eresus_Plugin
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_PluginTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus_Plugin::getDataURL
     */
    public function testGetDataURL()
    {
        $GLOBALS['Eresus'] = new stdClass();
        $GLOBALS['Eresus']->froot = '/home/example.org/';
        $GLOBALS['Eresus']->fdata = '/home/example.org/data/';
        $GLOBALS['Eresus']->fstyle = '/home/example.org/style/';
        $GLOBALS['Eresus']->root = 'http://example.org/';
        $GLOBALS['Eresus']->data = 'http://example.org/data/';
        $GLOBALS['Eresus']->style = 'http://example.org/style/';
        $plugin = $this->getMock('Eresus_Plugin', array('_'));
        /** @var Eresus_Plugin $plugin */
        $this->assertEquals('http://example.org/data/plugin/', $plugin->getDataURL());
    }

    /**
     * @covers Eresus_Plugin::getCodeURL
     */
    public function testGetCodeURL()
    {
        $GLOBALS['Eresus'] = new stdClass();
        $GLOBALS['Eresus']->froot = '/home/example.org/';
        $GLOBALS['Eresus']->fdata = '/home/example.org/data/';
        $GLOBALS['Eresus']->fstyle = '/home/example.org/style/';
        $GLOBALS['Eresus']->root = 'http://example.org/';
        $GLOBALS['Eresus']->data = 'http://example.org/data/';
        $GLOBALS['Eresus']->style = 'http://example.org/style/';
        $plugin = $this->getMock('Eresus_Plugin', array('_'));
        /** @var Eresus_Plugin $plugin */
        $this->assertEquals('http://example.org/ext/plugin/', $plugin->getCodeURL());
    }

    /**
     * @covers Eresus_Plugin::getStyleURL
     */
    public function testGetStyleURL()
    {
        $GLOBALS['Eresus'] = new stdClass();
        $GLOBALS['Eresus']->froot = '/home/example.org/';
        $GLOBALS['Eresus']->fdata = '/home/example.org/data/';
        $GLOBALS['Eresus']->fstyle = '/home/example.org/style/';
        $GLOBALS['Eresus']->root = 'http://example.org/';
        $GLOBALS['Eresus']->data = 'http://example.org/data/';
        $GLOBALS['Eresus']->style = 'http://example.org/style/';
        $plugin = $this->getMock('Eresus_Plugin', array('_'));
        /** @var Eresus_Plugin $plugin */
        $this->assertEquals('http://example.org/style/plugin/', $plugin->getStyleURL());
    }

    /**
     * Тест метода templates
     *
     * @covers Eresus_Plugin::templates
     */
    public function testTemplates()
    {
        $legacyKernel = $this->getMock('Eresus');
        $GLOBALS['Eresus'] = $legacyKernel;
        $plugin = $this->getMock('Eresus_Plugin', array('_'));
        /** @var Eresus_Plugin $plugin */
        $templates = $plugin->templates();
        $this->assertInstanceOf('Eresus_Plugin_Templates', $templates);
        $this->assertSame($templates, $plugin->templates());
    }
}

