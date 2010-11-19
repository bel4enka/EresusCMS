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

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/kernel-legacy.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class EresusTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus::password_hash
	 */
	public function test_password_hash()
	{
		$test = new Eresus();

		$password = 'mypass';
		$weak = md5($password);
		$strong = md5($weak);

		$test->conf['backward']['weak_password'] = false;
		$this->assertEquals($strong, $test->password_hash($password), 'Strong hash does not match');
		$test->conf['backward']['weak_password'] = true;
		$this->assertEquals($weak, $test->password_hash($password), 'Weak hash does not match');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_bad_symbols()
	{
		$test = new Eresus();

		$this->assertFalse($test->login('"root"', ''));
		$this->assertFalse($test->login('\root\\', ''));
		$this->assertFalse($test->login('/\'root/\"', ''));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_no_such_user()
	{
		$test = new Eresus();
		$test->db = $this->getMock('stdClass', array('selectItem'));
		$test->db->expects($this->once())->
			method('selectItem')->
			will($this->returnValue(null));

		$this->assertFalse($test->login('unexistent_user', ''));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_inactive_user()
	{
		$test = new Eresus();
		$test->db = $this->getMock('stdClass', array('selectItem'));
		$test->db->expects($this->once())->
			method('selectItem')->
			will($this->returnValue(array('active' => false)));

		$this->assertFalse($test->login('inactive_user', ''));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_too_early_login()
	{
		$test = new Eresus();
		$test->db = $this->getMock('stdClass', array('selectItem', 'updateItem'));
		$test->db->expects($this->once())->
			method('selectItem')->
			will($this->returnValue(array(
				'id' => 1,
				'active' => true,
				'lastLoginTime' => time(),
				'loginErrors' => 100
			)));
		$test->db->expects($this->once())->method('updateItem');

		$this->assertFalse($test->login('some_user', ''));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_bad_password()
	{
		$test = new Eresus();
		$test->db = $this->getMock('stdClass', array('selectItem', 'updateItem'));
		$test->db->expects($this->once())->
			method('selectItem')->
			will($this->returnValue(array(
				'id' => 1,
				'active' => true,
				'hash' => 'some_hash',
				'lastLoginTime' => time() - 100,
				'loginErrors' => 0
			)));
		$test->db->expects($this->once())->method('updateItem');

		$this->assertFalse($test->login('some_user', 'invalid_hash'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_bad_password_w_cookies()
	{
		$test = new Eresus();
		$test->db = $this->getMock('stdClass', array('selectItem', 'updateItem'));
		$test->db->expects($this->once())->
			method('selectItem')->
			will($this->returnValue(array(
				'id' => 1,
				'active' => true,
				'hash' => 'some_hash',
				'lastLoginTime' => time() - 100,
				'loginErrors' => 0
			)));
		$test->db->expects($this->never())->method('updateItem');

		$this->assertFalse($test->login('some_user', 'invalid_hash', false, true));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_auto()
	{
		$test = $this->getMock('Eresus', array('set_login_cookies', 'clear_login_cookies'));
		$test->expects($this->once())->method('set_login_cookies');
		$test->expects($this->never())->method('clear_login_cookies');

		$test->db = $this->getMock('stdClass', array('selectItem', 'updateItem'));
		$test->db->expects($this->once())->
			method('selectItem')->
			will($this->returnValue(array(
				'id' => 1,
				'active' => true,
				'hash' => 'some_hash',
				'lastLoginTime' => time() - 100,
				'loginErrors' => 0,
				'profile' => ''
			)));
		$test->db->expects($this->once())->method('updateItem');

		$this->assertTrue($test->login('some_user', 'some_hash', true));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_no_auto()
	{
		$test = $this->getMock('Eresus', array('set_login_cookies', 'clear_login_cookies'));
		$test->expects($this->never())->method('set_login_cookies');
		$test->expects($this->once())->method('clear_login_cookies');

		$test->db = $this->getMock('stdClass', array('selectItem', 'updateItem'));
		$test->db->expects($this->once())->
			method('selectItem')->
			will($this->returnValue(array(
				'id' => 1,
				'active' => true,
				'hash' => 'some_hash',
				'lastLoginTime' => time() - 100,
				'loginErrors' => 0,
				'profile' => ''
			)));
		$test->db->expects($this->once())->method('updateItem');

		$this->assertTrue($test->login('some_user', 'some_hash'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::logout
	 */
	public function test_logout()
	{
		$test = $this->getMock('Eresus', array('clear_login_cookies'));
		$test->expects($this->once())->method('clear_login_cookies');

		$test->db = array();

		$user = array(
			'id' => null,
			'auth' => false,
			'access' => GUEST
		);

		$test->logout();

		$this->assertEquals($user, $test->user);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::logout
	 */
	public function test_logout_save_cookies()
	{
		$test = $this->getMock('Eresus', array('clear_login_cookies'));
		$test->expects($this->never())->method('clear_login_cookies');

		$test->db = array();

		$user = array(
			'id' => null,
			'auth' => false,
			'access' => GUEST
		);

		$test->logout(false);

		$this->assertEquals($user, $test->user);
	}
	//-----------------------------------------------------------------------------

	/* */
}
