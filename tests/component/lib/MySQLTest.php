<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 * @package Tests
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../helpers.php';

require_once TEST_DIR_ROOT . '/core/lib/mysql.php';

/**
 * @package Tests
 */
class MySQLTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Возвращает экземпляр MySQL
	 * @return MySQL
	 */
	private function getInstance()
	{
		preg_match('/mysql:\/\/(.*):(.*)@(.*)\/(.*)(\?charset=(.*))/', $GLOBALS['TESTCONF']['DB']['dsn'], $m);
		if (!defined('LOCALE_CHARSET'))
			define('LOCALE_CHARSET', $m[6]);

		$instance = new MySQL();
		$instance->init($m[3], $m[1], $m[2], $m[4]);

		return $instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает экземпляр MySQL с префиксом
	 * @return MySQL
	 */
	private function getInstancePrefixed()
	{
		preg_match('/mysql:\/\/(.*):(.*)@(.*)\/(.*)(\?charset=(.*))/', $GLOBALS['TESTCONF']['DB']['dsn'], $m);
		if (!defined('LOCALE_CHARSET'))
			define('LOCALE_CHARSET', $m[6]);

		$instance = new MySQL();
		$instance->init($m[3], $m[1], $m[2], $m[4], 'test_');

		return $instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::init
	 */
	public function testInit()
	{
		$fixture = new MySQL();

		$this->assertFalse($fixture->init('-unexistent-', 'user', 'password', 'test'));
		preg_match('/mysql:\/\/(.*):(.*)@(.*)\/(.*)(\?charset=(.*))/', $GLOBALS['TESTCONF']['DB']['dsn'], $m);
		if (!defined('LOCALE_CHARSET'))
			define('LOCALE_CHARSET', $m[6]);

		$this->assertTrue($fixture->init($m[3], $m[1], $m[2], $m[4]));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::escape
	 */
	public function testEscape()
	{
		$fixture = $this->getInstance();
		$this->assertEquals('test', $fixture->escape('test'));
		$this->assertEquals(array('a' => 'test'), $fixture->escape(array('a' => 'test')));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::fields
	 */
	public function testFields()
	{
		$fixture = $this->getInstance();
		$fields = array('access', 'active', 'hash', 'id', 'lastLoginTime', 'lastVisit',
			'login', 'loginErrors', 'mail', 'name', 'profile');

		$this->assertEquals($fields, $fixture->fields('users'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::select
	 */
	public function testSelect()
	{
		$fixture = $this->getInstance();
		$items = $fixture->select('users', "active = 1", '-access', 'id,access', 2, 1, '', true);
		$this->assertEquals(2, count($items));
		$this->assertEquals(1, $items[1]['access']);
		$this->assertFalse(isset($items[0]['login']));

		$items = $fixture->select('users', "active = 1", '+access,lastVisit', 'id,access', 2);
		$this->assertEquals(2, count($items));
		$this->assertEquals(2, $items[1]['access']);

	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::selectItem
	 */
	public function testSelectItem()
	{
		$fixture = $this->getInstance();
		$item = $fixture->selectItem('users', "login = 'root'", 'id,access');
		$this->assertEquals(2, count($item));
		$this->assertEquals(1, $item['access']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка выборки методом MySQL::selectItem несуществующего элемента
	 */
	public function testSelectItemFail()
	{
		$fixture = $this->getInstance();
		$item = $fixture->selectItem('users', "login = '-nobody-'");
		$this->assertFalse($item);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::insert
	 */
	public function testInsert()
	{
		$fixture = $this->getInstance();
		$item = array(
			'access' => 1,
			'active' => 1,
			'hash' => '12345678901234567890123456789012',
			'lastLoginTime' => time(),
			'lastVisit' => time(),
			'login' => 'test',
			'loginErrors' => 0,
			'mail' => 'test@example.org',
			'name' => 'test',
			'profile' => '',
		);
		$fixture->insert('users', $item);
		$id = $fixture->getInsertedID();
		$this->assertEquals(4, $id);
		$item = $fixture->selectItem('users', "login = 'test'");
		$this->assertEquals('test@example.org', $item['mail']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::update
	 */
	public function testUpdate()
	{
		$fixture = $this->getInstance();
		$fixture->update('users', "login = 'editor2', mail = 'my@mail.org'", "id = 3");
		$item = $fixture->selectItem('users', "id = 3");
		$this->assertEquals('editor2', $item['login']);
		$this->assertEquals('my@mail.org', $item['mail']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::updateItem
	 */
	public function testUpdateItem()
	{
		$fixture = $this->getInstance();
		$item = array(
			'id' => 3,
			'login' => 'editor3',
			'hash' => '1234567890123456789012',
			'active' => 1,
			'lastVisit' => 0,
			'lastLoginTime' => 0,
			'loginErrors' => 0,
			'access' => 3,
			'name' => 'Editor',
			'mail' => 'editor3@example.org',
			'profile' => ''
		);
		$fixture->updateItem('users', $item, "id = 3");
		$item = $fixture->selectItem('users', "id = 3");
		$this->assertEquals('editor3', $item['login']);
		$this->assertEquals('editor3@example.org', $item['mail']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::delete
	 */
	public function testDelete()
	{
		$fixture = $this->getInstance();
		$fixture->delete('users', "id = 3");
		$item = $fixture->selectItem('users', "id = 3");
		$this->assertFalse($item);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::count
	 */
	public function testCount()
	{
		$fixture = $this->getInstance();
		$this->assertEquals(2, $fixture->count('pages'));
		$this->assertEquals(2, $fixture->count('users', 'access = 1'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::init с использованием префиксов
	 */
	public function testInitWithPrefix()
	{
		$fixture = new MySQL();

		preg_match('/mysql:\/\/(.*):(.*)@(.*)\/(.*)(\?charset=(.*))/', $GLOBALS['TESTCONF']['DB']['dsn'], $m);
		if (!defined('LOCALE_CHARSET'))
			define('LOCALE_CHARSET', $m[6]);

		$this->assertTrue($fixture->init($m[3], $m[1], $m[2], $m[4], 'test_'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::fields с использованием префиксов
	 */
	public function testFieldsWithPrefixes()
	{
		$fixture = $this->getInstancePrefixed();
		$fields = array('id', 'name');

		$this->assertEquals($fields, $fixture->fields('prefixed'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::select с использованием префиксов
	 */
	public function testSelectWithPrefixes()
	{
		$fixture = $this->getInstancePrefixed();
		$items = $fixture->select('prefixed', "id = 1");
		$this->assertEquals(1, count($items));
		$this->assertEquals('main', $items[0]['name']);
	}
	//-----------------------------------------------------------------------------

	/**/
}
