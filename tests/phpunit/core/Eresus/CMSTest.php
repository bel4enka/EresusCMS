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

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMSTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus_CMS::getPage
     */
    public function test_getPage()
    {
        $p_page = new ReflectionProperty("Eresus_CMS", "page");
        $p_page->setAccessible(true);

        $eresus = new Eresus_CMS();
        $p_page->setValue($eresus,'foo');

        $this->assertEquals('foo', $eresus->getPage());
    }

    /**
     * @covers Eresus_CMS::runWeb
     */
    public function test_runWeb()
    {
        $runWeb = new ReflectionMethod('Eresus_CMS', 'runWeb');
        $runWeb->setAccessible(true);

        $cms = $this->getMock('Eresus_CMS',
            array('initWeb', 'call3rdPartyExtension', 'runWebAdminUi', 'runWebClientUi'));
        $cms->expects($this->once())->method('call3rdPartyExtension');
        $cms->expects($this->once())->method('runWebAdminUi');
        $cms->expects($this->once())->method('runWebClientUi');

        /* call3rdPartyExtension */
        $request = $this->getMock('stdClass', array('getLocalUrl'));
        $request->expects($this->any())->method('getLocalUrl')->will($this->returnValue('/ext-3rd'));
        Eresus_Kernel::sc()->set('request', $request);
        $runWeb->invoke($cms);

        /* runWebAdminUi */
        $request = $this->getMock('stdClass', array('getLocalUrl'));
        $request->expects($this->any())->method('getLocalUrl')->will($this->returnValue('/admin'));
        Eresus_Kernel::sc()->set('request', $request);
        $runWeb->invoke($cms);

        /* runWebClientUi */
        $request = $this->getMock('stdClass', array('getLocalUrl'));
        $request->expects($this->any())->method('getLocalUrl')->will($this->returnValue('/'));
        Eresus_Kernel::sc()->set('request', $request);
        $runWeb->invoke($cms);
    }
}
