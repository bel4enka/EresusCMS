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

require_once dirname(__FILE__) . '/../../../helpers.php';

require_once TEST_DIR_ROOT . '/core/lib/mysql.php';
require_once TEST_DIR_ROOT . '/core/kernel-legacy.php';
require_once TEST_DIR_ROOT . '/core/classes.php';

/**
 * @package Tests
 */
class PluginTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Проверка метода Plugin::__item()
	 */
	public function test__itemEmpty()
	{
		global $Eresus;

		$Eresus = new PropertySet();
		$Eresus->db = new MySQL();

		$fixture = new Plugin();
		$test = $fixture->__item();

		$this->assertEquals('plugin', $test['name'], 'name');
		$this->assertFalse($test['content'], 'content');
		$this->assertTrue($test['active'], 'active');
		$this->assertEquals(1, $test['position'], 'position');
		$this->assertEquals('a:0:{}', $test['settings'], 'settings');
		$this->assertEquals('no title', $test['title'], 'title');
		$this->assertEquals('0.00', $test['version'], 'version');
		$this->assertEquals('', $test['description'], 'description');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода Plugin::__item()
	 */
	public function test__itemPlugin()
	{
		global $Eresus;

		$Eresus = new PropertySet();
		$Eresus->db = new MySQL();

		$fixture = new PluginTest_Plugin();
		$test = $fixture->__item();

		$this->assertEquals('plugintest_plugin', $test['name'], 'name');
		$this->assertFalse($test['content'], 'content');
		$this->assertTrue($test['active'], 'active');
		$this->assertEquals(1, $test['position'], 'position');
		$this->assertEquals('a:1:{s:1:"a";s:1:"b";}', $test['settings'], 'settings');
		$this->assertEquals('Test plugin', $test['title'], 'title');
		$this->assertEquals('9.99', $test['version'], 'version');
		$this->assertEquals('Test plugin description', $test['description'], 'description');
	}
	//-----------------------------------------------------------------------------

	/**/
}

/**
 * @package Tests
 */
class PluginTest_Plugin extends Plugin
{
	public $version = '9.99';
	public $title = 'Test plugin';
	public $description = 'Test plugin description';
	public $settings = array(
		'a' => 'b'
	);
}
