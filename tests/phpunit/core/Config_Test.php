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

require_once __DIR__ . '/../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/Config.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Config_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Config::set
	 * @covers Eresus_Config::get
	 * @covers Eresus_Config::drop
	 */
	public function test_set_get_drop()
	{
		Eresus_Config::set('key1', 'value1');
		$this->assertEquals('value1', Eresus_Config::get('key1'));
		Eresus_Config::drop('key1');
		$this->assertNull(Eresus_Config::get('key1'));

		$this->assertEquals('value2', Eresus_Config::get('key2', 'value2'));
	}
	//-----------------------------------------------------------------------------

	/* */
}
