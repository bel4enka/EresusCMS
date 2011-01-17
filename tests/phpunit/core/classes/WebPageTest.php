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

require_once dirname(__FILE__) . '/../../../../main/core/classes/WebPage.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class WebPageTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Проверяем WebPage::linkScripts
	 *
	 * @covers WebPage::linkScripts
	 * @covers WebPage::renderHeadSection
	 * @covers WebPage::renderBodySection
	 */
	public function test_linkScripts()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$page = new WebPage();
		$page->linkScripts('head.js');
		$page->linkScripts('body.js', 'defer');

		$renderHeadSection = new ReflectionMethod('WebPage', 'renderHeadSection');
		$renderHeadSection->setAccessible(true);
		$this->assertEquals('<script type="text/javascript" src="head.js"></script>', $renderHeadSection->invoke($page));

		$renderBodySection = new ReflectionMethod('WebPage', 'renderBodySection');
		$renderBodySection->setAccessible(true);
		$this->assertEquals('<script type="text/javascript" src="body.js" defer></script>', $renderBodySection->invoke($page));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем WebPage::addScripts
	 *
	 * @covers WebPage::addScripts
	 * @covers WebPage::renderHeadSection
	 * @covers WebPage::renderBodySection
	 */
	public function test_addScripts()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$page = new WebPage();
		$page->addScripts('var head;');
		$page->addScripts('var body;', 'defer');

		$renderHeadSection = new ReflectionMethod('WebPage', 'renderHeadSection');
		$renderHeadSection->setAccessible(true);
		$this->assertEquals("<script type=\"text/javascript\">//<!-- <![CDATA[\nvar head;\n//]] --></script>", $renderHeadSection->invoke($page));

		$renderBodySection = new ReflectionMethod('WebPage', 'renderBodySection');
		$renderBodySection->setAccessible(true);
		$this->assertEquals("<script type=\"text/javascript\">//<!-- <![CDATA[\nvar body;\n//]] --></script>", $renderBodySection->invoke($page));
	}
	//-----------------------------------------------------------------------------

	/* */
}
