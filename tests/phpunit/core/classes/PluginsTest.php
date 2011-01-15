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

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/DBAL/EresusORM.php';
require_once dirname(__FILE__) . '/../../../../main/core/DBAL/EresusActiveRecord.php';
require_once dirname(__FILE__) . '/../../../../main/core/Domain/EresusPlugin.php';
require_once dirname(__FILE__) . '/../../../../main/core/classes/Plugins.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class PluginsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Plugins::__construct
	 */
	public function test_construct()
	{
		$Doctrine_Table = $this->getMock('stdClass', array('findAll'));
		$Doctrine_Table->expects($this->once())->method('findAll')->will($this->returnValue(array()));

		$Doctrine_Core = $this->getMock('stdClass', array('getTable'));
		$Doctrine_Core->expects($this->once())->method('getTable')->with('EresusPlugin')->
			will($this->returnValue($Doctrine_Table));
		Doctrine_Core::setMock($Doctrine_Core);

		$test = new Plugins();
	}
	//-----------------------------------------------------------------------------

	/* */
}
