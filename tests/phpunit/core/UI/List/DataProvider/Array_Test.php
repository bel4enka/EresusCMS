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
require_once TESTS_SRC_DIR . '/core/UI/List/Item/Interface.php';
require_once TESTS_SRC_DIR . '/core/UI/List/Item/Object.php';
require_once TESTS_SRC_DIR . '/core/UI/List/DataProvider/Array.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_UI_List_DataProvider_Array_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_UI_List_DataProvider_Array::__construct
	 * @covers Eresus_UI_List_DataProvider_Array::getItems
	 * @covers Eresus_UI_List_DataProvider_Array::getCount
	 */
	public function test_overall()
	{
		$provider = new Eresus_UI_List_DataProvider_Array(array(new stdClass(), new stdClass()));
		$this->assertEquals(2, $provider->getCount());
		$items = $provider->getItems();
		$this->assertInstanceOf('Eresus_UI_List_Item_Interface', $items[0]);
	}
	//-----------------------------------------------------------------------------
}
