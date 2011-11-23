<?php
/**
 * ${product.title}
 *
 * Модульные тесты
 *
 * @version ${product.version}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
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

PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

require_once __DIR__ . '/Admin/AllTests.php';
require_once __DIR__ . '/DB/AllTests.php';
require_once __DIR__ . '/XML/AllTests.php';
require_once __DIR__ . '/CMS_Test.php';
require_once __DIR__ . '/Config_Test.php';
require_once __DIR__ . '/Functions_Test.php';
require_once __DIR__ . '/i18n_Test.php';
require_once __DIR__ . '/Kernel_Test.php';
require_once __DIR__ . '/Plugin_Test.php';
require_once __DIR__ . '/classes/AllTests.php';

class Eresus_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('All/Eresus');

		$suite->addTest(Eresus_Admin_AllTests::suite());
		$suite->addTest(Eresus_DB_AllTests::suite());
		$suite->addTest(Eresus_XML_AllTests::suite());
		$suite->addTestSuite('Eresus_CMS_Test');
		$suite->addTestSuite('Eresus_Config_Test');
		$suite->addTestSuite('Functions_Test');
		$suite->addTestSuite('Eresus_i18n_Test');
		$suite->addTestSuite('Eresus_Kernel_Test');
		$suite->addTestSuite('Eresus_Plugin_Test');

		$suite->addTest(Core_Classes_AllTests::suite());

		return $suite;
	}
}
