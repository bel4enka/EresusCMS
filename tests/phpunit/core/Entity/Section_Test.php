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
 * @package Eresus
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * $Id: Section_Test.php 1609 2011-05-18 09:46:37Z mk $
 */

require_once dirname(__FILE__) . '/../../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/DB/Record.php';
require_once TESTS_SRC_DIR . '/core/Entity/Section.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Entity_Section_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Entity_Section::setTableDefinition
	 */
	public function test_setTableDefinition()
	{
		$test = $this->getMockBuilder('Eresus_Entity_Section')->
			setMethods(array('setTableName', 'hasColumns'))->disableOriginalConstructor()->getMock();
		$test->expects($this->once())->method('setTableName')->with('pages');
		$test->expects($this->once())->method('hasColumns');
		$test->setTableDefinition();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Entity_Section::setUp
	 */
	public function test_setUp()
	{
		$test = $this->getMockBuilder('Eresus_Entity_Section')->setMethods(array('hasAccessorMutator'))
			->disableOriginalConstructor()->getMock();
		$test->expects($this->once())->method('hasAccessorMutator')->
			with('options', 'unserializeAccessor', 'serializeMutator');
		$test->setUp();
	}
	//-----------------------------------------------------------------------------

	/* */
}
