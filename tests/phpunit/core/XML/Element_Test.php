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
	 * @covers Eresus_XML_Element::__construct
	 */
	public function test_construct()
	{
		$p_xml = new ReflectionProperty('Eresus_XML_Element', 'xml');
		$p_xml->setAccessible(true);

		$xml = new SimpleXMLElement('<test />');
		$node = new Eresus_XML_Element($xml);

		$this->assertSame($xml, $p_xml->getValue($node));

		$xml = '<test />';
		$node = new Eresus_XML_Element($xml);

		$this->assertInstanceOf('SimpleXMLElement', $p_xml->getValue($node));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_XML_Element::convertValue
	 */
	public function test_convertValue()
	{
		$m_convertValue = new ReflectionMethod('Eresus_XML_Element', 'convertValue');
		$m_convertValue->setAccessible(true);

		$node = new Eresus_XML_Element('<foo />');

		$this->assertEquals('foo', $m_convertValue->invoke($node, 'foo'));
		$this->assertInstanceOf('Eresus_XML_Element',
			$m_convertValue->invoke($node, new SimpleXMLElement('<foo />')));

		$a = $m_convertValue->invoke($node, array('foo', new SimpleXMLElement('<foo />')));
		$this->assertEquals('foo', $a[0]);
		$this->assertInstanceOf('Eresus_XML_Element', $a[1]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_XML_Element::__get
	 */
	public function test_get()
	{
		$node = new Eresus_XML_Element('<foo><bar /></foo>');
		$this->assertInstanceOf('Eresus_XML_Element', $node->bar);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_XML_Element::__set
	 */
	public function test_set()
	{
		$node = new Eresus_XML_Element('<foo><bar /></foo>');
		$node->bar = 'foo';
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_XML_Element::__call
	 */
	public function test_call()
	{
		$node = new Eresus_XML_Element('<foo><bar /><bar /></foo>');
		$this->assertEquals(2, $node->bar->count());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_XML_Element::offsetExists
	 */
	public function test_offsetExists()
	{
		$node = new Eresus_XML_Element('<foo bar="baz" />');
		$this->assertTrue(isset($node['bar']));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_XML_Element::offsetGet
	 * @covers Eresus_XML_Element::__toString
	 */
	public function test_offsetGet_toString()
	{
		$node = new Eresus_XML_Element('<foo bar="baz" />');
		$this->assertEquals('baz', strval($node['bar']));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_XML_Element::offsetSet
	 */
	public function test_offsetSet()
	{
		$node = new Eresus_XML_Element('<foo bar="baz" />');
		$node['bar'] = 'foo';
		$this->assertEquals('foo', strval($node['bar']));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_XML_Element::offsetUnset
	 */
	public function test_offsetUnset()
	{
		$node = new Eresus_XML_Element('<foo bar="baz" />');
		unset($node['bar']);
		$this->assertEquals(0, count($node['bar']));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_XML_Element::serialize
	 * @covers Eresus_XML_Element::unserialize
	 */
	public function test_serialize()
	{
		$node1 = new Eresus_XML_Element('<foo bar="baz" />');
		$node2 = unserialize(serialize($node1));
		$this->assertEquals('baz', strval($node2['bar']));
	}
	//-----------------------------------------------------------------------------

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
