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
require_once TESTS_SRC_DIR . '/core/Eresus/Kernel.php';
require_once TESTS_SRC_DIR . '/core/Eresus/Exceptions/SuccessException.php';
require_once TESTS_SRC_DIR . '/core/Eresus/Exceptions/ExitException.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_KernelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var mixed
     */
    private $error_log;

    /**
     * @var string
     */
    private $include_path;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->include_path = get_include_path();
        $this->error_log = ini_get('error_log');
    }

    /**
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        ini_set('error_log', $this->error_log);
        set_include_path($this->include_path);

        $filename = TESTS_SRC_DIR . '/core/Stub.php';
        if (is_file($filename))
        {
            unlink($filename);
        }

        $filename = TESTS_SRC_DIR . '/core/botobor/botobor.php';
        if (is_file($filename))
        {
            unlink($filename);
        }

        $filename = TESTS_SRC_DIR . '/core/botobor';
        if (is_dir($filename))
        {
            rmdir($filename);
        }
    }

    /**
     * @covers Eresus_Kernel::initExceptionHandling
     */
    public function testInitExceptionHandling()
    {
        $kernel = $this->getMockBuilder('Eresus_Kernel')->disableOriginalConstructor()->getMock();

        $method = new ReflectionMethod('Eresus_Kernel', 'initExceptionHandling');
        $method->setAccessible(true);
        $method->invoke($kernel);

        $this->assertTrue(isset($GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER']), 'No emergency buffer');
        $this->assertEquals(0, ini_get('html_errors'), '"html_errors" option is set');
    }

    /**
     * @ covers Eresus_Kernel::handleException
     * /
    public function test_handleException()
    {
        $e = new Exception;
        ini_set('error_log', '/dev/null');
        $this->expectOutputString("Unhandled Exception!\n");
        Eresus_Kernel::handleException($e);
    }*/

    /**
     * @covers Eresus_Kernel::errorHandler
     */
    public function testErrorHandlerAt()
    {
        @Eresus_Kernel::errorHandler(E_ERROR, 'Error', '/some/file', 123);
    }

    /**
     * @covers Eresus_Kernel::errorHandler
     */
    public function testErrorHandlerNotice()
    {
        Eresus_Kernel::errorHandler(E_NOTICE, 'Notice', '/some/file', 123);
    }

    /**
     * @covers Eresus_Kernel::errorHandler
     * @expectedException ErrorException
     */
    public function testErrorHandlerWarning()
    {
        Eresus_Kernel::errorHandler(E_WARNING, 'Warning', '/some/file', 123);
    }

    /**
     * @covers Eresus_Kernel::errorHandler
     * @expectedException ErrorException
     */
    public function testErrorHandlerError()
    {
        Eresus_Kernel::errorHandler(E_ERROR, 'Error', '/some/file', 123);
    }

    /**
     * @covers Eresus_Kernel::fatalErrorHandler
     * /
    public function test_fatalErrorHandler()
    {
        $this->assertFalse(Eresus_Kernel::fatalErrorHandler(''));
        Eresus_Config::set('eresus.cms.log.level', -1);
        $this->assertEquals(
            "PARSE ERROR\nSee application log for more info.\n",
            Eresus_Kernel::fatalErrorHandler('Parse error: A in B on line C'));
        $this->assertEquals(
            "FATAL ERROR\nSee application log for more info.\n",
            Eresus_Kernel::fatalErrorHandler('Fatal error: A in B on line C'));
    }*/

    /**
     * Just make sure that method can be executed
     *
     * @covers Eresus_Kernel::isCGI
     */
    public function testIsCgi()
    {
        Eresus_Kernel::isCGI();
        $this->assertTrue(true);
    }

    /**
     * Just make sure that method can be executed
     *
     * @covers Eresus_Kernel::isCLI
     */
    public function testIsCli()
    {
        Eresus_Kernel::isCLI();
        $this->assertTrue(true);
    }

    /**
     * Just make sure that method can be executed
     *
     * @covers Eresus_Kernel::isModule
     */
    public function testIsModule()
    {
        Eresus_Kernel::isModule();
        $this->assertTrue(true);
    }
}
