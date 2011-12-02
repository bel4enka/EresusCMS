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
 * $Id: Element_Test.php 1984 2011-11-23 10:07:10Z mk $
 */


require_once __DIR__ . '/../../../../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/UI/List/URL/Interface.php';
require_once TESTS_SRC_DIR . '/core/UI/List/URL/Query.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_UI_List_URL_Query_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_UI_List_URL_Query::__construct
	 */
	public function test_construct()
	{
		$p_baseURL = new ReflectionProperty('Eresus_UI_List_URL_Query', 'baseURL');
		$p_baseURL->setAccessible(true);

		$url = new Eresus_UI_List_URL_Query('http://example.org/');
		$this->assertEquals('http://example.org/?', $p_baseURL->getValue($url));

		$GLOBALS['page'] = $this->getMock('stdClass', array('url'));
		$GLOBALS['page']->expects($this->once())->method('url')->
			will($this->returnValue('http://example.org/'));

		$url = new Eresus_UI_List_URL_Query();

		$this->assertEquals('http://example.org/?', $p_baseURL->getValue($url));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_List_URL_Query::setIdName
	 * @covers Eresus_UI_List_URL_Query::getPagination
	 * @covers Eresus_UI_List_URL_Query::getDelete
	 * @covers Eresus_UI_List_URL_Query::getEdit
	 * @covers Eresus_UI_List_URL_Query::getOrderingUp
	 * @covers Eresus_UI_List_URL_Query::getOrderingDown
	 * @covers Eresus_UI_List_URL_Query::getToggle
	 */
	public function test_overall()
	{
		$url = new Eresus_UI_List_URL_Query('http://example.org/');
		$url->setIdName('uid');

		$item = new Eresus_UI_List_URL_Query_Test_Item();

		$this->assertEquals('http://example.org/?page=%d', $url->getPagination());
		$this->assertEquals('http://example.org/?uid=1&action=delete', $url->getDelete($item));
		$this->assertEquals('http://example.org/?uid=1&action=edit', $url->getEdit($item));
		$this->assertEquals('http://example.org/?uid=1&action=up', $url->getOrderingUp($item));
		$this->assertEquals('http://example.org/?uid=1&action=down', $url->getOrderingDown($item));
		$this->assertEquals('http://example.org/?uid=1&action=toggle', $url->getToggle($item));
	}
	//-----------------------------------------------------------------------------
}


class Eresus_UI_List_URL_Query_Test_Item implements Eresus_UI_List_Item_Interface
{
	public function getId()
	{
		return 1;
	}
	//-----------------------------------------------------------------------------

	public function isEnabled()
	{
		return true;
	}
	//-----------------------------------------------------------------------------
}