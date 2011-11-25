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


require_once __DIR__ . '/../../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/UI/List/Control.php';
require_once TESTS_SRC_DIR . '/core/UI/List/DataProvider/Interface.php';
require_once TESTS_SRC_DIR . '/core/UI/List/Item/Interface.php';
require_once TESTS_SRC_DIR . '/core/UI/List/URL/Interface.php';
require_once TESTS_SRC_DIR . '/core/UI/List/URL/Query.php';
require_once TESTS_SRC_DIR . '/core/UI/List.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_UI_List_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_UI_List::__construct
	 */
	public function test_construct()
	{
		$list = $this->getMockBuilder('Eresus_UI_List')->disableOriginalConstructor()->
			setMethods(array('setDataProvider', 'setURL'))->getMock();
		$list->expects($this->once())->method('setDataProvider');
		$list->expects($this->once())->method('setURL');
		$list->__construct(new Eresus_UI_List_Test_DataProvider(), new Eresus_UI_List_Test_URL());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_List::setDataProvider
	 * @covers Eresus_UI_List::getDataProvider
	 */
	public function test_set_get_DataProvider()
	{
		$list = new Eresus_UI_List();
		$provider = new Eresus_UI_List_Test_DataProvider();
		$list->setDataProvider($provider);
		$this->assertSame($provider, $list->getDataProvider());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_List::getURL
	 * @covers Eresus_UI_List::setURL
	 */
	public function test_get_set_URL()
	{
		$GLOBALS['page'] = new UniversalStub();
		$list = new Eresus_UI_List();
		$this->assertInstanceOf('Eresus_UI_List_URL_Query', $list->getURL());

		$url = new Eresus_UI_List_Test_URL();
		$list = new Eresus_UI_List();
		$list->setURL($url);
		$this->assertSame($url, $list->getURL());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_List::setPage
	 */
	public function test_setPage()
	{
		$list = new Eresus_UI_List();
		$list->setPage(1);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_List::setPageSize
	 */
	public function test_setPageSize()
	{
		$list = new Eresus_UI_List();
		$list->setPageSize(1);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_List::getItems
	 */
	public function test_getItems()
	{
		$provider = $this->getMock('Eresus_UI_List_Test_DataProvider', array('getItems'));
		$provider->expects($this->once())->method('getItems')->with(10, 10);
		$list = new Eresus_UI_List($provider);
		$list->setPageSize(10);
		$list->setPage(2);
		$list->getItems();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_List::getPagination
	 */
	public function test_getPagination()
	{
		$provider = $this->getMock('Eresus_UI_List_Test_DataProvider', array('getCount'));
		$provider->expects($this->once())->method('getCount')->will($this->returnValue(100));

		$url = new Eresus_UI_List_Test_URL;

		$list = new Eresus_UI_List($provider, $url);
		$list->setPageSize(10);
		$list->setPage(5);
		$list->getPagination();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_List::getControls
	 * @expectedException LogicException
	 */
	public function test_getControls_unknownControl()
	{
		$list = new Eresus_UI_List();
		$item = new Eresus_UI_List_Test_Item();
		$list->getControls($item, 'fake');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_List::getControls
	 * @expectedException LogicException
	 */
	public function test_getControls_unknownClass()
	{
		$list = new Eresus_UI_List();
		$list->registerControl('fake', 'Unexistent');
		$item = new Eresus_UI_List_Test_Item();
		$list->getControls($item, 'fake');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_List::registerControl
	 * @covers Eresus_UI_List::getControls
	 */
	public function test_registerControl_getControls()
	{
		$GLOBALS['page'] = new UniversalStub();
		$list = new Eresus_UI_List();
		$list->registerControl('fake', 'Eresus_UI_List_Test_Control');
		$item = new Eresus_UI_List_Test_Item();
		$this->assertEquals('OK', $list->getControls($item, 'fake'));
	}
	//-----------------------------------------------------------------------------
}

class Eresus_UI_List_Test_DataProvider implements Eresus_UI_List_DataProvider_Interface
{
	/**
	 * @see Eresus_UI_List_DataProvider_Interface::getItems()
	 */
	public function getItems($limit = null, $offset = 0) {}
	//-----------------------------------------------------------------------------

	/**
	 * @see Eresus_UI_List_DataProvider_Interface::getCount()
	 */
	public function getCount() {}
	//-----------------------------------------------------------------------------
}



class Eresus_UI_List_Test_URL implements Eresus_UI_List_URL_Interface
{
	/**
	 * @see Eresus_UI_List_URL_Interface::getPagination()
	 */
	public function getPagination() {}
	//-----------------------------------------------------------------------------

	/**
	 * @see Eresus_UI_List_URL_Interface::getDelete()
	 */
	public function getDelete(Eresus_UI_List_Item_Interface $item) {}
	//-----------------------------------------------------------------------------

	/**
	 * @see Eresus_UI_List_URL_Interface::getEdit()
	 */
	public function getEdit(Eresus_UI_List_Item_Interface $item) {}
	//-----------------------------------------------------------------------------

	/**
	 * @see Eresus_UI_List_URL_Interface::getOrderingUp()
	 */
	public function getOrderingUp(Eresus_UI_List_Item_Interface $item) {}
	//-----------------------------------------------------------------------------

	/**
	 * @see Eresus_UI_List_URL_Interface::getOrderingDown()
	 */
	public function getOrderingDown(Eresus_UI_List_Item_Interface $item) {}
	//-----------------------------------------------------------------------------

	/**
	 * @see Eresus_UI_List_URL_Interface::getToggle()
	 */
	public function getToggle(Eresus_UI_List_Item_Interface $item) {}
	//-----------------------------------------------------------------------------
}



class Eresus_UI_List_Test_Item implements Eresus_UI_List_Item_Interface
{
	/**
	 */
	public function getId()
	{
		;
	}
	//-----------------------------------------------------------------------------

	/**
	 */
	public function isEnabled()
	{
		;
	}
	//-----------------------------------------------------------------------------
}



class Eresus_UI_List_Test_Control extends Eresus_UI_List_Control
{
	/**
	 */
	public function render(Eresus_UI_List_Item_Interface $item)
	{
		return 'OK';
	}
	//-----------------------------------------------------------------------------
}
