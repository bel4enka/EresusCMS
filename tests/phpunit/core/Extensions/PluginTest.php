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

require_once __DIR__ . '/../../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/Extensions/Plugin.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class PluginTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Plugin::getDataURL
	 */
	public function test_getDataURL()
	{
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->froot = '/home/exmaple.org/';
		$GLOBALS['Eresus']->fdata = '/home/exmaple.org/data/';
		$GLOBALS['Eresus']->fstyle = '/home/exmaple.org/style/';
		$GLOBALS['Eresus']->root = 'http://exmaple.org/';
		$GLOBALS['Eresus']->data = 'http://exmaple.org/data/';
		$GLOBALS['Eresus']->style = 'http://exmaple.org/style/';
		$test = new Plugin();
		$this->assertEquals('http://exmaple.org/data/plugin/', $test->getDataURL());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Plugin::getCodeURL
	 */
	public function test_getCodeURL()
	{
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->froot = '/home/exmaple.org/';
		$GLOBALS['Eresus']->fdata = '/home/exmaple.org/data/';
		$GLOBALS['Eresus']->fstyle = '/home/exmaple.org/style/';
		$GLOBALS['Eresus']->root = 'http://exmaple.org/';
		$GLOBALS['Eresus']->data = 'http://exmaple.org/data/';
		$GLOBALS['Eresus']->style = 'http://exmaple.org/style/';
		$test = new Plugin();
		$this->assertEquals('http://exmaple.org/ext/plugin/', $test->getCodeURL());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Plugin::getStyleURL
	 */
	public function test_getStyleURL()
	{
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->froot = '/home/exmaple.org/';
		$GLOBALS['Eresus']->fdata = '/home/exmaple.org/data/';
		$GLOBALS['Eresus']->fstyle = '/home/exmaple.org/style/';
		$GLOBALS['Eresus']->root = 'http://exmaple.org/';
		$GLOBALS['Eresus']->data = 'http://exmaple.org/data/';
		$GLOBALS['Eresus']->style = 'http://exmaple.org/style/';
		$test = new Plugin();
		$this->assertEquals('http://exmaple.org/style/plugin/', $test->getStyleURL());
	}
	//-----------------------------------------------------------------------------

	/* */
}
