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

require_once dirname(__FILE__) . '/../../../stubs.php';
require_once TESTS_SRC_ROOT . '/core/UI/Menu.php';
require_once TESTS_SRC_ROOT . '/core/UI/Menu/Admin.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_UI_Menu_Admin_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		Eresus_Tests::setStatic('Eresus_Security', null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_Menu_Admin::addItem
	 */
	public function test_addItem()
	{
		$Eresus_Security = $this->getMock('stdClass', array('isGranted'));
		$Eresus_Security->expects($this->any())->method('isGranted')->
			will($this->returnCallback(function ($role) { return $role == 'ROLE_ADMIN';}));
		Eresus_Tests::setStatic('Eresus_Security', $Eresus_Security);

		$menu = new Eresus_UI_Menu_Admin();

		$item = new Eresus_UI_Menu_Admin_Item();
		$item->setAccess('ROLE_EDITOR');
		$menu->addItem($item);

		$item = new Eresus_UI_Menu_Admin_Item();
		$item->setAccess('ROLE_ADMIN');
		$menu->addItem($item);

		$items = $menu->getItems();
		$this->assertInternalType('array', $items);
		$this->assertEquals(1, count($items));
		$this->assertSame($item, $items[0]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_Menu_Admin::addItem
	 */
	public function test_addItem_array()
	{
		$Eresus_Security = $this->getMock('stdClass', array('isGranted'));
		$Eresus_Security->expects($this->any())->method('isGranted')->
			will($this->returnCallback(function ($role) { return $role == 'ROLE_ADMIN';}));
		Eresus_Tests::setStatic('Eresus_Security', $Eresus_Security);

		$menu = new Eresus_UI_Menu_Admin();
		$menu->addItem(array('caption' => 'Caption', 'path' => '/foo', 'access' => 'ROLE_ADMIN'));
		$items = $menu->getItems();
		$this->assertInternalType('array', $items);
		$this->assertEquals(1, count($items));
		$this->assertEquals('/admin/foo', $items[0]->getPath());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_Menu_Admin::addItem
	 * @expectedException InvalidArgumentException
	 */
	public function test_addItem_invalid()
	{
		$menu = new Eresus_UI_Menu_Admin();
		$menu->addItem('foo');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_Menu::render
	 */
	public function test_render()
	{
		$template = $this->getMock('stdClass', array('compile'));
		$template->expects($this->once())->method('compile')->
			will($this->returnCallback(function ($vars) {return $vars;}));

		$service = $this->getMock('stdClass', array('get'));
		$service->expects($this->once())->method('get')->with('foo', 'bar')->
			will($this->returnValue($template));
		Eresus_Tests::setStatic('Eresus_Template_Service', $service);

		$menu = new Eresus_UI_Menu();
		$x = $menu->render('foo', 'bar');
		$this->assertInternalType('array', $x);
		$this->assertArrayHasKey('menu', $x);
		$this->assertInstanceOf('Eresus_UI_Menu', $x['menu']);
	}
	//-----------------------------------------------------------------------------

	/* */
}
