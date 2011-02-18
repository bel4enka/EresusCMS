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
require_once dirname(__FILE__) . '/../../../../main/core/Model/Section.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_Model_Section_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Model_Section::setTableDefinition
	 */
	public function test_setTableDefinition()
	{
		$test = $this->getMockBuilder('Eresus_Model_Section')->
			setMethods(array('setTableName', 'hasColumns'))->disableOriginalConstructor()->getMock();
		$test->expects($this->once())->method('setTableName')->with('pages');
		$test->expects($this->once())->method('hasColumns');
		$test->setTableDefinition();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Model_Section::setUp
	 */
	public function test_setUp()
	{
		$test = $this->getMockBuilder('Eresus_Model_Section')->setMethods(array('hasAccessorMutator'))
			->disableOriginalConstructor()->getMock();
		$test->expects($this->once())->method('hasAccessorMutator')->
			with('options', 'unserializeAccessor', 'serializeMutator');
		$test->setUp();
	}
	//-----------------------------------------------------------------------------

	/* */
}
