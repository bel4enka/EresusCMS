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

require_once dirname(__FILE__) . '/stubs.php';

PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

$root = realpath(dirname(__FILE__) . '/../..');

/*
 * На некоторых системах некоторые файлы (содержащие абстрактные классы?) включаются не в том
 * порядке, что приводит к ошибкам. Добавление этих файлов в начало белого списка решает проблему.
 */
PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist($root . '/main/core/UI/Admin/List/DataProvider.php');
PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist($root . '/main/core/UI/Admin/List/Mutator.php');
PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist($root . '/main/core/UI/Admin/List/ItemControl.php');
PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist($root . '/main/core/CMS/Mode.php');
PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist($root . '/main/core/CMS/UI.php');

PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhitelist($root . '/main/core');
PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhitelist($root . '/main/admin');

PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist($root . '/main/ext-3rd/editarea/eresus-connector.php');
PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist($root . '/main/ext-3rd/tinymce/eresus-connector.php');
PHP_CodeCoverage_Filter::getInstance()->addFileToWhitelist($root . '/main/ext-3rd/elfinder/eresus-connector.php');

PHP_CodeCoverage_Filter::getInstance()->removeFileFromWhitelist($root . '/main/core/errors.html.php');
PHP_CodeCoverage_Filter::getInstance()->removeFileFromWhitelist($root . '/main/core/fatal.html.php');
PHP_CodeCoverage_Filter::getInstance()->removeFileFromWhitelist($root . '/main/core/gziph.php');

require_once dirname(__FILE__) . '/core/AllTests.php';
require_once dirname(__FILE__) . '/ext-3rd/AllTests.php';

class AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('All Tests');

		$suite->addTest(Core_AllTests::suite());
		$suite->addTest(Ext3rd_AllTests::suite());

		return $suite;
	}
}
