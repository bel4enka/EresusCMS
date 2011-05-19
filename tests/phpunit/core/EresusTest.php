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

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/Kernel.php';
require_once dirname(__FILE__) . '/../../../main/core/CMS.php';
require_once dirname(__FILE__) . '/../../../main/core/kernel-legacy.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class EresusTest extends PHPUnit_Framework_TestCase
{
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		MockFacade::setMock(null);
		$app = new ReflectionProperty('Eresus_Kernel', 'app');
		$app->setAccessible(true);
		$app->setValue('Eresus_Kernel', null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка определения путей сайта
	 *
	 * @covers Eresus::init_resolve
	 */
	public function test_froot_and_path()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$_SERVER['SCRIPT_FILENAME'] = '/home/user/public_html/site/index.php';
		$_SERVER['DOCUMENT_ROOT'] = '/home/user/public_html';

		$app = $this->getMock('stdClass', array('getFsRoot'));
		$app->expects($this->once())->method('getFsRoot')->
			will($this->returnValue('/home/user/public_html/site'));

		$appProp = new ReflectionProperty('Eresus_Kernel', 'app');
		$appProp->setAccessible(true);
		$appProp->setValue('Eresus_Kernel', $app);

		$init_resolve = new ReflectionMethod('Eresus', 'init_resolve');
		$init_resolve->setAccessible(true);

		$mock = $this->getMockBuilder('Eresus')->disableOriginalConstructor()->getMock();
		$init_resolve->invoke($mock);

		$this->assertEquals('/home/user/public_html/site/', $mock->froot, 'Invalid Eresus::$froot');
		$this->assertEquals('/site/', $mock->path, 'Invalid Eresus::$path');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка определения путей сайта в Windows
	 *
	 * @covers Eresus::init_resolve
	 */
	public function test_froot_and_path_Windows()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$_SERVER['SCRIPT_FILENAME'] =  'c:\\index.php';
		$_SERVER['DOCUMENT_ROOT'] = 'c:/';

		$app = $this->getMock('stdClass', array('getFsRoot'));
		$app->expects($this->once())->method('getFsRoot')->
			will($this->returnValue('/c:'));

		$appProp = new ReflectionProperty('Eresus_Kernel', 'app');
		$appProp->setAccessible(true);
		$appProp->setValue('Eresus_Kernel', $app);

		$init_resolve = new ReflectionMethod('Eresus', 'init_resolve');
		$init_resolve->setAccessible(true);

		$mock = $this->getMockBuilder('Eresus')->disableOriginalConstructor()->getMock();
		$init_resolve->invoke($mock);

		$this->assertEquals('/c:/', $mock->froot, 'Invalid Eresus::$froot');
		$this->assertEquals('/', $mock->path, 'Invalid Eresus::$path');
	}
	//-----------------------------------------------------------------------------

	/* */
}
