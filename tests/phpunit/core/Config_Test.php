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
 * @package Kernel
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/Config.php';

/**
 * @package Kernel
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
