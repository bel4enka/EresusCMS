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
 * $Id: EresusTest.php 1285 2010-12-10 15:34:43Z mk $
 */

require_once 'PHPUnit/Extensions/OutputTestCase.php';

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/main.php';
require_once dirname(__FILE__) . '/../../../../main/core/classes/EresusExtensionConnector.php';
require_once dirname(__FILE__) . '/../../../../main/ext-3rd/elfinder/eresus-connector.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class elFinderConnectorTest extends PHPUnit_Extensions_OutputTestCase
{
	/**
	 * @covers elFinderConnector::getOptions
	 */
	public function test_getOptions()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$getOptions = new ReflectionMethod('elFinderConnector', 'getOptions');
		$getOptions->setAccessible(true);

		$test = $this->getMockBuilder('elFinderConnector')->setMethods(array())->
			disableOriginalConstructor()->getMock();

		$this->assertType('array', $getOptions->invoke($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers elFinderConnector::prepare
	 */
	public function test_prepare()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$prepare = new ReflectionMethod('elFinderConnector', 'prepare');
		$prepare->setAccessible(true);

		$test = $this->getMockBuilder('elFinderConnector')->setMethods(array())->
			disableOriginalConstructor()->getMock();

		$GLOBALS['Eresus'] = new stdClass;
		$GLOBALS['Eresus']->root = 'http://example.org/';

		$GLOBALS['page'] = $this->getMock('stdClass', array('linkScripts', 'linkStyles'));
		$GLOBALS['page']->expects($this->exactly(2))->method('linkScripts');
		$GLOBALS['page']->expects($this->exactly(1))->method('linkStyles');

		$prepare->invoke($test);
		$prepare->invoke($test);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers elFinderConnector::proxyUnexistent
	 * @expectedException ExitException
	 */
	public function test_proxyUnexistent_generic()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$proxyUnexistent = new ReflectionMethod('elFinderConnector', 'proxyUnexistent');
		$proxyUnexistent->setAccessible(true);

		$test = $this->getMockBuilder('elFinderConnector')->
			setMethods(array('proxyUnexistent'))->disableOriginalConstructor()->
			getMock();

		$proxyUnexistent->invoke($test, 'http://example.org/ext-3rd/elfinder/somefile.php');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers elFinderConnector::proxyUnexistent
	 */
	public function test_proxyUnexistent_databrowser()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$proxyUnexistent = new ReflectionMethod('elFinderConnector', 'proxyUnexistent');
		$proxyUnexistent->setAccessible(true);

		$test = $this->getMockBuilder('elFinderConnector')->
			setMethods(array('proxyUnexistent', 'dataConnector'))->disableOriginalConstructor()->
			getMock();

		$test->expects($this->once())->method('dataConnector')->will($this->returnValue(null));

		$proxyUnexistent->invoke($test, 'http://example.org/ext-3rd/elfinder/databrowser.php');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers elFinderConnector::proxyUnexistent
	 */
	public function test_proxyUnexistent_datapopup()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$proxyUnexistent = new ReflectionMethod('elFinderConnector', 'proxyUnexistent');
		$proxyUnexistent->setAccessible(true);

		$test = $this->getMockBuilder('elFinderConnector')->
			setMethods(array('proxyUnexistent', 'dataPopup'))->disableOriginalConstructor()->
			getMock();

		$test->expects($this->once())->method('dataPopup')->will($this->returnValue(null));

		$proxyUnexistent->invoke($test, 'http://example.org/ext-3rd/elfinder/datapopup.php');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers elFinderConnector::getInitScript
	 */
	public function test_getInitScript()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$getInitScript = new ReflectionMethod('elFinderConnector', 'getInitScript');
		$getInitScript->setAccessible(true);

		$test = $this->getMockBuilder('elFinderConnector')->setMethods(array())->
			disableOriginalConstructor()->getMock();

		$GLOBALS['Eresus'] = new stdClass;
		$GLOBALS['Eresus']->root = 'http://example.org/';

		$s = $getInitScript->invoke($test, 'data');
		$this->assertNotEquals(false, strpos($s, 'http://example.org/ext-3rd/elfinder/databrowser.php'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers elFinderConnector::getDataBrowser
	 */
	public function test_getDataBrowser()
	{
		$test = $this->getMockBuilder('elFinderConnector')->setMethods(array('prepare'))->
			disableOriginalConstructor()->getMock();
		$test->expects($this->once())->method('prepare');

		$GLOBALS['Eresus'] = new stdClass;
		$GLOBALS['Eresus']->root = 'http://example.org/';

		$GLOBALS['page'] = $this->getMock('stdClass', array('addScripts'));
		$GLOBALS['page']->expects($this->once())->method('addScripts');

		$test->getDataBrowser();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers elFinderConnector::dataConnector
	 */
	public function test_dataConnector()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$dataConnector = new ReflectionMethod('elFinderConnector', 'dataConnector');
		$dataConnector->setAccessible(true);

		$test = $this->getMockBuilder('elFinderConnector')->
			setMethods(array('getOptions'))->disableOriginalConstructor()->
			getMock();

		$test->expects($this->once())->method('getOptions')->will($this->returnValue(array()));

		$GLOBALS['Eresus'] = new stdClass;
		$GLOBALS['Eresus']->root = 'http://example.org/';
		$GLOBALS['Eresus']->froot = '/home/xample.org/htdocs/';

		$dataConnector->invoke($test);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers elFinderConnector::dataPopup
	 * @expectedException ExitException
	 */
	public function test_dataPopup()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$dataPopup = new ReflectionMethod('elFinderConnector', 'dataPopup');
		$dataPopup->setAccessible(true);

		$test = $this->getMockBuilder('elFinderConnector')->
			setMethods(array('getInitScript'))->disableOriginalConstructor()->
			getMock();

		$test->expects($this->once())->method('getInitScript')->with('data')
			->will($this->returnValue(array()));

		$GLOBALS['Eresus'] = new stdClass;
		$GLOBALS['Eresus']->root = 'http://example.org/';

		$dataPopup->invoke($test);
	}
	//-----------------------------------------------------------------------------

	/* */
}
