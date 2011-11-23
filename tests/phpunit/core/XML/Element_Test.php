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

require_once __DIR__ . '/../../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/Config.php';
require_once TESTS_SRC_DIR . '/core/i18n.php';
require_once TESTS_SRC_DIR . '/core/Kernel.php';
require_once TESTS_SRC_DIR . '/core/XML/Element.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_XML_Element_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_XML_Element::getLocalized
	 */
	public function test_getLocalized()
	{
		$node = new Eresus_XML_Element('
			<root>
				<ru_RU>Русский текст</ru_RU>
				<en_US>English text</en_US>
			</root>
		');

		$container = new sfServiceContainerBuilder();
		Eresus_Tests::setStatic('Eresus_Kernel', $container, 'sc');

		$i18n = new Eresus_i18n(TESTS_SRC_DIR . '/lang');
		$container->setService('i18n', $i18n);

		$i18n->setLocale('en_US');
		$this->assertEquals('English text', $node->getLocalized());
		$i18n->setLocale('en_GB');
		$this->assertEquals('English text', $node->getLocalized());
		$i18n->setLocale('ru_RU');
		$this->assertEquals('Русский текст', $node->getLocalized());
		$i18n->setLocale('xx_XX');
		$this->assertEquals('Русский текст', $node->getLocalized());

		$node = new Eresus_XML_Element('
			<root>
				<en_US>English text</en_US>
			</root>
		');
		$this->assertEquals('', $node->getLocalized());
	}
	//-----------------------------------------------------------------------------

	/* */
}
