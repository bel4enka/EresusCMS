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

PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

require_once dirname(__FILE__) . '/helpers/AllTests.php';

require_once dirname(__FILE__) . '/AdminModuleTest.php';
require_once dirname(__FILE__) . '/AdminRouteServiceTest.php';
require_once dirname(__FILE__) . '/HtmlElementTest.php';
require_once dirname(__FILE__) . '/HtmlScriptElementTest.php';
require_once dirname(__FILE__) . '/WebPageTest.php';
require_once dirname(__FILE__) . '/PluginsTest.php';
require_once dirname(__FILE__) . '/PluginTest.php';
require_once dirname(__FILE__) . '/EresusExtensionConnectorTest.php';

class Core_Classes_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('core/classes');

		$suite->addTest(Core_Classes_Helpers_AllTests::suite());

		$suite->addTestSuite('AdminModuleTest');
		$suite->addTestSuite('AdminRouteServiceTest');
		$suite->addTestSuite('HtmlElementTest');
		$suite->addTestSuite('HtmlScriptElementTest');
		$suite->addTestSuite('WebPageTest');
		$suite->addTestSuite('PluginsTest');
		$suite->addTestSuite('PluginTest');
		$suite->addTestSuite('EresusExtensionConnectorTest');

		return $suite;
	}
}
