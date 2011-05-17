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
 * @package Kernel
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id: PHPTest.php 669 2010-12-04 10:36:49Z mk $
 */

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/Kernel/PHP.php';

/**
 * @package Kernel
 * @subpackage Tests
 */
class Eresus_Kernel_PHP_Test extends PHPUnit_Framework_TestCase
{
	private $inclue_path;

	/**
	 * Set up
	 * @see Framework/PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		// @coverageIgnoreStart
		$this->inclue_path = get_include_path();
		// @coverageIgnoreEnd
	}
	//-----------------------------------------------------------------------------

	/**
	 * Clean up
	 * @see Framework/PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		// @coverageIgnoreStart
		set_include_path($this->inclue_path);
		// @coverageIgnoreEnd
	}
	//-----------------------------------------------------------------------------

	/**
	 * Just make sure that method can be executed
	 *
	 * @covers Eresus_Kernel_PHP::isCGI
	 */
	public function test_lint_isCGI()
	{
		Eresus_Kernel_PHP::isCGI();
		$this->assertTrue(true);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Just make sure that method can be executed
	 *
	 * @covers Eresus_Kernel_PHP::isCLI
	 */
	public function test_lint_isCLI()
	{
		Eresus_Kernel_PHP::isCLI();
		$this->assertTrue(true);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Just make sure that method can be executed
	 *
	 * @covers Eresus_Kernel_PHP::isModule
	 */
	public function test_lint_isModule()
	{
		Eresus_Kernel_PHP::isModule();
		$this->assertTrue(true);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel_PHP::inOpenBaseDir
	 */
	public function test_inOpenBaseDir()
	{
		$this->assertTrue(Eresus_Kernel_PHP::inOpenBaseDir('/dir/file', false), 'Test 1');
		$cwd = getcwd();
		$this->assertFalse(Eresus_Kernel_PHP::inOpenBaseDir('/dir/file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 2');
		$this->assertTrue(Eresus_Kernel_PHP::inOpenBaseDir('/dir1/file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 3');
		$this->assertTrue(Eresus_Kernel_PHP::inOpenBaseDir('/dir2/file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 4');
		$this->assertTrue(Eresus_Kernel_PHP::inOpenBaseDir('/dir3/file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 5');
		$this->assertTrue(Eresus_Kernel_PHP::inOpenBaseDir('./file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 6');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel_PHP::classExists
	 */
	public function test_classExists()
	{
		$this->assertFalse(Eresus_Kernel_PHP::classExists('UnexistentClass'));
		$this->assertTrue(Eresus_Kernel_PHP::classExists('Eresus_Kernel_PHP_Test_Class'));
		$this->assertTrue(Eresus_Kernel_PHP::classExists('Eresus_Kernel_PHP_Test_Interface'));
	}
	//-----------------------------------------------------------------------------

	/* */
}


interface Eresus_Kernel_PHP_Test_Interface {};
class Eresus_Kernel_PHP_Test_Class {};
