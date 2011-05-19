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

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/Kernel.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_Kernel_Test extends PHPUnit_Framework_TestCase
{
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
		throw new ExitException;
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

// @codeCoverageIgnoreEnd
