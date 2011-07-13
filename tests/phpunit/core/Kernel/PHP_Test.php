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

require_once 'PHPUnit/Extensions/OutputTestCase.php';

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/Kernel/PHP.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Kernel_PHP_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @var string
	 */
	private $inclue_path;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		$this->inclue_path = get_include_path();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		set_include_path($this->inclue_path);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel_PHP::inOpenBaseDir
	 */
	public function test_inOpenBaseDir()
	{
		$this->assertTrue(Eresus_Kernel_PHP::inOpenBaseDir('/dir/file', false), 'Test 1');
		$cwd = getcwd();
		$this->assertFalse(Eresus_Kernel_PHP::inOpenBaseDir('/dir/file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 2');
		$this->assertTrue(Eresus_Kernel_PHP::inOpenBaseDir('/dir1/file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 3');
		$this->assertTrue(Eresus_Kernel_PHP::inOpenBaseDir('/dir2/file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 4');
		$this->assertTrue(Eresus_Kernel_PHP::inOpenBaseDir('/dir3/file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 5');
		$this->assertTrue(Eresus_Kernel_PHP::inOpenBaseDir('./file', '/dir1:/dir2:/dir3:' . $cwd), 'Test 6');
	}
	//-----------------------------------------------------------------------------
	/* */
}
