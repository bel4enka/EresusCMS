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
require_once dirname(__FILE__) . '/../../../../main/core/CMS/Service.php';
require_once dirname(__FILE__) . '/../../../../main/core/CMS/Event.php';
require_once dirname(__FILE__) . '/../../../../main/core/Helper/Collection.php';
require_once dirname(__FILE__) . '/../../../../main/core/Service/Events.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_Service_Events_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Service_Events::getInstance
	 */
	public function test_interface()
	{
		$test = Eresus_Service_Events::getInstance();
		$this->assertInstanceOf('Eresus_CMS_Service', $test);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Service_Events::addListener
	 * @covers Eresus_Service_Events::dispatch
	 */
	public function test_dispatch()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}
		$test = Eresus_Service_Events::getInstance();

		$test->addListener('event1', function ($e) {$e->a = 'A';});
		$test->addListener('event2', function ($e) {throw new Exception;});

		$e = new Eresus_CMS_Event('event1');
		$test->dispatch($e);
		$this->assertEquals('A', $e->a);
	}
	//-----------------------------------------------------------------------------

	/* */
}