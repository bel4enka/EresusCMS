<?php
/**
 * ${product.title}
 *
 * Модульные тесты
 *
 * @version ${product.version}
 *
 * @copyright 2004, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007, Eresus Project, http://eresus.ru/
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

require_once __DIR__ . '/../bootstrap.php';

require_once TESTS_SRC_DIR . '/core/kernel-legacy.php';


class Functions_Test extends PHPUnit_Framework_TestCase
{
	/**
	 *
	 */
	public function test_macroConst()
	{
		define('Functions_Test_Foo', 'bar');
		$this->assertEquals('bar', __macroConst(array(null, 'Functions_Test_Foo')));
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function test_macroVar()
	{
		$GLOBALS['Functions_Test_Foo'] = 'bar';
		$this->assertEquals('bar', __macroVar(array(null, null, 'Functions_Test_Foo')));
		$this->assertEquals('barbaz', __macroVar(array(null, null, 'Functions_Test_Foo', '."baz"')));
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function test_FormatDate()
	{
		$this->assertEquals('14:45, 15 февраля 1987', FormatDate('1987-02-15 14:45:12'));
		$this->assertEquals('Дата и время неизвестны', FormatDate(''));
	}
	//-----------------------------------------------------------------------------
}
