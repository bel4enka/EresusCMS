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

PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

require_once __DIR__ . '/Delete_Test.php';
require_once __DIR__ . '/Down_Test.php';
require_once __DIR__ . '/Edit_Test.php';
require_once __DIR__ . '/Toggle_Test.php';
require_once __DIR__ . '/Up_Test.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_UI_List_Control_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('All/Eresus/UI/List/Control');

		$suite->addTestSuite('Eresus_UI_List_Control_Delete_Test');
		$suite->addTestSuite('Eresus_UI_List_Control_Down_Test');
		$suite->addTestSuite('Eresus_UI_List_Control_Edit_Test');
		$suite->addTestSuite('Eresus_UI_List_Control_Toggle_Test');
		$suite->addTestSuite('Eresus_UI_List_Control_Up_Test');

		return $suite;
	}
}
