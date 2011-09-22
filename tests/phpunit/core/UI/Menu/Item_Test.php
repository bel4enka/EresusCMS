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
 * $Id: ACL_Test.php 1748 2011-07-27 08:03:10Z mk $
 */

require_once dirname(__FILE__) . '/../../../stubs.php';
require_once TESTS_SRC_ROOT . '/core/UI/Menu/Item.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_UI_Menu_Item_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		Eresus_Tests::setStatic('Eresus_CMS_Request', null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_Menu_Item::setPath
	 * @covers Eresus_UI_Menu_Item::getPath
	 */
	public function test_xetPath()
	{
		$item = new Eresus_UI_Menu_Item();
		$item->setPath('a/b/c');
		$this->assertEquals('a/b/c', $item->getPath());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_Menu_Item::setCaption
	 * @covers Eresus_UI_Menu_Item::getCaption
	 */
	public function test_xetCaption()
	{
		$item = new Eresus_UI_Menu_Item();
		$item->setCaption('test');
		$this->assertEquals('test', $item->getCaption());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_Menu_Item::setHint
	 * @covers Eresus_UI_Menu_Item::getHint
	 */
	public function test_xetHint()
	{
		$item = new Eresus_UI_Menu_Item();
		$item->setHint('test');
		$this->assertEquals('test', $item->getHint());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_Menu_Item::__get
	 */
	public function test_get()
	{
		$item = new Eresus_UI_Menu_Item();
		$item->setCaption('test');
		$this->assertEquals('test', $item->caption);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_Menu_Item::__get
	 * @expectedException LogicException
	 */
	public function test_get_invalid()
	{
		$item = new Eresus_UI_Menu_Item();
		$item->invalid_property;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_Menu_Item::getCurrent
	 */
	public function test_getCurrent()
	{
		$item = new Eresus_UI_Menu_Item();

		$req = $this->getMock('stdClass', array('getPathInfo'));
		$req->expects($this->any())->method('getPathInfo')->will($this->returnValue('/foo/bar'));
		Eresus_Tests::setStatic('Eresus_CMS_Request', $req);

		$item->setPath('/foo');
		$this->assertFalse($item->getCurrent());
		$item->setPath('/bar');
		$this->assertFalse($item->getCurrent());
		$item->setPath('/foo/bar');
		$this->assertTrue($item->getCurrent());
		$item->setPath('/foo/bar/baz');
		$this->assertFalse($item->getCurrent());
		$item->setPath('/foo/barbaz');
		$this->assertFalse($item->getCurrent());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_Menu_Item::getOpened
	 */
	public function test_getOpened()
	{
		$item = new Eresus_UI_Menu_Item();

		$req = $this->getMock('stdClass', array('getPathInfo'));
		$req->expects($this->any())->method('getPathInfo')->will($this->returnValue('/foo/bar'));
		Eresus_Tests::setStatic('Eresus_CMS_Request', $req);

		$item->setPath('/foo');
		$this->assertTrue($item->getOpened());
		$item->setPath('/bar');
		$this->assertFalse($item->getOpened());
		$item->setPath('/foo/bar');
		$this->assertTrue($item->getOpened());
		$item->setPath('/foo/barbaz');
		$this->assertFalse($item->getOpened());
	}
	//-----------------------------------------------------------------------------

	/* */
}
