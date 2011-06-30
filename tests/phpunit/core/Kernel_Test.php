<?php
/**
 * ${product.title}
 *
 * @version ${product.version}
 *
 * PhpUnit Tests
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EresusCMS
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * $Id$
 */

require_once 'PHPUnit/Extensions/OutputTestCase.php';

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/Kernel.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_Kernel_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @var mixed
	 */
	private $error_log;

	/**
	 * @var string
	 */
	private $inclue_path;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		$this->inclue_path = get_include_path();
		$this->error_log = ini_get('error_log');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		ini_set('error_log', $this->error_log);
		set_include_path($this->inclue_path);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::initExceptionHandling
	 */
	public function test_initExceptionHandling()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('This test requires at PHP 5.3.2 or higher');
		}
		$method = new ReflectionMethod('Eresus_Kernel', 'initExceptionHandling');
		$method->setAccessible(true);
		$method->invoke(null);

		$this->assertTrue(isset($GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER']), 'No emergency buffer');
		$this->assertEquals(0, ini_get('html_errors'), '"html_errors" option is set');

	}
	//-----------------------------------------------------------------------------

	/**
	 * @ covers Eresus_Kernel::handleException
	 * /
	public function test_handleException()
	{
		$e = new Exception;
		ini_set('error_log', '/dev/null');
		$this->expectOutputString("Unhandled Exception!\n");
		Eresus_Kernel::handleException($e);
	}
	//-----------------------------------------------------------------------------

	/**
	 */
	public function test_errorHandler_at()
	{
		@Eresus_Kernel::errorHandler(E_ERROR, 'Error', '/some/file', 123);
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function test_errorHandler_NOTICE()
	{
		Eresus_Kernel::errorHandler(E_NOTICE, 'Notice', '/some/file', 123);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @expectedException ErrorException
	 */
	public function test_errorHandler_WARNING()
	{
		Eresus_Kernel::errorHandler(E_WARNING, 'Warning', '/some/file', 123);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @expectedException ErrorException
	 */
	public function test_errorHandler_ERROR()
	{
		Eresus_Kernel::errorHandler(E_ERROR, 'Error', '/some/file', 123);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::exec
	 */
	public function testExecOk()
	{
		$this->assertEquals(123, Eresus_Kernel::exec('Eresus_Kernel_Test_Application1'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::exec
	 * @expectedException LogicException
	 */
	public function testExecInvalidClass()
	{
		Eresus_Kernel::exec('StdClass');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::exec
	 * @expectedException LogicException
	 */
	public function testExecUnexistentClass()
	{
		Eresus_Kernel::exec('UnexistentClass');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::exec
	 */
	public function testExecAppWithException()
	{
		$this->assertEquals(0xFFFF, Eresus_Kernel::exec('Eresus_Kernel_Test_Application2'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::exec
	 */
	public function test_ExecAppWith_with_SuccessException()
	{
		$this->assertEquals(0, Eresus_Kernel::exec('Eresus_Kernel_Test_Application3'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Just make sure that method can be executed
	 *
	 * @covers Eresus_Kernel::isCGI
	 */
	public function test_lint_isCGI()
	{
		Eresus_Kernel::isCGI();
		$this->assertTrue(true);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Just make sure that method can be executed
	 *
	 * @covers Eresus_Kernel::isCLI
	 */
	public function test_lint_isCLI()
	{
		Eresus_Kernel::isCLI();
		$this->assertTrue(true);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Just make sure that method can be executed
	 *
	 * @covers Eresus_Kernel::isModule
	 */
	public function test_lint_isModule()
	{
		Eresus_Kernel::isModule();
		$this->assertTrue(true);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::inOpenBaseDir
	 */
	public function test_inOpenBaseDir()
	{
		$this->assertTrue(Eresus_Kernel::inOpenBaseDir('/dir/file', false), 'Test 1');
		$cwd = getcwd();
		$this->assertFalse(Eresus_Kernel::inOpenBaseDir('/dir/file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 2');
		$this->assertTrue(Eresus_Kernel::inOpenBaseDir('/dir1/file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 3');
		$this->assertTrue(Eresus_Kernel::inOpenBaseDir('/dir2/file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 4');
		$this->assertTrue(Eresus_Kernel::inOpenBaseDir('/dir3/file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 5');
		$this->assertTrue(Eresus_Kernel::inOpenBaseDir('./file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 6');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::classExists
	 */
	public function test_classExists()
	{
		$this->assertFalse(Eresus_Kernel::classExists('UnexistentClass'));
		$this->assertTrue(Eresus_Kernel::classExists('Eresus_Kernel_Test_Class'));
		$this->assertTrue(Eresus_Kernel::classExists('Eresus_Kernel_Test_Interface'));
	}
	//-----------------------------------------------------------------------------

	/* */
}


// @codeCoverageIgnoreStart
/**
 * EresusApplication stub
 */
class Eresus_Kernel_Test_Application1
{
	/**
	 * (non-PHPdoc)
	 * @see core/EresusApplication#main()
	 */
	public function main()
	{
		return 123;
	}
	//-----------------------------------------------------------------------------
}

/**
 * EresusApplication stub
 *
 */
class Eresus_Kernel_Test_Application2
{
	/**
	 * (non-PHPdoc)
	 * @see core/EresusApplication#main()
	 */
	public function main()
	{
		throw new RuntimeException('Message');
	}
	//-----------------------------------------------------------------------------
}

/**
 * EresusApplication stub
 *
 */
class Eresus_Kernel_Test_Application3
{
	/**
	 * (non-PHPdoc)
	 * @see core/EresusApplication#main()
	 */
	public function main()
	{
		throw new Eresus_ExitException;
	}
	//-----------------------------------------------------------------------------
}

/**
 * Autoloader stub
 * @param string $class
 */
function Eresus_Kernel_Test_autoloader($class)
{

}
//-----------------------------------------------------------------------------

/**
 *
 */
function Eresus_Kernel_Test_error_handler()
{

}
//-----------------------------------------------------------------------------
 /* */

interface Eresus_Kernel_Test_Interface {};
class Eresus_Kernel_Test_Class {};

// @codeCoverageIgnoreEnd

