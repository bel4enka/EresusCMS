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

namespace Tests\Eresus\CmsBundle\Extensions;

use Eresus_Kernel;
use vfsStreamWrapper;
use vfsStream;

require_once __DIR__ . '/../../../../bootstrap.php';
require_once 'vfsStream/vfsStream.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Eresus\CmsBundle\Extensions\Registry::get
     */
    public function testGetCache()
    {
        $registry = $this->getMockBuilder('Eresus\CmsBundle\Extensions\Registry')
            ->disableOriginalConstructor()->setMethods(array('none'))->getMock();

        $plugin = new \stdClass();

        $plugins = new \ReflectionProperty('Eresus\CmsBundle\Extensions\Registry', 'plugins');
        $plugins->setAccessible(true);
        $plugins->setValue($registry, array('Acme\Foo' => $plugin));

        $p = $registry->get('Acme\Foo');
        $this->assertSame($plugin, $p);
    }

    /**
     * @covers \Eresus\CmsBundle\Extensions\Registry::get
     */
    public function testGetNotInstalled()
    {
        $registry = $this->getMockBuilder('Eresus\CmsBundle\Extensions\Registry')
            ->disableOriginalConstructor()->setMethods(array('none'))->getMock();

        $this->assertFalse($registry->get('Acme\Foo'));
    }

    /**
     * @covers \Eresus\CmsBundle\Extensions\Registry::get
     */
    public function testGetDisabled()
    {
        $registry = $this->getMockBuilder('Eresus\CmsBundle\Extensions\Registry')
            ->disableOriginalConstructor()->setMethods(array('none'))->getMock();

        $config = new \ReflectionProperty('Eresus\CmsBundle\Extensions\Registry', 'config');
        $config->setAccessible(true);
        $config->setValue($registry, array('Acme\Foo' => array('enabled' => false)));

        $this->assertFalse($registry->get('Acme\Foo'));
    }

    /**
     * @covers \Eresus\CmsBundle\Extensions\Registry::get
     */
    public function testGetNew()
    {
        $conf = array('enabled' => true, 'settings' => array('a' => 'b'));
        $plugin = $this->getMock('stdClass', array('setContainer'));

        $registry = $this->getMockBuilder('Eresus\CmsBundle\Extensions\Registry')
            ->disableOriginalConstructor()->setMethods(array('createPluginInstance'))->getMock();
        $registry->expects($this->once())->method('createPluginInstance')
            ->with('Acme\Foo', $conf)->will($this->returnValue($plugin));

        $config = new \ReflectionProperty('Eresus\CmsBundle\Extensions\Registry', 'config');
        $config->setAccessible(true);
        $config->setValue($registry, array('Acme\Foo' => $conf));

        $this->assertSame($plugin, $registry->get('Acme\Foo'));
        $this->assertSame($plugin, $registry->get('Acme\Foo')); // Проверяем помещение кэш
    }

    /**
     * @covers \Eresus\CmsBundle\Extensions\Registry::getDbFilename
     */
    public function testGetDbFilename()
    {
        $configLocator = $this->getMock('stdClass', array('locate'));
        $configLocator->expects($this->any())->method('locate')->with('plugins.yml')
            ->will($this->returnValue('/path/to/site/plugins.yml'));

        $container = new \Tests\Container();
        $container->set('config_locator', $configLocator);

        $registry = $this->getMockBuilder('Eresus\CmsBundle\Extensions\Registry')
            ->disableOriginalConstructor()->setMethods(array('none'))->getMock();
        $registry->setContainer($container);

        $getDbFilename = new \ReflectionMethod('Eresus\CmsBundle\Extensions\Registry',
            'getDbFilename');
        $getDbFilename->setAccessible(true);

        $this->assertEquals('/path/to/site/plugins.yml', $getDbFilename->invoke($registry));
    }

    /**
     * @covers \Eresus\CmsBundle\Extensions\Registry::init
     */
    public function testInit()
    {
        vfsStreamWrapper::register();
        vfsStream::setup('root', null, array('config' => array(
            'plugins.yml' => "Acme\\Bundle:\n enabled: true\n"
        )));

        $configLocator = $this->getMock('stdClass', array('locate'));
        $configLocator->expects($this->any())->method('locate')->with('plugins.yml')
            ->will($this->returnValue(vfsStream::url('config/plugins.yml')));

        $container = new \Tests\Container();
        $container->set('config_locator', $configLocator);

        $registry = $this->getMockBuilder('Eresus\CmsBundle\Extensions\Registry')
            ->disableOriginalConstructor()->setMethods(array('get'))->getMock();
        $registry->expects($this->once())->method('get')->with('Acme\Bundle');
        $registry->setContainer($container);

        $init = new \ReflectionMethod('Eresus\CmsBundle\Extensions\Registry', 'init');
        $init->setAccessible(true);

        $init->invoke($registry);
    }
}

