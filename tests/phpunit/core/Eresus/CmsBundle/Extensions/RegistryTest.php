<?php
/**
 * Тесты реестра модулей расширения
 *
 * @version ${product.version}
 * @copyright 2012, Михаил Красильников <m.krasilnikov@yandex.ru>
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

namespace Tests\Eresus\CmsBundle\Extensions;

use Eresus\CmsBundle\Extensions\Registry;
use Tests\Container as TestContainer;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/../../../../bootstrap.php';

/**
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
        vfsStream::setup('root', null, array(
            'config' => array(
                'plugins.yml' => "Acme\\Bundle:\n enabled: true\n"
            ),
            'plugins' => array(
                'Acme' => array(
                    'Bundle' => array(
                        'plugin.yml' =>
                            "title: 'Foo'\nversion: '1.00'\nrequire: {CMS: {min: '4.00'}}",
                    )
                ),
            ),
        ));

        $configLocator = $this->getMock('stdClass', array('locate'));
        $configLocator->expects($this->any())->method('locate')->with('plugins.yml')
            ->will($this->returnValue(vfsStream::url('root/config/plugins.yml')));

        $container = new TestContainer();
        $container->set('config_locator', $configLocator);

        $kernel = $this->getMock('stdClass', array('getRootDir'));
        $kernel->expects($this->any())->method('getRootDir')
            ->will($this->returnValue(vfsStream::url('root')));
        $container->set('kernel', $kernel);

        $registry = $this->getMockBuilder('Eresus\CmsBundle\Extensions\Registry')
            ->disableOriginalConstructor()->setMethods(array('register'))->getMock();
        $registry->setContainer($container);

        $init = new \ReflectionMethod('Eresus\CmsBundle\Extensions\Registry', 'init');
        $init->setAccessible(true);

        // Загружаем функции
        class_exists('\Eresus\CmsBundle\EresusCmsBundle');
        $init->invoke($registry);
    }
}

