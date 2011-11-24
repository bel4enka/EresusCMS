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
 * @package Eresus
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

require_once 'bootstrap.php';

PHP_CodeCoverage_Filter::getInstance()->
	addFileToWhiteList(TESTS_SRC_DIR . '/core/kernel-legacy.php');
PHP_CodeCoverage_Filter::getInstance()->
	addFileToWhiteList(TESTS_SRC_DIR . '/core/lib/accounts.php');
PHP_CodeCoverage_Filter::getInstance()->
	addFileToWhiteList(TESTS_SRC_DIR . '/core/classes/backward/TPlugin.php');
PHP_CodeCoverage_Filter::getInstance()->
	addFileToWhiteList(TESTS_SRC_DIR . '/core/classes/backward/TContentPlugin.php');
PHP_CodeCoverage_Filter::getInstance()->
	addFileToWhiteList(TESTS_SRC_DIR . '/core/Admin/Controller.php');
PHP_CodeCoverage_Filter::getInstance()->
	addFileToWhiteList(TESTS_SRC_DIR . '/core/Console/Command.php');
PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhiteList(TESTS_SRC_DIR);

PHP_CodeCoverage_Filter::getInstance()->removeDirectoryFromWhiteList(TESTS_SRC_DIR . '/cfg');
PHP_CodeCoverage_Filter::getInstance()->removeDirectoryFromWhiteList(TESTS_SRC_DIR . '/ext-3rd');
PHP_CodeCoverage_Filter::getInstance()->removeDirectoryFromWhiteList(TESTS_SRC_DIR . '/lang');
PHP_CodeCoverage_Filter::getInstance()->removeFileFromWhiteList(TESTS_SRC_DIR .
	'/core/errors.html.php');
PHP_CodeCoverage_Filter::getInstance()->removeFileFromWhiteList(TESTS_SRC_DIR .
	'/core/fatal.html.php');
PHP_CodeCoverage_Filter::getInstance()->removeFileFromWhiteList(TESTS_SRC_DIR .
	'/core/gziph.php');
PHP_CodeCoverage_Filter::getInstance()->removeFileFromWhiteList(TESTS_SRC_DIR .
	'/index.php');

require_once __DIR__ . '/core/AllTests.php';

class AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('All Tests');

		$suite->addTest(Eresus_AllTests::suite());

		return $suite;
	}
}
