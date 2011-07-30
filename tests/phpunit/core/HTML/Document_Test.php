<?php
/**
 * ${product.title}
 *
 * Модульные тесты
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
 * @subpackage Tests
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../../stubs.php';
require_once TESTS_SRC_ROOT . '/core/HTML/Document.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_HTML_Document_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_HTML_Document::setTemplate
	 */
	public function test_setTemplate()
	{
		$doc = new Eresus_HTML_Document();
		$doc->setTemplate('template_name', 'module_name');

		$p_template = new ReflectionProperty('Eresus_HTML_Document', 'template');
		$p_template->setAccessible(true);

		$this->assertEquals(array('template_name', 'module_name'), $p_template->getValue($doc));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTML_Document::setVar
	 */
	public function test_setVar()
	{
		$doc = new Eresus_HTML_Document();
		$doc->setVar('a', 'b');
		$doc->setVar('c', 'd');

		$p_vars = new ReflectionProperty('Eresus_HTML_Document', 'vars');
		$p_vars->setAccessible(true);

		$this->assertEquals(array('a' => 'b', 'c' => 'd'), $p_vars->getValue($doc));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTML_Document::linkStylesheet
	 */
	public function test_linkStylesheet()
	{
		$doc = new Eresus_HTML_Document();
		$doc->linkStylesheet('default.css');
		$doc->linkStylesheet('print.css', 'print');

		$p_stylesheets = new ReflectionProperty('Eresus_HTML_Document', 'stylesheets');
		$p_stylesheets->setAccessible(true);

		$this->assertEquals(
			array('default.css' => array('media' => ''), 'print.css' => array('media' => 'print')),
			$p_stylesheets->getValue($doc));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTML_Document::linkScript
	 */
	public function test_linkScript()
	{
		$doc = new Eresus_HTML_Document();
		$doc->linkScript('1.js');
		$doc->linkScript('2.js', 'async');
		$doc->linkScript('3.js', 'defer');
		$doc->linkScript('4.js', 'async', 'defer');

		$p_scripts = new ReflectionProperty('Eresus_HTML_Document', 'scripts');
		$p_scripts->setAccessible(true);

		$this->assertEquals(
			array('1.js' => array(), '2.js' => array('async'), '3.js' => array('defer'),
			'4.js' => array('async', 'defer')),
			$p_scripts->getValue($doc));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTML_Document::compileStylesheets
	 */
	public function test_compileStylesheets()
	{
		$doc = new Eresus_HTML_Document();
		$doc->linkStylesheet('default.css');
		$doc->linkStylesheet('print.css', 'print');

		$m_compileStylesheets = new ReflectionMethod('Eresus_HTML_Document', 'compileStylesheets');
		$m_compileStylesheets->setAccessible(true);

		$this->assertEquals(
			'<link rel="stylesheet" href="default.css">' . "\n" .
			'<link rel="stylesheet" href="print.css" media="print">' . "\n",
			$m_compileStylesheets->invoke($doc));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTML_Document::compileScripts
	 */
	public function test_compileScripts()
	{
		$doc = new Eresus_HTML_Document();
		$doc->linkScript('1.js');
		$doc->linkScript('2.js', 'async');
		$doc->linkScript('3.js', 'defer');
		$doc->linkScript('4.js', 'async', 'defer');

		$m_compileScripts = new ReflectionMethod('Eresus_HTML_Document', 'compileScripts');
		$m_compileScripts->setAccessible(true);

		$this->assertEquals(
			'<script src="1.js"></script>' . "\n" .
			'<script src="2.js" async></script>' . "\n" .
			'<script src="3.js" defer></script>' . "\n" .
			'<script src="4.js" async defer></script>' . "\n",
			$m_compileScripts->invoke($doc));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTML_Document::compile
	 */
	public function test_compile()
	{
		$doc = new Eresus_HTML_Document();
		$doc->setTemplate('template', 'module');
		$doc->linkStylesheet('styles.css', 'print');
		$doc->linkScript('scripts.js', 'async', 'defer');

		$tmpl = $this->getMock('stdClass', array('compile'));
		$tmpl->expects($this->once())->method('compile')->with(array())->
			will($this->returnValue('<html><head></head><body></body></html>'));

		$ts = $this->getMock('stdClass', array('get'));
		$ts->expects($this->once())->method('get')->with('template', 'module')->
			will($this->returnValue($tmpl));

		$p_instance = new ReflectionProperty('Eresus_Template_Service', 'instance');
		$p_instance->setAccessible(true);
		$p_instance->setValue('Eresus_Template_Service', $ts);

		$this->assertEquals(
			'<html><head><link rel="stylesheet" href="styles.css" media="print">' . "\n" .
			'<script src="scripts.js" async defer></script>' . "\n" .
			'</head><body></body></html>',
			$doc->compile());
	}
	//-----------------------------------------------------------------------------

	/* */
}
