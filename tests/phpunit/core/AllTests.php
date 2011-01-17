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

require_once dirname(__FILE__) . '/EresusCMSTest.php';
require_once dirname(__FILE__) . '/EresusTest.php';
require_once dirname(__FILE__) . '/EresusFormTest.php';
require_once dirname(__FILE__) . '/LegacyFunctionsTest.php';

require_once dirname(__FILE__) . '/AccessControl/AllTests.php';
require_once dirname(__FILE__) . '/BusinessLogic/AllTests.php';
require_once dirname(__FILE__) . '/DBAL/AllTests.php';
require_once dirname(__FILE__) . '/Domain/AllTests.php';
require_once dirname(__FILE__) . '/Helpers/AllTests.php';
require_once dirname(__FILE__) . '/UI/AllTests.php';
require_once dirname(__FILE__) . '/classes/AllTests.php';
require_once dirname(__FILE__) . '/lib/AllTests.php';

class Core_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Core tests');

		$suite->addTestSuite('EresusCMSTest');
		$suite->addTestSuite('EresusTest');
		$suite->addTestSuite('EresusFormTest');
		$suite->addTestSuite('LegacyFunctionsTest');

		$suite->addTest(Core_AccessControl_AllTests::suite());
		$suite->addTest(Core_BusinessLogic_AllTests::suite());
		$suite->addTest(Core_DBAL_AllTests::suite());
		$suite->addTest(Core_Domain_AllTests::suite());
		$suite->addTest(Core_Helpers_AllTests::suite());
		$suite->addTest(Core_UI_AllTests::suite());
		$suite->addTest(Core_Classes_AllTests::suite());
		$suite->addTest(Core_Lib_AllTests::suite());

		return $suite;
	}
}
