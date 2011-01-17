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

@require_once 'vfsStream/vfsStream.php';

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/main.php';
require_once dirname(__FILE__) . '/../../../../main/core/classes/AdminModule.php';
require_once dirname(__FILE__) . '/../../../../main/core/classes/AdminRouteService.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class AdminRouteServiceTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers AdminRouteService::getInstance
	 */
	public function test_interface()
	{
		$test = AdminRouteService::getInstance();
		$this->assertInstanceOf('ServiceInterface', $test);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers AdminRouteService::init
	 */
	public function test_init_no_module()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$test = AdminRouteService::getInstance();

		$request = $this->getMock('HttpRequest', array('getLocal'));
		$request->expects($this->once())->method('getLocal')->
			will($this->returnValue('/admin/'));

		$test->init($request);

		$pModuleName = new ReflectionProperty('AdminRouteService', 'moduleName');
		$pModuleName->setAccessible(true);

		$pActionName = new ReflectionProperty('AdminRouteService', 'actionName');
		$pActionName->setAccessible(true);

		$pParams = new ReflectionProperty('AdminRouteService', 'params');
		$pParams->setAccessible(true);

		$this->assertEquals('', $pModuleName->getValue($test));
		$this->assertEquals('', $pActionName->getValue($test));
		$this->assertEquals(array(), $pParams->getValue($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers AdminRouteService::init
	 */
	public function test_init_full()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$test = AdminRouteService::getInstance();

		$request = $this->getMock('HttpRequest', array('getLocal'));
		$request->expects($this->once())->method('getLocal')->
			will($this->returnValue('/admin/some_module/some_method/p1/v1/p2/v2/p3/?a1=av1&a2=av2'));

		$test->init($request);

		$pModuleName = new ReflectionProperty('AdminRouteService', 'moduleName');
		$pModuleName->setAccessible(true);

		$pActionName = new ReflectionProperty('AdminRouteService', 'actionName');
		$pActionName->setAccessible(true);

		$pParams = new ReflectionProperty('AdminRouteService', 'params');
		$pParams->setAccessible(true);

		$this->assertEquals('some_module', $pModuleName->getValue($test));
		$this->assertEquals('some_method', $pActionName->getValue($test));
		$this->assertEquals(array('p1' => 'v1', 'p2' => 'v2', 'p3' => null), $pParams->getValue($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers AdminRouteService::init
	 */
	public function test_init_no_action()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$test = AdminRouteService::getInstance();

		$request = $this->getMock('HttpRequest', array('getLocal'));
		$request->expects($this->once())->method('getLocal')->
			will($this->returnValue('/admin/some_module/'));

		$test->init($request);

		$pModuleName = new ReflectionProperty('AdminRouteService', 'moduleName');
		$pModuleName->setAccessible(true);

		$pActionName = new ReflectionProperty('AdminRouteService', 'actionName');
		$pActionName->setAccessible(true);

		$pParams = new ReflectionProperty('AdminRouteService', 'params');
		$pParams->setAccessible(true);

		$this->assertEquals('some_module', $pModuleName->getValue($test));
		$this->assertEquals('', $pActionName->getValue($test));
		$this->assertEquals(array(), $pParams->getValue($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers AdminRouteService::getModule
	 */
	public function test_getModule_default()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$test = AdminRouteService::getInstance();

		$pModuleName = new ReflectionProperty('AdminRouteService', 'moduleName');
		$pModuleName->setAccessible(true);
		$pModuleName->setValue($test, '');

		$this->assertNull($test->getModule());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers AdminRouteService::getModule
	 * @expectedException PageNotFoundException
	 */
	public function test_getModule_noFile()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}
		if (!class_exists('vfsStream', false))
		{
			$this->markTestSkipped('vfsStream required for ' . __METHOD__);
		}

		$test = AdminRouteService::getInstance();

		$pModuleName = new ReflectionProperty('AdminRouteService', 'moduleName');
		$pModuleName->setAccessible(true);
		$pModuleName->setValue($test, 'example');

		$pModule = new ReflectionProperty('AdminRouteService', 'module');
		$pModule->setAccessible(true);
		$pModule->setValue($test, null);

		vfsStreamWrapper::register();
		$htdocs = new vfsStreamDirectory('htdocs');
		vfsStreamWrapper::setRoot($htdocs);

		$app = $this->getMock('stdClass', array('getFsRoot'));
		$app->expects($this->once())->method('getFsRoot')->
			will($this->returnValue(vfsStream::url('htdocs')));

		Core::$app = $app;

		$htdocs->addChild(new vfsStreamDirectory('admin'));
		$htdocs->getChild('admin')->addChild(new vfsStreamDirectory('modules'));

		$test->getModule();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers AdminRouteService::getModule
	 * @expectedException LogicException
	 */
	public function test_getModule_noClass()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}
		if (!class_exists('vfsStream', false))
		{
			$this->markTestSkipped('vfsStream required for ' . __METHOD__);
		}
		if (extension_loaded('suhosin') &&
			strpos(ini_get('suhosin.executor.include.whitelist'), 'vfs') === false)
		{
			$this->markTestSkipped(__METHOD__ . ' needs "vfs" to be allowed in "suhosin.executor.include.whitelist" option');
		}

		$test = AdminRouteService::getInstance();

		$pModuleName = new ReflectionProperty('AdminRouteService', 'moduleName');
		$pModuleName->setAccessible(true);
		$pModuleName->setValue($test, 'example');

		$pModule = new ReflectionProperty('AdminRouteService', 'module');
		$pModule->setAccessible(true);
		$pModule->setValue($test, null);

		vfsStreamWrapper::register();
		$htdocs = new vfsStreamDirectory('htdocs');
		vfsStreamWrapper::setRoot($htdocs);

		$app = $this->getMock('stdClass', array('getFsRoot'));
		$app->expects($this->once())->method('getFsRoot')->
			will($this->returnValue(vfsStream::url('htdocs')));

		Core::$app = $app;

		$htdocs->addChild(new vfsStreamDirectory('admin'));
		$htdocs->getChild('admin')->addChild(new vfsStreamDirectory('modules'));
		$module = new vfsStreamFile('example.php');
		$module->setContent('');
		$htdocs->getChild('admin')->getChild('modules')->addChild($module);

		$test->getModule();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers AdminRouteService::getModule
	 * @expectedException LogicException
	 */
	public function test_getModule_invalidClass()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}
		if (!class_exists('vfsStream', false))
		{
			$this->markTestSkipped('vfsStream required for ' . __METHOD__);
		}
		if (extension_loaded('suhosin') &&
			strpos(ini_get('suhosin.executor.include.whitelist'), 'vfs') === false)
		{
			$this->markTestSkipped(__METHOD__ . ' needs "vfs" to be allowed in "suhosin.executor.include.whitelist" option');
		}

		$test = AdminRouteService::getInstance();

		$pModuleName = new ReflectionProperty('AdminRouteService', 'moduleName');
		$pModuleName->setAccessible(true);
		$pModuleName->setValue($test, 'example1');

		$pModule = new ReflectionProperty('AdminRouteService', 'module');
		$pModule->setAccessible(true);
		$pModule->setValue($test, null);

		vfsStreamWrapper::register();
		$htdocs = new vfsStreamDirectory('htdocs');
		vfsStreamWrapper::setRoot($htdocs);

		$app = $this->getMock('stdClass', array('getFsRoot'));
		$app->expects($this->once())->method('getFsRoot')->
			will($this->returnValue(vfsStream::url('htdocs')));

		Core::$app = $app;

		$htdocs->addChild(new vfsStreamDirectory('admin'));
		$htdocs->getChild('admin')->addChild(new vfsStreamDirectory('modules'));
		$module = new vfsStreamFile('example1.php');
		$module->setContent('<?php class Example1Module {}');
		$htdocs->getChild('admin')->getChild('modules')->addChild($module);

		$test->getModule();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers AdminRouteService::getModule
	 */
	public function test_getModule_success()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}
		if (!class_exists('vfsStream', false))
		{
			$this->markTestSkipped('vfsStream required for ' . __METHOD__);
		}
		if (extension_loaded('suhosin') &&
			strpos(ini_get('suhosin.executor.include.whitelist'), 'vfs') === false)
		{
			$this->markTestSkipped(__METHOD__ . ' needs "vfs" to be allowed in "suhosin.executor.include.whitelist" option');
		}

		$test = AdminRouteService::getInstance();

		$pModuleName = new ReflectionProperty('AdminRouteService', 'moduleName');
		$pModuleName->setAccessible(true);
		$pModuleName->setValue($test, 'example2');

		$pModule = new ReflectionProperty('AdminRouteService', 'module');
		$pModule->setAccessible(true);
		$pModule->setValue($test, null);

		vfsStreamWrapper::register();
		$htdocs = new vfsStreamDirectory('htdocs');
		vfsStreamWrapper::setRoot($htdocs);

		$app = $this->getMock('stdClass', array('getFsRoot'));
		$app->expects($this->once())->method('getFsRoot')->
			will($this->returnValue(vfsStream::url('htdocs')));

		Core::$app = $app;

		$htdocs->addChild(new vfsStreamDirectory('admin'));
		$htdocs->getChild('admin')->addChild(new vfsStreamDirectory('modules'));
		$module = new vfsStreamFile('example2.php');
		$module->setContent('<?php class Example2Module extends AdminModule {function actionIndex($p = array()){return "";}}');
		$htdocs->getChild('admin')->getChild('modules')->addChild($module);

		$this->assertInstanceOf('AdminModule', $test->getModule());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers AdminRouteService::getAction
	 */
	public function test_getAction_default()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$module = $this->getMock('stdClass', array('actionIndex'));

		$service = $this->getMockBuilder('AdminRouteService')->setMethods(array('getModule'))
			->disableOriginalConstructor()->getMock();
		$service->expects($this->once())->method('getModule')->will($this->returnValue($module));

		$pActionName = new ReflectionProperty('AdminRouteService', 'actionName');
		$pActionName->setAccessible(true);
		$pActionName->setValue($service, '');

		$this->assertEquals(array($module, 'actionIndex'), $service->getAction());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers AdminRouteService::getAction
	 * @expectedException PageNotFoundException
	 */
	public function test_getAction_noMethod()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$module = $this->getMock('stdClass');

		$service = $this->getMockBuilder('AdminRouteService')->setMethods(array('getModule'))
			->disableOriginalConstructor()->getMock();
		$service->expects($this->once())->method('getModule')->will($this->returnValue($module));

		$pActionName = new ReflectionProperty('AdminRouteService', 'actionName');
		$pActionName->setAccessible(true);
		$pActionName->setValue($service, '');

		$service->getAction();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers AdminRouteService::getAction
	 */
	public function test_getAction()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$module = $this->getMock('stdClass', array('actionMethod'));

		$service = $this->getMockBuilder('AdminRouteService')->setMethods(array('getModule'))
			->disableOriginalConstructor()->getMock();
		$service->expects($this->once())->method('getModule')->will($this->returnValue($module));

		$pActionName = new ReflectionProperty('AdminRouteService', 'actionName');
		$pActionName->setAccessible(true);
		$pActionName->setValue($service, 'method');

		$this->assertEquals(array($module, 'actionmethod'), $service->getAction());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers AdminRouteService::call
	 */
	public function test_call()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$module = $this->getMock('stdClass', array('actionMethod'));
		$module->expects($this->once())->method('actionMethod')->will($this->returnValue(123));

		$service = $this->getMockBuilder('AdminRouteService')->setMethods(array('getAction'))->
			disableOriginalConstructor()->getMock();
		$service->expects($this->once())->method('getAction')->
			will($this->returnValue(array($module, 'actionMethod')));

		$this->assertEquals(123, $service->call());
	}
	//-----------------------------------------------------------------------------

	/* */
}