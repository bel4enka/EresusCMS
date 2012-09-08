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
 * @package Eresus_CMS
 * @subpackage Tests
 * @author Михаил Красильников <mk@eresus.ru>
 *
 * $Id: CMS_Test.php 2187 2012-05-24 17:07:44Z mk $
 */

require_once __DIR__ . '/../../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/Eresus/Sections.php';

/**
 * @package Eresus_CMS
 * @subpackage Tests
 */
class Eresus_SectionsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Sections::branch_ids
	 */
	public function test_branch_ids()
	{
		$branch_ids = new ReflectionMethod('Eresus_Sections', 'branch_ids');
		$branch_ids->setAccessible(true);
		
		$p_index = new ReflectionProperty('Eresus_Sections', 'index');
		$p_index->setAccessible(true);

		$sections = new Eresus_Sections();
		$this->assertEquals(array(), $branch_ids->invoke($sections, 2));
		
		
		$p_index->setValue($sections, array(
				0=>array(1, 2, 3),
				1=>array(4, 5),
				2=>array(),
				3=>array()
			));
		$test = $p_index->getValue($sections);
		$this->assertEquals(array(
				0=>array(1, 2, 3),
				1=>array(4, 5),
				2=>array(),
				3=>array()
			), $test);
			
	  $this->assertEquals(array(1, 2, 3, 4, 5), $branch_ids->invoke($sections, 0));
		$this->assertEquals(array(4, 5), $branch_ids->invoke($sections, 1));
		
		
		
	}
	/* */
}
