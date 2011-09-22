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

require_once dirname(__FILE__) . '/../../../../stubs.php';
require_once TESTS_SRC_ROOT . '/core/UI/Menu/Item.php';
require_once TESTS_SRC_ROOT . '/core/UI/Menu/Admin/Item.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_UI_Menu_Admin_Item_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_UI_Menu_Admin_Item::setPath
	 */
	public function test_setPath()
	{
		$item = new Eresus_UI_Menu_Admin_Item();
		$item->setPath('/foo');
		$this->assertEquals('/admin/foo', $item->getPath());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_UI_Menu_Admin_Item::setAccess
	 * @covers Eresus_UI_Menu_Admin_Item::getAccess
	 */
	public function test_xetAccess()
	{
		$item = new Eresus_UI_Menu_Admin_Item();
		$item->setAccess('ROLE_ADMIN');
		$this->assertEquals('ROLE_ADMIN', $item->getAccess());
	}
	//-----------------------------------------------------------------------------

	/* */
}
