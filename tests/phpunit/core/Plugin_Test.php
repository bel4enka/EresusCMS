<?php
/**
 * ${product.title} ${product.version}
 *
 * Модульные тесты
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
 * @package Eresus
 * @subpackage Tests
 *
 * $Id$
 */

require_once __DIR__ . '/../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/XML/Element.php';
require_once TESTS_SRC_DIR . '/core/i18n.php';
require_once TESTS_SRC_DIR . '/core/Kernel.php';
require_once TESTS_SRC_DIR . '/core/Plugin.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Plugin_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Plugin::loadFromFile
	 * @covers Eresus_Plugin::__get
	 * @covers Eresus_Plugin::getUID
	 * @covers Eresus_Plugin::getName
	 * @covers Eresus_Plugin::getTitle
	 * @covers Eresus_Plugin::getVersion
	 * @covers Eresus_Plugin::getDescription
	 * @covers Eresus_Plugin::getRequiredKernel
	 * @covers Eresus_Plugin::getRequiredPlugins
	 * @covers Eresus_Plugin::getDevelopers
	 * @covers Eresus_Plugin::getAuthors
	 * @covers Eresus_Plugin::getDocs
	 */
	public function test_overall()
	{
		$container = new sfServiceContainerBuilder();
		Eresus_Tests::setStatic('Eresus_Kernel', $container, 'sc');
		$i18n = new Eresus_i18n(TESTS_SRC_DIR . '/lang');
		$i18n->setLocale('ru_RU');
		$container->setService('i18n', $i18n);

		$info = Eresus_Plugin::loadFromFile(TESTS_SRC_DIR . '/plugins/Test/plugin.xml');
		$this->assertInstanceOf('Eresus_Plugin', $info);
		$this->assertEquals('ru.eresus.plugins.Test', $info->uid);
		$this->assertEquals('Test', $info->name);
		$this->assertEquals('Тестовый плагин', $info->title);
		$this->assertEquals('1.00', $info->version);
		$this->assertEquals('Пример плагина', $info->description);
		$this->assertEquals(array('min' => '2.17', 'max' => '2.17'), $info->requiredKernel);
		$this->assertEquals(array('ru.eresus.plugins.Test2' => array(
			'uid' => 'ru.eresus.plugins.Test2',
			'min' => '1.00',
			'max' => '1.00',
			'name' => 'Test2',
			'url' => 'http://example.org/',
		)), $info->requiredPlugins);
		$this->assertEquals(array(array(
			'title' => 'Eresus',
			'url' => 'http://eresus.ru/',
		)), $info->developers);
		$this->assertEquals(array(array(
			'name' => 'Михаил Красильников',
			'email' => 'mihalych@vsepofigu.ru',
			'url' => null,
		)), $info->authors);
		$this->assertEquals(array('ru_RU' => 'http://docs.eresus.ru/cms/dev/index'), $info->docs);
	}
	//-----------------------------------------------------------------------------

	/* */
}
