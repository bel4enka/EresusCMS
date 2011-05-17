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
 * $Id: PHPTest.php 669 2010-12-04 10:36:49Z mk $
 */

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/Helper/Registry.php';

/**
 * @package Kernel
 * @subpackage Tests
 */
class Eresus_Helper_Registry_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Helper_Registry::set
	 * @covers Eresus_Helper_Registry::get
	 * @covers Eresus_Helper_Registry::drop
	 */
	public function test_set_get_drop()
	{
		Eresus_Helper_Registry::set('key1', 'value1');
		$this->assertEquals('value1', Eresus_Helper_Registry::get('key1'));
		Eresus_Helper_Registry::drop('key1');
		$this->assertNull(Eresus_Helper_Registry::get('key1'));

		$this->assertEquals('value2', Eresus_Helper_Registry::get('key2', 'value2'));
	}
	//-----------------------------------------------------------------------------

	/* */
}
