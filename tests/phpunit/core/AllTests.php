<?php
/**
 * ${product.title}
 *
 * @version ${product.version}
 *
 * PhpUnit Tests
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EresusCMS
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
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

require_once dirname(__FILE__) . '/BusinessLogic/AllTests.php';
require_once dirname(__FILE__) . '/Config_Test.php';
require_once dirname(__FILE__) . '/Controller/AllTests.php';
require_once dirname(__FILE__) . '/classes/AllTests.php';
require_once dirname(__FILE__) . '/CMS_Test.php';
require_once dirname(__FILE__) . '/CMS/AllTests.php';
require_once dirname(__FILE__) . '/DB/AllTests.php';
require_once dirname(__FILE__) . '/EresusTest.php';
require_once dirname(__FILE__) . '/EresusFormTest.php';
require_once dirname(__FILE__) . '/Helper/AllTests.php';
require_once dirname(__FILE__) . '/HTTP/AllTests.php';
require_once dirname(__FILE__) . '/Kernel_Test.php';
require_once dirname(__FILE__) . '/Kernel/AllTests.php';
//require_once dirname(__FILE__) . '/lib/AllTests.php';
require_once dirname(__FILE__) . '/Logger_Test.php';
require_once dirname(__FILE__) . '/Mail_Test.php';
require_once dirname(__FILE__) . '/Model/AllTests.php';
require_once dirname(__FILE__) . '/Service/AllTests.php';
require_once dirname(__FILE__) . '/Template_Test.php';
require_once dirname(__FILE__) . '/UI/AllTests.php';
require_once dirname(__FILE__) . '/WebServer_Test.php';

require_once dirname(__FILE__) . '/LegacyFunctionsTest.php';


class Core_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Core tests');

		$suite->addTestSuite('Eresus_Config_Test');
		$suite->addTestSuite('Eresus_CMS_Test');
		$suite->addTest(      Eresus_CMS_AllTests::suite());
		$suite->addTest(      Eresus_DB_AllTests::suite());
		$suite->addTestSuite('EresusTest');
		$suite->addTestSuite('EresusFormTest');
		$suite->addTest(      Core_Helper_AllTests::suite());
		$suite->addTest(      Eresus_HTTP_AllTests::suite());
		$suite->addTest(      Core_Kernel_AllTests::suite());
		$suite->addTestSuite('Eresus_Kernel_Test');
		$suite->addTestSuite('Eresus_Logger_Test');
		$suite->addTestSuite('Eresus_Mail_Test');
		$suite->addTest(      Eresus_Service_AllTests::suite());
		$suite->addTestSuite('Eresus_WebServer_Test');

		$suite->addTestSuite('LegacyFunctionsTest');
		$suite->addTestSuite('Eresus_Template_Test');

		$suite->addTest(Core_BusinessLogic_AllTests::suite());
		$suite->addTest(Core_Domain_AllTests::suite());
		$suite->addTest(Core_Controller_AllTests::suite());
		$suite->addTest(Core_UI_AllTests::suite());
		$suite->addTest(Core_Classes_AllTests::suite());
		//$suite->addTest(Core_Lib_AllTests::suite());

		return $suite;
	}
}
