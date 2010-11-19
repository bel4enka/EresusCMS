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

	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/kernel-legacy.php');
	PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhitelist('../../main/core/lib');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/about.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/admin.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/classes.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/client.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/content.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/EresusForm.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/files.php');
	//PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/gziph.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/i18n.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/languages.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/main.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/pages.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/plgmgr.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/settings.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/themes.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../main/core/users.php');
	PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhitelist('../../main/core/classes');
	PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhitelist('../../lib');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../ext-3rd/editarea/eresus-connector.php');
	PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist('../../ext-3rd/tinymce/eresus-connector.php');
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
