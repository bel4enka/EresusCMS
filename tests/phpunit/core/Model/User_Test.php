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

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/DBAL/EresusActiveRecord.php';
require_once dirname(__FILE__) . '/../../../../main/core/Model/User.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_Model_User_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Model_User::setTableDefinition
	 */
	public function test_setTableDefinition()
	{
		$test = $this->getMockBuilder('Eresus_Model_User')->
			setMethods(array('setTableName', 'hasColumns'))->disableOriginalConstructor()->getMock();
		$test->expects($this->once())->method('setTableName')->with('users');
		$test->expects($this->once())->method('hasColumns');
		$test->setTableDefinition();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Model_User::setUp
	 */
	public function test_setUp()
	{
		$test = $this->getMockBuilder('Eresus_Model_User')->
			setMethods(array('hasAccessorMutator', 'hasMutator'))
			->disableOriginalConstructor()->getMock();
		$test->expects($this->once())->method('hasAccessorMutator')->
			with('profile', 'unserializeAccessor', 'serializeMutator');
		$test->expects($this->exactly(2))->method('hasMutator');
		$test->setUp();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Model_User::usernameMutator
	 */
	public function test_usernameMutator()
	{
		$user = $this->getMock('Eresus_Model_User', array('_set'));
		$user->expects($this->once())->method('_set')->with('username', 'user');
		$user->usernameMutator('user');

		$user = $this->getMock('Eresus_Model_User', array('_set'));
		$user->expects($this->once())->method('_set')->with('username', '');
		$user->usernameMutator('тест');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Model_User::passwordMutator
	 * @covers Eresus_Model_User::passwordHash
	 */
	public function test_passwordMutator()
	{
		$user = $this->getMock('Eresus_Model_User', array('_set'));
		$user->expects($this->once())->method('_set')->
			with('password', $user->passwordHash('test'));
		$user->passwordMutator('test');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Model_User::isPasswordValid
	 */
	public function test_isPasswordValid()
	{
		$user = new Eresus_Model_User;
		$user->password = $user->passwordHash('test');
		$this->assertTrue($user->isPasswordValid('test'));
		$this->assertFalse($user->isPasswordValid(''));
	}
	//-----------------------------------------------------------------------------

	/* */
}
