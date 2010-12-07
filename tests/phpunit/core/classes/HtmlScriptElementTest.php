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

require_once dirname(__FILE__) . '/../../../../main/core/classes/WebPage.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class HtmlScriptElementTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Проверяем простую конструкцию
	 *
	 * @covers HtmlScriptElement::__construct
	 */
	public function test_simple()
	{
		$test = new HtmlScriptElement();
		$this->assertEquals('<script type="text/javascript"></script>', $test->getHTML());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем конструктор с URL
	 *
	 * @covers HtmlScriptElement::__construct
	 */
	public function test_cusntruct_URL()
	{
		$test = new HtmlScriptElement('http://example.org/some_script');
		$this->assertEquals('<script type="text/javascript" src="http://example.org/some_script"></script>', $test->getHTML());

		$test = new HtmlScriptElement('/some_script.js');
		$this->assertEquals('<script type="text/javascript" src="/some_script.js"></script>', $test->getHTML());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем конструктор с кодом
	 *
	 * @covers HtmlScriptElement::__construct
	 * @covers HtmlScriptElement::setContents
	 */
	public function test_cusntruct_code()
	{
		$test = new HtmlScriptElement('alert("Hello world");');
		$this->assertEquals("<script type=\"text/javascript\">//<!-- <![CDATA[\nalert(\"Hello world\");\n//]] --></script>", $test->getHTML());

		$test = new HtmlScriptElement('alert("test");');
		$this->assertEquals("<script type=\"text/javascript\">//<!-- <![CDATA[\nalert(\"test\");\n//]] --></script>", $test->getHTML());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем async и defer
	 *
	 * @covers HtmlScriptElement::__construct
	 */
	public function test_cusntruct_async_defer()
	{
		$test = new HtmlScriptElement('script.js');
		$test->setAttribute('defer');
		$this->assertEquals('<script type="text/javascript" src="script.js" defer></script>', $test->getHTML());
	}
	//-----------------------------------------------------------------------------

	/* */
}
