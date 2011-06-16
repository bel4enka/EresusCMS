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
 * $Id: ContentPlugin_Test.php 1656 2011-06-16 08:18:07Z mk $
 */

require_once dirname(__FILE__) . '/../../../stubs.php';
require_once dirname(__FILE__) . '/../../../../../main/core/CMS/UI.php';
require_once dirname(__FILE__) . '/../../../../../main/core/CMS/UI/Client.php';
require_once dirname(__FILE__) . '/../../../../../main/core/CMS/Mode.php';
require_once dirname(__FILE__) . '/../../../../../main/core/CMS/Mode/Web.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_CMS_Mode_Web_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_CMS_Mode_Web::initSession
	 */
	public function test_initSession()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$initSession = new ReflectionMethod('Eresus_CMS_Mode_Web', 'initSession');
		$initSession->setAccessible(true);

		ini_set('session.save_path', '/tmp');
		$mode = $this->getMockBuilder('Eresus_CMS_Mode_Web')->disableOriginalConstructor()->getMock();

		$initSession->invoke($mode);
	}
	//-----------------------------------------------------------------------------

	/* */
}
