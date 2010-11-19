<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 * @package Tests
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../../helpers.php';

require_once TEST_DIR_ROOT . '/core/kernel-legacy.php';
require_once TEST_DIR_ROOT . '/core/framework/core/EresusApplication.php';

/**
 * @package Tests
 */
class EresusTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Восстановление окружения
	 */
	protected function tearDown()
	{
		Core::testModeUnset('PHP::isCLI');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка определения путей сайта
	 */
	public function test_froot_and_path()
	{
		Core::testModeSet('PHP::isCLI', false);
		$_SERVER['SCRIPT_FILENAME'] = '/home/user/public_html/site/index.php';
		$_SERVER['DOCUMENT_ROOT'] = '/home/user/public_html';

		$app = new EresusTest_Application();
		Core::testSetApplication($app);

		$stub = new EresusTest_Eresus();
		$stub->init_resolve();

		$this->assertEquals('/home/user/public_html/site/', $stub->froot, 'Invalid Eresus::$froot');
		$this->assertEquals('/site/', $stub->path, 'Invalid Eresus::$path');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка определения путей сайта в Windows
	 */
	public function test_froot_and_path_Windows()
	{
		Core::testModeSet('PHP::isCLI', false);
		FS::init(new WindowsFS());
		$_SERVER['SCRIPT_FILENAME'] =  'c:\\index.php';
		$_SERVER['DOCUMENT_ROOT'] = 'c:/';

		$app = new EresusTest_Application();
		Core::testSetApplication($app);

		$stub = new EresusTest_Eresus();
		$stub->init_resolve();

		$this->assertEquals('c:\\', $stub->froot);
		$this->assertEquals('/', $stub->path, 'Invalid Eresus::$path');
	}
	//-----------------------------------------------------------------------------

	/**/
}

class EresusTest_Application extends EresusApplication
{
	public function main()
	{
		return 0;
	}
	//-----------------------------------------------------------------------------
}

class EresusTest_Eresus extends Eresus
{
	public function init_resolve()
	{
		parent::init_resolve();
	}
	//-----------------------------------------------------------------------------

}