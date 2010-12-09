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
 * @package EresusCMS
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../../../../main/core/classes/EresusActiveRecord.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class EresusActiveRecordTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers EresusActiveRecord::unserialize
	 */
	public function test_unserialize()
	{
		$test = $this->getMock('EresusActiveRecord', array('_set'));
		$test->expects($this->once())->method('_set')->with('a', array());
		$test->unserialize(null, true, 'a');

		$test = $this->getMock('EresusActiveRecord', array('_set'));
		$test->expects($this->once())->method('_set')->with('a', array());
		$test->unserialize('', true, 'a');

		$test = $this->getMock('EresusActiveRecord', array('_set'));
		$test->expects($this->once())->method('_set')->with('a', array('a' => 'b'));
		$test->unserialize('a:1:{s:1:"a";s:1:"b";}', true, 'a');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusActiveRecord::serialize
	 */
	public function test_serialize()
	{
		$test = $this->getMock('EresusActiveRecord', array('_set'));
		$test->expects($this->once())->method('_set')->with('a', 'a:1:{s:1:"a";s:1:"b";}');
		$test->serialize(array('a' => 'b'), true, 'a');
	}
	//-----------------------------------------------------------------------------

	/* */
}
