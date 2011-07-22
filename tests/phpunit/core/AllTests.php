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

PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

require_once dirname(__FILE__) . '/Auth_Test.php';
require_once dirname(__FILE__) . '/CMS/AllTests.php';
require_once dirname(__FILE__) . '/CMS_Test.php';
require_once dirname(__FILE__) . '/Config_Test.php';
require_once dirname(__FILE__) . '/Controller/AllTests.php';
require_once dirname(__FILE__) . '/DB/AllTests.php';
require_once dirname(__FILE__) . '/EresusFormTest.php';
require_once dirname(__FILE__) . '/Helper/AllTests.php';
require_once dirname(__FILE__) . '/HTTP/AllTests.php';
require_once dirname(__FILE__) . '/i18n_Test.php';
require_once dirname(__FILE__) . '/Kernel/AllTests.php';
require_once dirname(__FILE__) . '/Kernel_Test.php';
require_once dirname(__FILE__) . '/Logger_Test.php';
require_once dirname(__FILE__) . '/Mail_Test.php';
require_once dirname(__FILE__) . '/Model/AllTests.php';
require_once dirname(__FILE__) . '/Service/AllTests.php';
require_once dirname(__FILE__) . '/Template_Test.php';
require_once dirname(__FILE__) . '/UI/AllTests.php';
require_once dirname(__FILE__) . '/URI_Test.php';
require_once dirname(__FILE__) . '/WebServer_Test.php';

class Core_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Core tests');

		$suite->addTestSuite('Eresus_Auth_Test');
		$suite->addTest(      Eresus_CMS_AllTests::suite());
		$suite->addTestSuite('Eresus_CMS_Test');
		$suite->addTestSuite('Eresus_Config_Test');
		$suite->addTest(      Eresus_Controller_AllTests::suite());
		$suite->addTest(      Eresus_DB_AllTests::suite());
		$suite->addTestSuite('EresusFormTest');
		$suite->addTest(      Core_Helper_AllTests::suite());
		$suite->addTest(      Eresus_HTTP_AllTests::suite());
		$suite->addTestSuite('Eresus_i18n_Test');
		$suite->addTest(      Eresus_Kernel_AllTests::suite());
		$suite->addTestSuite('Eresus_Kernel_Test');
		$suite->addTestSuite('Eresus_Logger_Test');
		$suite->addTestSuite('Eresus_Mail_Test');
		$suite->addTest(      Eresus_Model_AllTests::suite());
		$suite->addTest(      Eresus_Service_AllTests::suite());
		$suite->addTestSuite('Eresus_Template_Test');
		$suite->addTest(      Eresus_UI_AllTests::suite());
		$suite->addTestSuite('Eresus_URI_Test');
		$suite->addTestSuite('Eresus_WebServer_Test');

		return $suite;
	}
}
