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
require_once dirname(__FILE__) . '/../../../../main/core/Kernel/PHP.php';
require_once dirname(__FILE__) . '/../../../../main/core/classes/EresusExtensionConnector.php';

require_once 'PHPUnit/Extensions/OutputTestCase.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class EresusExtensionConnectorTest extends PHPUnit_Extensions_OutputTestCase
{
	/**
	 * @covers EresusExtensionConnector::__construct
	 * @covers EresusExtensionConnector::getRoot
	 * @covers EresusExtensionConnector::getRootDir
	 */
	public function test_construct()
	{
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->root = 'http://example.org/';
		$GLOBALS['Eresus']->froot = '/home/example.org/htdocs/';
		$test = new EresusExtensionConnector();

		$this->assertEquals('http://example.org/ext-3rd/eresusextension/', $test->getRoot());
		$this->assertEquals('/home/example.org/htdocs/ext-3rd/eresusextension/', $test->getRootDir());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusExtensionConnector::proxyUnexistent
	 * @expectedException ExitException
	 */
	public function test_proxyUnexistent()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$proxyUnexistent = new ReflectionMethod('EresusExtensionConnector', 'proxyUnexistent');
		$proxyUnexistent->setAccessible(true);

		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->root = 'http://example.org/';
		$GLOBALS['Eresus']->froot = '/home/example.org/htdocs/';
		$test = new EresusExtensionConnector();

		$proxyUnexistent->invoke($test, 'somefile');
	}
	//-----------------------------------------------------------------------------

	/* */
}
