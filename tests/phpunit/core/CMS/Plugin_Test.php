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
require_once dirname(__FILE__) . '/../../../../main/core/Kernel.php';
require_once dirname(__FILE__) . '/../../../../main/core/CMS.php';
require_once dirname(__FILE__) . '/../../../../main/core/CMS/Plugin.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_CMS_Plugin_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		$app = new ReflectionProperty('Eresus_Kernel', 'app');
		$app->setAccessible(true);
		$app->setValue('Eresus_Kernel', null);
	}
	//-----------------------------------------------------------------------------
	/**
	 * @covers Eresus_CMS_Plugin::getDataURL
	 */
	public function test_getDataURL()
	{
		$app = $this->getMock('stdClass', array('getRootDir'));
		$app->expects($this->once())->method('getRootDir')->
			will($this->returnValue('/home/exmaple.org'));
		$appProp = new ReflectionProperty('Eresus_Kernel', 'app');
		$appProp->setAccessible(true);
		$appProp->setValue('Eresus_Kernel', $app);

		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->froot = '/home/exmaple.org/';
		$GLOBALS['Eresus']->fdata = '/home/exmaple.org/data/';
		$GLOBALS['Eresus']->fstyle = '/home/exmaple.org/style/';
		$GLOBALS['Eresus']->root = 'http://exmaple.org/';
		$GLOBALS['Eresus']->data = 'http://exmaple.org/data/';
		$GLOBALS['Eresus']->style = 'http://exmaple.org/style/';
		$test = new Eresus_CMS_Plugin();
		$this->assertEquals('http://exmaple.org/data/eresus_cms_plugin/', $test->getDataURL());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Plugin::getCodeURL
	 */
	public function test_getCodeURL()
	{
		$app = $this->getMock('stdClass', array('getRootDir'));
		$app->expects($this->once())->method('getRootDir')->
			will($this->returnValue('/home/exmaple.org'));
		$appProp = new ReflectionProperty('Eresus_Kernel', 'app');
		$appProp->setAccessible(true);
		$appProp->setValue('Eresus_Kernel', $app);

		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->froot = '/home/exmaple.org/';
		$GLOBALS['Eresus']->fdata = '/home/exmaple.org/data/';
		$GLOBALS['Eresus']->fstyle = '/home/exmaple.org/style/';
		$GLOBALS['Eresus']->root = 'http://exmaple.org/';
		$GLOBALS['Eresus']->data = 'http://exmaple.org/data/';
		$GLOBALS['Eresus']->style = 'http://exmaple.org/style/';
		$test = new Eresus_CMS_Plugin();
		$this->assertEquals('http://exmaple.org/ext/eresus_cms_plugin/', $test->getCodeURL());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_Plugin::getStyleURL
	 */
	public function test_getStyleURL()
	{
		$app = $this->getMock('stdClass', array('getRootDir'));
		$app->expects($this->once())->method('getRootDir')->
			will($this->returnValue('/home/exmaple.org'));
		$appProp = new ReflectionProperty('Eresus_Kernel', 'app');
		$appProp->setAccessible(true);
		$appProp->setValue('Eresus_Kernel', $app);

		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->froot = '/home/exmaple.org/';
		$GLOBALS['Eresus']->fdata = '/home/exmaple.org/data/';
		$GLOBALS['Eresus']->fstyle = '/home/exmaple.org/style/';
		$GLOBALS['Eresus']->root = 'http://exmaple.org/';
		$GLOBALS['Eresus']->data = 'http://exmaple.org/data/';
		$GLOBALS['Eresus']->style = 'http://exmaple.org/style/';
		$test = new Eresus_CMS_Plugin();
		$this->assertEquals('http://exmaple.org/style/eresus_cms_plugin/', $test->getStyleURL());
	}
	//-----------------------------------------------------------------------------

	/* */
}
