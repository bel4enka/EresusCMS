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
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/DBAL/EresusActiveRecord.php';
require_once dirname(__FILE__) . '/../../../../main/core/Domain/EresusUser.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class EresusUserTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers EresusUser::setTableDefinition
	 */
	public function test_setTableDefinition()
	{
		$test = $this->getMockBuilder('EresusUser')->
			setMethods(array('setTableName', 'hasColumns'))->disableOriginalConstructor()->getMock();
		$test->expects($this->once())->method('setTableName')->with('users');
		$test->expects($this->once())->method('hasColumns');
		$test->setTableDefinition();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusUser::setUp
	 */
	public function test_setUp()
	{
		$test = $this->getMockBuilder('EresusUser')->
			setMethods(array('hasAccessorMutator', 'hasMutator'))
			->disableOriginalConstructor()->getMock();
		$test->expects($this->once())->method('hasAccessorMutator')->
			with('profile', 'unserializeAccessor', 'serializeMutator');
		$test->expects($this->exactly(2))->method('hasMutator');
		$test->setUp();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusUser::usernameMutator
	 */
	public function test_usernameMutator()
	{
		$user = $this->getMock('EresusUser', array('_set'));
		$user->expects($this->once())->method('_set')->with('username', 'user');
		$user->usernameMutator('user');

		$user = $this->getMock('EresusUser', array('_set'));
		$user->expects($this->once())->method('_set')->with('username', '');
		$user->usernameMutator('тест');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusUser::passwordMutator
	 * @covers EresusUser::passwordHash
	 */
	public function test_passwordMutator()
	{
		$user = $this->getMock('EresusUser', array('_set'));
		$user->expects($this->once())->method('_set')->
			with('password', $user->passwordHash('test'));
		$user->passwordMutator('test');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusUser::isPasswordValid
	 */
	public function test_isPasswordValid()
	{
		$user = new EresusUser;
		$user->password = $user->passwordHash('test');
		$this->assertTrue($user->isPasswordValid('test'));
		$this->assertFalse($user->isPasswordValid(''));
	}
	//-----------------------------------------------------------------------------

	/* */
}
