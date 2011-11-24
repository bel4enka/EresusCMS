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
 * $Id: AllTests.php 1659 2011-06-16 09:50:38Z mk $
 */

PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

require_once __DIR__ . '/Plugin_Test.php';
require_once __DIR__ . '/Section_Test.php';
require_once __DIR__ . '/User_Test.php';

class Eresus_Entity_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('All/Eresus/Entity');

		$suite->addTestSuite('Eresus_Entity_Plugin_Test');
		$suite->addTestSuite('Eresus_Entity_Section_Test');
		$suite->addTestSuite('Eresus_Entity_User_Test');

		return $suite;
	}
}
