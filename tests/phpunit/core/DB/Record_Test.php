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
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/DBAL/EresusActiveRecord.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class EresusActiveRecordTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers EresusActiveRecord::unserializeAccessor
	 */
	public function test_unserializeAccessor()
	{
		$test = $this->getMock('EresusActiveRecord', array('_set', '_get'));
		$test->expects($this->once())->method('_set')->with('a', array());
		$test->expects($this->once())->method('_get')->with('a', false)->
			will($this->returnValue(null));
		$test->unserializeAccessor(true, 'a');

		$test = $this->getMock('EresusActiveRecord', array('_set', '_get'));
		$test->expects($this->once())->method('_set')->with('a', array());
		$test->expects($this->once())->method('_get')->with('a', false)->
			will($this->returnValue(''));
		$test->unserializeAccessor(true, 'a');

		$test = $this->getMock('EresusActiveRecord', array('_set', '_get'));
		$test->expects($this->once())->method('_set')->with('a', array('a' => 'b'));
		$test->expects($this->once())->method('_get')->with('a', false)->
			will($this->returnValue('a:1:{s:1:"a";s:1:"b";}'));
		$test->unserializeAccessor(true, 'a');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusActiveRecord::serializeMutator
	 */
	public function test_serializeMutator()
	{
		$test = $this->getMock('EresusActiveRecord', array('_set'));
		$test->expects($this->once())->method('_set')->with('a', 'a:1:{s:1:"a";s:1:"b";}');
		$test->serializeMutator(array('a' => 'b'), true, 'a');
	}
	//-----------------------------------------------------------------------------

	/* */
}
