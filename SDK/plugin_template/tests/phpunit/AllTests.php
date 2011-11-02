<?php
/**
 * [Краткое название плагина]
 *
 * Модульные тесты
 *
 * @version ${product.version}
 *
 * @copyright [год], [владелец], [адрес, если нужен]
 * @license http://www.gnu.org/licenses/gpl.txt	GPL License 3
 * @author [Автор1 <E-mail автора1>]
 * @author [АвторN <E-mail автораN>]
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
 * @package [Имя пакета]
 * @subpackage Tests
 *
 * $Id$
 */

if (class_exists('PHP_CodeCoverage_Filter', false))
{
	PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);
}
else
{
	PHPUnit_Util_Filter::addFileToFilter(__FILE__);
}

require_once dirname(__FILE__) . '/classes/AllTests.php';
require_once dirname(__FILE__) . '/MyPlugin_Test.php';

/**
 * @package [Имя пакета]
 * @subpackage Tests
 */
class AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('All Tests');

		$suite->addTest(      Classes_AllTests::suite());
		$suite->addTestSuite('MyPlugin_Test');

		return $suite;
	}
}
