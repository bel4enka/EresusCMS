<?php
/**
 * ${product.title}
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package Eresus
 */

require_once TESTS_SRC_DIR . '/core/Eresus/HTML/ScriptElement.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class HtmlScriptElementTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Проверяем простую конструкцию
	 *
	 * @covers Eresus_HTML_ScriptElement::__construct
	 */
	public function test_simple()
	{
		$test = new Eresus_HTML_ScriptElement();
		$this->assertEquals('<script type="text/javascript"></script>', $test->getHTML());
	}

	/**
	 * Проверяем конструктор с URL
	 *
	 * @covers Eresus_HTML_ScriptElement::__construct
	 */
	public function test_construct_URL()
	{
		$test = new Eresus_HTML_ScriptElement('http://example.org/some_script');
		$this->assertEquals(
			'<script type="text/javascript" src="http://example.org/some_script"></script>',
			$test->getHTML()
		);

		$test = new Eresus_HTML_ScriptElement('/some_script.js');
		$this->assertEquals(
			'<script type="text/javascript" src="/some_script.js"></script>',
			$test->getHTML()
		);
	}

	/**
	 * Проверяем конструктор с кодом
	 *
	 * @covers Eresus_HTML_ScriptElement::__construct
	 * @covers Eresus_HTML_ScriptElement::setContents
	 */
	public function test_construct_code()
	{
		$test = new Eresus_HTML_ScriptElement('alert("Hello world");');
		$this->assertEquals(
			"<script type=\"text/javascript\">//<!-- <![CDATA[\n" .
				"alert(\"Hello world\");\n//]] --></script>",
			$test->getHTML()
		);

		$test = new Eresus_HTML_ScriptElement('jQuery.webshims.polyfill();');
		$this->assertEquals(
			"<script type=\"text/javascript\">//<!-- <![CDATA[\n" .
				"jQuery.webshims.polyfill();\n//]] --></script>",
			$test->getHTML()
		);
	}

	/**
	 * Проверяем async и defer
	 *
	 * @covers Eresus_HTML_ScriptElement::__construct
	 */
	public function test_construct_async_defer()
	{
		$test = new Eresus_HTML_ScriptElement('script.js');
		$test->setAttribute('defer');
		$this->assertEquals('<script type="text/javascript" src="script.js" defer></script>',
			$test->getHTML());
	}

	/* */
}
