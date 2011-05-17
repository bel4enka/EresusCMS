<?php
/**
 * ${product.title}
 *
 * @version ${product.version}
 *
 * PhpUnit Tests
 *
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * @package Templates
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/Template.php';

class Eresus_Template_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Template::compile
	 * @expectedException RuntimeException
	 */
	public function test_unexistent()
	{
		$exception = new ErrorException('file not found', 0, E_ERROR, 'some_file', 123);
		$dwoo = $this->getMockBuilder('stdClass')->setMethods(array('get'))->getMock();
		$dwoo->expects($this->once())->method('get')->will($this->throwException($exception));

		$test = new Eresus_Template();

		$dwooProp = new ReflectionProperty('Eresus_Template', 'dwoo');
		$dwooProp->setAccessible(true);
		$dwooProp->setValue($test, $dwoo);

		$test->compile();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template::setGlobalValue
	 * @covers Eresus_Template::getGlobalValue
	 * @covers Eresus_Template::removeGlobalValue
	 */
	public function testSetGetRemove()
	{
		Eresus_Template::setGlobalValue('test', 'testValue');
		$this->assertEquals('testValue', Eresus_Template::getGlobalValue('test'));
		Eresus_Template::removeGlobalValue('test');
		$this->assertNull(Eresus_Template::getGlobalValue('test'));
	}
	//-----------------------------------------------------------------------------

	/* */
}
