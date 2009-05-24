<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модульные тесты основой части системы
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-${build.year}, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 * @package EresusCMS
 * @subpackage Tests
 *
 * $Id$
 */

require_once 'PHPUnit/Framework.php';

require_once 'kernel/AllTests.php';
/*require_once 'lib/AllTests.php';
require_once 'classes_php/AllTests.php';
require_once 'client_php/AllTests.php';
*/

require_once 'EresusCmsTest.php';

class Core_AllTests
{
	/**
	 *
	 */
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Core Tests');

		$suite->addTest(Core_Kernel_AllTests::suite());
		/*$suite->addTest(Core_Lib_AllTests::suite());
		$suite->addTest(Core_Classes_php_AllTests::suite());
		$suite->addTest(Core_Client_php_AllTests::suite());*/

		$suite->addTestSuite('EresusCmsTest');

		return $suite;
	}
}
