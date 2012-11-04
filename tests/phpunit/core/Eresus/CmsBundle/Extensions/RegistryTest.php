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

require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Eresus\CmsBundle\Extensions\Registry::autoload
     */
    public function testAutoload()
    {
        $plugins = $this->getMock('Eresus\CmsBundle\Extensions\Registry', array('load'));
        $plugins->expects($this->any())->method('load')->
            will($this->returnCallback(
                function ($a)
                {
                    return 'foo' == $a;
                }
            )
        );

        $app = $this->getMock('stdClass', array('getFsRoot'));
        $app->expects($this->any())->method('getFsRoot')->
            will($this->returnValue(TESTS_FIXT_DIR . '/core/Eresus/CmsBundle/Extensions/Registry/'));
        Eresus_Kernel::sc()->set('app', $app);

        // Нет такого файла
        $this->assertFalse($plugins->autoload('Baz_Foo_Bar'));

        // Файл есть, но плагин не активирован
        $this->assertFalse($plugins->autoload('Bar_Foo_Baz'));

        // Файл есть и плагин активирован
        $this->assertTrue($plugins->autoload('Foo_Bar_Baz'));
        $this->assertTrue(class_exists('Foo_Bar_Baz', false));
    }
}

