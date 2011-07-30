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
 * $Id$
 */

require_once dirname(__FILE__) . '/../../stubs.php';
require_once TESTS_SRC_ROOT . '/core/Template.php';
require_once TESTS_SRC_ROOT . '/core/Template/Service.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Template_Service_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		$p_instance = new ReflectionProperty('Eresus_Template_Service', 'instance');
		$p_instance->setAccessible(true);
		$p_instance->setValue('Eresus_Template_Service', null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		Eresus_Config::drop('core.template.templateDir');
		$p_instance = new ReflectionProperty('Eresus_Template_Service', 'instance');
		$p_instance->setAccessible(true);
		$p_instance->setValue('Eresus_Template_Service', null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template_Service::getInstance
	 */
	public function test_getInstance()
	{
		$test = Eresus_Template_Service::getInstance();
		$this->assertSame($test, Eresus_Template_Service::getInstance());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template_Service::get
	 */
	public function test_get()
	{
		$ts = Eresus_Template_Service::getInstance();
		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT);
		$ts->get('default');
		$ts->get('auth', 'core');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template_Service::get
	 * @expectedException LogicException
	 */
	public function test_get_bad_module()
	{
		$ts = Eresus_Template_Service::getInstance();
		$ts->get('auth', 'unexistent');
	}
	//-----------------------------------------------------------------------------

	/* */
}
