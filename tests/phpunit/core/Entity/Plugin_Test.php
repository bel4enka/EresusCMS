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
 * @package Eresus
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * $Id: Plugin_Test.php 1609 2011-05-18 09:46:37Z mk $
 */

require_once dirname(__FILE__) . '/../../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/DB/Record.php';
require_once TESTS_SRC_DIR . '/core/i18n.php';
require_once TESTS_SRC_DIR . '/core/Kernel.php';
require_once TESTS_SRC_DIR . '/core/XML/Element.php';
require_once TESTS_SRC_DIR . '/core/Entity/Plugin.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Entity_Plugin_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Entity_Plugin::setTableDefinition
	 */
	public function test_setTableDefinition()
	{
		$test = $this->getMockBuilder('Eresus_Entity_Plugin')->setMethods(array('setTableName', 'hasColumns'))
			->disableOriginalConstructor()->getMock();
		$test->expects($this->once())->method('setTableName')->with('plugins');
		$test->expects($this->once())->method('hasColumns');
		$test->setTableDefinition();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Entity_Plugin::loadFromFile
	 * @covers Eresus_Entity_Plugin::getTitle
	 * @covers Eresus_Entity_Plugin::getVersion
	 * @covers Eresus_Entity_Plugin::getDescription
	 * @covers Eresus_Entity_Plugin::getRequiredKernel
	 * @covers Eresus_Entity_Plugin::getRequiredPlugins
	 * @covers Eresus_Entity_Plugin::getDevelopers
	 * @covers Eresus_Entity_Plugin::getAuthors
	 * @covers Eresus_Entity_Plugin::getDocs
	 */
	public function test_overall()
	{
		$container = new sfServiceContainerBuilder();
		Eresus_Tests::setStatic('Eresus_Kernel', $container, 'sc');
		$i18n = new Eresus_i18n(TESTS_SRC_DIR . '/lang');
		$i18n->setLocale('ru_RU');
		$container->setService('i18n', $i18n);

		$plugin = Eresus_Entity_Plugin::loadFromFile(TESTS_SRC_DIR . '/plugins/Test2/plugin.xml');
		$this->assertInstanceOf('Eresus_Entity_Plugin', $plugin);
		$this->assertEquals('ru.eresus.plugins.Test2', $plugin->uid);
		$this->assertEquals('Test2', $plugin->name);
		$this->assertEquals('Тестовый плагин 2', $plugin->title);
		$this->assertEquals('1.00', $plugin->version);
		$this->assertEquals('Пример плагина', $plugin->description);
		$this->assertEquals(array('min' => '2.17', 'max' => '2.17'), $plugin->requiredKernel);
		$this->assertEquals(array('ru.eresus.plugins.Test1' => array(
			'uid' => 'ru.eresus.plugins.Test1',
			'min' => '1.00',
			'max' => '1.00',
			'name' => 'Test1',
			'url' => 'http://example.org/',
		)), $plugin->requiredPlugins);
		$this->assertEquals(array(array(
			'title' => 'Eresus',
			'url' => 'http://eresus.ru/',
		)), $plugin->developers);
		$this->assertEquals(array(array(
			'name' => 'Михаил Красильников',
			'email' => 'mihalych@vsepofigu.ru',
			'url' => null,
		)), $plugin->authors);
		$this->assertEquals(array('ru_RU' => 'http://docs.eresus.ru/cms/dev/index'), $plugin->docs);
	}
	//-----------------------------------------------------------------------------

	/* */
}
