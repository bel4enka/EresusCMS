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
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/stubs.php';

if (class_exists('PHP_CodeCoverage_Filter', false))
{
	PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

	$root = realpath(dirname(__FILE__) . '/../..');

	if (version_compare(PHP_VERSION, '5.3', '<'))
	{
		PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist($root . '/main/core/classes/backward/TPlugin.php');
		PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist($root . '/main/core/classes/backward/TContentPlugin.php');
	}
	PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhitelist($root . '/main/core');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist($root . '/main/ext-3rd/editarea/eresus-connector.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist($root . '/main/ext-3rd/tinymce/eresus-connector.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist($root . '/main/ext-3rd/elfinder/eresus-connector.php');

	PHP_CodeCoverage_Filter::getInstance()->removeFileFromWhitelist($root . '/main/core/errors.html.php');
	PHP_CodeCoverage_Filter::getInstance()->removeFileFromWhitelist($root . '/main/core/fatal.html.php');
	PHP_CodeCoverage_Filter::getInstance()->removeFileFromWhitelist($root . '/main/core/gziph.php');
	PHP_CodeCoverage_Filter::getInstance()->removeFileFromWhitelist($root . '/main/core/models/User.php');
}
else
{
	PHPUnit_Util_Filter::addFileToFilter(__FILE__);
}

require_once dirname(__FILE__) . '/core/AllTests.php';

class AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('All Tests');

		$suite->addTest(Core_AllTests::suite());

		return $suite;
	}
}
