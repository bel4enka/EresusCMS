<?php
/**
 * ${product.title} ${product.version}
 *
 * Модульные тесты
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
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
require_once TESTS_SRC_ROOT . '/core/CMS/UI.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMS_UI_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_CMS_UI::getInstance
	 * @expectedException LogicException
	 */
	public function test_getInstance_no_instance()
	{
		$inst = Eresus_CMS_UI::getInstance();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_UI::getInstance
	 * @expectedException LogicException
	 */
	public function test_getInstance_invalid_class()
	{
		$inst = Eresus_CMS_UI::getInstance('stdClass');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_UI::getInstance
	 * @covers Eresus_CMS_UI::__construct
	 * @covers Eresus_CMS_UI::getDocument
	 */
	public function test_getInstance()
	{
		$inst = Eresus_CMS_UI::getInstance('Eresus_CMS_UI_Test_UI');
		$this->assertInstanceOf('Eresus_CMS_UI', $inst);
		$this->assertInstanceOf('Eresus_HTML_Document', $inst->getDocument());
	}
	//-----------------------------------------------------------------------------

	/* */
}


class Eresus_CMS_UI_Test_UI extends Eresus_CMS_UI
{
	public function process()
	{
		;
	}
	//-----------------------------------------------------------------------------
}