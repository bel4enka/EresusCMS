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
require_once dirname(__FILE__) . '/../../../main/core/classes/ORM.php';
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
		$mock = $this->getMock('stdClass', array('findByLogin'));
		Doctrine_Core::setMock($mock);

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
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}
		$Doctrine_Table = $this->getMock('Doctrine_Table', array('findByLogin'));
		$Doctrine_Table->expects($this->once())->method('findByLogin')->
			will($this->returnValue(array()));

		$Doctrine_Core = $this->getMock('Doctrine_Core', array('getTable'));
		$Doctrine_Core->expects($this->once())->method('getTable')->with('User')->
			will($this->returnValue($Doctrine_Table));

		Doctrine_Core::setMock($Doctrine_Core);

		$test = new Eresus();

		$this->assertFalse($test->login('unexistent_user', ''));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_inactive_user()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}
		$User = $this->getMock('User', array('save'));
		$User->active = false;
		$User->expects($this->never())->method('save');

		$Doctrine_Table = $this->getMock('Doctrine_Table', array('findByLogin'));
		$Doctrine_Table->expects($this->once())->method('findByLogin')->
			will($this->returnValue(array($User)));

		$Doctrine_Core = $this->getMock('Doctrine_Core', array('getTable'));
		$Doctrine_Core->expects($this->once())->method('getTable')->with('User')->
			will($this->returnValue($Doctrine_Table));

		Doctrine_Core::setMock($Doctrine_Core);

		$test = new Eresus();

		$this->assertFalse($test->login('inactive_user', ''));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_too_early_login()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}
		$User = $this->getMock('User', array('save'));
		$User->id = 1;
		$User->active = true;
		$User->lastLoginTime = time();
		$User->loginErrors = 100;
		$User->expects($this->once())->method('save');

		$Doctrine_Table = $this->getMock('Doctrine_Table', array('findByLogin'));
		$Doctrine_Table->expects($this->once())->method('findByLogin')->
			will($this->returnValue(array($User)));

		$Doctrine_Core = $this->getMock('Doctrine_Core', array('getTable'));
		$Doctrine_Core->expects($this->once())->method('getTable')->with('User')->
			will($this->returnValue($Doctrine_Table));

		Doctrine_Core::setMock($Doctrine_Core);

		$test = new Eresus();

		$this->assertFalse($test->login('some_user', ''));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_bad_password()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}
		$User = $this->getMock('User', array('save'));
		$User->id = 1;
		$User->active = true;
		$User->hash = 'some_hash';
		$User->lastLoginTime = time() - 100;
		$User->loginErrors = 0;
		$User->expects($this->once())->method('save');

		$Doctrine_Table = $this->getMock('Doctrine_Table', array('findByLogin'));
		$Doctrine_Table->expects($this->once())->method('findByLogin')->
			will($this->returnValue(array($User)));

		$Doctrine_Core = $this->getMock('Doctrine_Core', array('getTable'));
		$Doctrine_Core->expects($this->once())->method('getTable')->with('User')->
			will($this->returnValue($Doctrine_Table));

		Doctrine_Core::setMock($Doctrine_Core);

		$test = new Eresus();

		$this->assertFalse($test->login('some_user', 'invalid_hash'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_bad_password_w_cookies()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}
		$User = $this->getMock('User', array('save'));
		$User->id = 1;
		$User->active = true;
		$User->hash = 'some_hash';
		$User->lastLoginTime = time() - 100;
		$User->loginErrors = 0;
		$User->expects($this->never())->method('save');

		$Doctrine_Table = $this->getMock('Doctrine_Table', array('findByLogin'));
		$Doctrine_Table->expects($this->once())->method('findByLogin')->
			will($this->returnValue(array($User)));

		$Doctrine_Core = $this->getMock('Doctrine_Core', array('getTable'));
		$Doctrine_Core->expects($this->once())->method('getTable')->with('User')->
			will($this->returnValue($Doctrine_Table));

		Doctrine_Core::setMock($Doctrine_Core);

		$test = new Eresus();

		$this->assertFalse($test->login('some_user', 'invalid_hash', false, true));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_auto()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}
		$User = $this->getMock('User', array('save'));
		$User->id = 1;
		$User->active = true;
		$User->hash = 'some_hash';
		$User->lastLoginTime = time() - 100;
		$User->loginErrors = 0;
		$User->profile = array();
		$User->expects($this->once())->method('save');

		$Doctrine_Table = $this->getMock('Doctrine_Table', array('findByLogin'));
		$Doctrine_Table->expects($this->once())->method('findByLogin')->
			will($this->returnValue(array($User)));

		$Doctrine_Core = $this->getMock('Doctrine_Core', array('getTable'));
		$Doctrine_Core->expects($this->once())->method('getTable')->with('User')->
			will($this->returnValue($Doctrine_Table));

		Doctrine_Core::setMock($Doctrine_Core);

		$test = $this->getMock('Eresus', array('set_login_cookies', 'clear_login_cookies'));
		$test->expects($this->once())->method('set_login_cookies');
		$test->expects($this->never())->method('clear_login_cookies');

		$this->assertTrue($test->login('some_user', 'some_hash', true));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus::login
	 */
	public function test_login_no_auto()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}
		$User = $this->getMock('User', array('save'));
		$User->id = 1;
		$User->active = true;
		$User->hash = 'some_hash';
		$User->lastLoginTime = time() - 100;
		$User->loginErrors = 0;
		$User->profile = array();
		$User->expects($this->once())->method('save');

		$Doctrine_Table = $this->getMock('Doctrine_Table', array('findByLogin'));
		$Doctrine_Table->expects($this->once())->method('findByLogin')->
			will($this->returnValue(array($User)));

		$Doctrine_Core = $this->getMock('Doctrine_Core', array('getTable'));
		$Doctrine_Core->expects($this->once())->method('getTable')->with('User')->
			will($this->returnValue($Doctrine_Table));

		Doctrine_Core::setMock($Doctrine_Core);

		$test = $this->getMock('Eresus', array('set_login_cookies', 'clear_login_cookies'));
		$test->expects($this->never())->method('set_login_cookies');
		$test->expects($this->once())->method('clear_login_cookies');

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
			'access' => GUEST
		);

		$test->logout(false);

		$this->assertEquals($user, $test->user);
	}
	//-----------------------------------------------------------------------------

	/* */
}
