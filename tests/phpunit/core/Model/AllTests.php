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

require_once dirname(__FILE__) . '/Plugin_Test.php';
require_once dirname(__FILE__) . '/EresusSiteSectionTest.php';
require_once dirname(__FILE__) . '/User_Test.php';

class Core_Domain_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('core/Domain');

		$suite->addTestSuite('Eresus_Model_Plugin_Test');
		$suite->addTestSuite('EresusSiteSectionTest');
		$suite->addTestSuite('Eresus_Model_User_Test');

		return $suite;
	}
}
