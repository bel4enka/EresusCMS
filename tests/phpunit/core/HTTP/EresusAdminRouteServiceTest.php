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
require_once dirname(__FILE__) . '/../../../../main/core/CMS.php';
require_once dirname(__FILE__) . '/../../../../main/core/BusinessLogic/EresusAdminController.php';
require_once dirname(__FILE__) . '/../../../../main/core/HTTP/EresusAdminRouteService.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class EresusAdminRouteServiceTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers EresusAdminRouteService::getInstance
	 */
	public function test_interface()
	{
		$test = EresusAdminRouteService::getInstance();
		$this->assertInstanceOf('ServiceInterface', $test);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminRouteService::init
	 */
	public function test_init_no_controller()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$test = EresusAdminRouteService::getInstance();

		$request = $this->getMock('HttpRequest', array('getLocal'));
		$request->expects($this->once())->method('getLocal')->
			will($this->returnValue('/admin/'));

		$test->init($request);

		$pControllerName = new ReflectionProperty('EresusAdminRouteService', 'controllerName');
		$pControllerName->setAccessible(true);

		$pActionName = new ReflectionProperty('EresusAdminRouteService', 'actionName');
		$pActionName->setAccessible(true);

		$pParams = new ReflectionProperty('EresusAdminRouteService', 'params');
		$pParams->setAccessible(true);

		$this->assertEquals('', $pControllerName->getValue($test));
		$this->assertEquals('', $pActionName->getValue($test));
		$this->assertEquals(array(), $pParams->getValue($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminRouteService::init
	 */
	public function test_init_full()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$test = EresusAdminRouteService::getInstance();

		$request = $this->getMock('HttpRequest', array('getLocal'));
		$request->expects($this->once())->method('getLocal')->
			will($this->returnValue('/admin/some_controller/some_method/p1/v1/p2/v2/p3/?a1=av1&a2=av2'));

		$test->init($request);

		$pControllerName = new ReflectionProperty('EresusAdminRouteService', 'controllerName');
		$pControllerName->setAccessible(true);

		$pActionName = new ReflectionProperty('EresusAdminRouteService', 'actionName');
		$pActionName->setAccessible(true);

		$pParams = new ReflectionProperty('EresusAdminRouteService', 'params');
		$pParams->setAccessible(true);

		$this->assertEquals('some_controller', $pControllerName->getValue($test));
		$this->assertEquals('some_method', $pActionName->getValue($test));
		$this->assertEquals(array('p1' => 'v1', 'p2' => 'v2', 'p3' => null), $pParams->getValue($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminRouteService::init
	 */
	public function test_init_no_action()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$test = EresusAdminRouteService::getInstance();

		$request = $this->getMock('HttpRequest', array('getLocal'));
		$request->expects($this->once())->method('getLocal')->
			will($this->returnValue('/admin/some_controller/'));

		$test->init($request);

		$pControllerName = new ReflectionProperty('EresusAdminRouteService', 'controllerName');
		$pControllerName->setAccessible(true);

		$pActionName = new ReflectionProperty('EresusAdminRouteService', 'actionName');
		$pActionName->setAccessible(true);

		$pParams = new ReflectionProperty('EresusAdminRouteService', 'params');
		$pParams->setAccessible(true);

		$this->assertEquals('some_controller', $pControllerName->getValue($test));
		$this->assertEquals('', $pActionName->getValue($test));
		$this->assertEquals(array(), $pParams->getValue($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminRouteService::getController
	 */
	public function test_getController_default()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$test = EresusAdminRouteService::getInstance();

		$pControllerName = new ReflectionProperty('EresusAdminRouteService', 'controllerName');
		$pControllerName->setAccessible(true);
		$pControllerName->setValue($test, '');

		$this->assertNull($test->getController());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminRouteService::getController
	 * @expectedException PageNotFoundException
	 */
	public function test_getController_noFile()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}
		if (!class_exists('vfsStream', false))
		{
			$this->markTestSkipped('vfsStream required for ' . __METHOD__);
		}

		$test = EresusAdminRouteService::getInstance();

		$pControllerName = new ReflectionProperty('EresusAdminRouteService', 'controllerName');
		$pControllerName->setAccessible(true);
		$pControllerName->setValue($test, 'example');

		$pController = new ReflectionProperty('EresusAdminRouteService', 'controller');
		$pController->setAccessible(true);
		$pController->setValue($test, null);

		vfsStreamWrapper::register();
		$htdocs = new vfsStreamDirectory('htdocs');
		vfsStreamWrapper::setRoot($htdocs);

		$app = $this->getMock('stdClass', array('getFsRoot'));
		$app->expects($this->once())->method('getFsRoot')->
			will($this->returnValue(vfsStream::url('htdocs')));

		Core::$app = $app;

		$htdocs->addChild(new vfsStreamDirectory('admin'));
		$htdocs->getChild('admin')->addChild(new vfsStreamDirectory('controllers'));

		$test->getController();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminRouteService::getController
	 * @expectedException LogicException
	 */
	public function test_getController_noClass()
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

		$test = EresusAdminRouteService::getInstance();

		$pControllerName = new ReflectionProperty('EresusAdminRouteService', 'controllerName');
		$pControllerName->setAccessible(true);
		$pControllerName->setValue($test, 'example');

		$pController = new ReflectionProperty('EresusAdminRouteService', 'controller');
		$pController->setAccessible(true);
		$pController->setValue($test, null);

		vfsStreamWrapper::register();
		$htdocs = new vfsStreamDirectory('htdocs');
		vfsStreamWrapper::setRoot($htdocs);

		$app = $this->getMock('stdClass', array('getFsRoot'));
		$app->expects($this->once())->method('getFsRoot')->
			will($this->returnValue(vfsStream::url('htdocs')));

		Core::$app = $app;

		$htdocs->addChild(new vfsStreamDirectory('admin'));
		$htdocs->getChild('admin')->addChild(new vfsStreamDirectory('controllers'));
		$controller = new vfsStreamFile('example.php');
		$controller->setContent('');
		$htdocs->getChild('admin')->getChild('controllers')->addChild($controller);

		$test->getController();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminRouteService::getController
	 * @expectedException LogicException
	 */
	public function test_getController_invalidClass()
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

		$test = EresusAdminRouteService::getInstance();

		$pControllerName = new ReflectionProperty('EresusAdminRouteService', 'controllerName');
		$pControllerName->setAccessible(true);
		$pControllerName->setValue($test, 'example1');

		$pController = new ReflectionProperty('EresusAdminRouteService', 'controller');
		$pController->setAccessible(true);
		$pController->setValue($test, null);

		vfsStreamWrapper::register();
		$htdocs = new vfsStreamDirectory('htdocs');
		vfsStreamWrapper::setRoot($htdocs);

		$app = $this->getMock('stdClass', array('getFsRoot'));
		$app->expects($this->once())->method('getFsRoot')->
			will($this->returnValue(vfsStream::url('htdocs')));

		Core::$app = $app;

		$htdocs->addChild(new vfsStreamDirectory('admin'));
		$htdocs->getChild('admin')->addChild(new vfsStreamDirectory('controllers'));
		$controller = new vfsStreamFile('example1.php');
		$controller->setContent('<?php class Example1Controller {}');
		$htdocs->getChild('admin')->getChild('controllers')->addChild($controller);

		$test->getController();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminRouteService::getController
	 */
	public function test_getController_success()
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

		$test = EresusAdminRouteService::getInstance();

		$pControllerName = new ReflectionProperty('EresusAdminRouteService', 'controllerName');
		$pControllerName->setAccessible(true);
		$pControllerName->setValue($test, 'example2');

		$pController = new ReflectionProperty('EresusAdminRouteService', 'controller');
		$pController->setAccessible(true);
		$pController->setValue($test, null);

		vfsStreamWrapper::register();
		$htdocs = new vfsStreamDirectory('htdocs');
		vfsStreamWrapper::setRoot($htdocs);

		$app = $this->getMock('stdClass', array('getFsRoot'));
		$app->expects($this->once())->method('getFsRoot')->
			will($this->returnValue(vfsStream::url('htdocs')));

		Core::$app = $app;

		$htdocs->addChild(new vfsStreamDirectory('admin'));
		$htdocs->getChild('admin')->addChild(new vfsStreamDirectory('controllers'));
		$controller = new vfsStreamFile('example2.php');
		$controller->setContent('<?php class EresusExample2Controller extends EresusAdminController {function actionIndex($p = array()){return "";}}');
		$htdocs->getChild('admin')->getChild('controllers')->addChild($controller);

		$this->assertInstanceOf('EresusAdminController', $test->getController());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminRouteService::getAction
	 */
	public function test_getAction_default()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$controller = $this->getMock('stdClass', array('actionIndex'));

		$service = $this->getMockBuilder('EresusAdminRouteService')->setMethods(array('getController'))
			->disableOriginalConstructor()->getMock();
		$service->expects($this->once())->method('getController')->will($this->returnValue($controller));

		$pActionName = new ReflectionProperty('EresusAdminRouteService', 'actionName');
		$pActionName->setAccessible(true);
		$pActionName->setValue($service, '');

		$this->assertEquals(array($controller, 'actionIndex'), $service->getAction());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminRouteService::getAction
	 * @expectedException PageNotFoundException
	 */
	public function test_getAction_noMethod()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$controller = $this->getMock('stdClass');

		$service = $this->getMockBuilder('EresusAdminRouteService')->setMethods(array('getController'))
			->disableOriginalConstructor()->getMock();
		$service->expects($this->once())->method('getController')->will($this->returnValue($controller));

		$pActionName = new ReflectionProperty('EresusAdminRouteService', 'actionName');
		$pActionName->setAccessible(true);
		$pActionName->setValue($service, '');

		$service->getAction();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminRouteService::getAction
	 */
	public function test_getAction()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$controller = $this->getMock('stdClass', array('actionMethod'));

		$service = $this->getMockBuilder('EresusAdminRouteService')->setMethods(array('getController'))
			->disableOriginalConstructor()->getMock();
		$service->expects($this->once())->method('getController')->will($this->returnValue($controller));

		$pActionName = new ReflectionProperty('EresusAdminRouteService', 'actionName');
		$pActionName->setAccessible(true);
		$pActionName->setValue($service, 'method');

		$this->assertEquals(array($controller, 'actionmethod'), $service->getAction());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminRouteService::call
	 */
	public function test_call()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required for ' . __METHOD__);
		}

		$controller = $this->getMock('stdClass', array('actionMethod'));
		$controller->expects($this->once())->method('actionMethod')->will($this->returnValue(123));

		$service = $this->getMockBuilder('EresusAdminRouteService')->setMethods(array('getAction'))->
			disableOriginalConstructor()->getMock();
		$service->expects($this->once())->method('getAction')->
			will($this->returnValue(array($controller, 'actionMethod')));

		$this->assertEquals(123, $service->call());
	}
	//-----------------------------------------------------------------------------

	/* */
}