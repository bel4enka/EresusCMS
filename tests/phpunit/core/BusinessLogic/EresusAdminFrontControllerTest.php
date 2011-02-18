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
 * $Id: AdminUITest.php 1369 2011-01-16 20:04:53Z mk $
 */

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/CMS.php';
require_once dirname(__FILE__) . '/../../../../main/core/CMS/Service.php';
require_once dirname(__FILE__) . '/../../../../main/core/AccessControl/EresusAuthService.php';
require_once dirname(__FILE__) . '/../../../../main/core/DBAL/EresusActiveRecord.php';
require_once dirname(__FILE__) . '/../../../../main/core/Model/User.php';
require_once dirname(__FILE__) . '/../../../../main/core/kernel-legacy.php';
require_once dirname(__FILE__) . '/../../../../main/core/HTTP/EresusAdminRouteService.php';
require_once dirname(__FILE__) . '/../../../../main/core/i18n.php';
require_once dirname(__FILE__) . '/../../../../main/core/BusinessLogic/EresusAdminFrontController.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class EresusAdminFrontControllerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		HTTP::$request = null;
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			$instance = new ReflectionProperty('EresusAdminRouteService', 'instance');
			$instance->setAccessible(true);
			$instance->setValue('EresusAdminRouteService', null);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminFrontController::__construct
	 */
	public function test_render_construct()
	{
		$ui = new stdClass();

		$test = new EresusAdminFrontController($ui);

		$this->assertAttributeSame($ui, 'ui', $test);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminFrontController::setController
	 * @covers EresusAdminFrontController::getController
	 */
	public function test_setgetController()
	{
		$controller = new stdClass();

		$mock = $this->getMockBuilder('EresusAdminFrontController')->setMethods(array('__constrcut'))->
			disableOriginalConstructor()->getMock();
		$mock->setController($controller);
		$this->assertSame($controller, $mock->getController());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminFrontController::render
	 */
	public function test_render_logged()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$user = new stdClass();
		$user->access = 1;

		$EresusAuthService = $this->getMock('stdClass', array('getUser'));
		$EresusAuthService->expects($this->once())->method('getUser')->will($this->returnValue($user));

		$instance = new ReflectionProperty('EresusAuthService', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('EresusAuthService', $EresusAuthService);

		$HttpRequest = $this->getMock('HttpRequest', array('getLocal'));
		$HttpRequest->expects($this->any())->method('getLocal')->will($this->returnValue(''));
		HTTP::$request = $HttpRequest;

		$ui = $this->getMock('stdClass', array('render'));
		$ui->expects($this->once())->method('render');

		$EresusAdminRouteService = $this->getMock('stdClass', array('call', 'init'));

		$instance = new ReflectionProperty('EresusAdminRouteService', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('EresusAdminRouteService', $EresusAdminRouteService);

		$EresusAdminFrontController = new EresusAdminFrontController($ui);
		$EresusAdminFrontController->render();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminFrontController::render
	 */
	public function test_render_logged_logout()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$user = new stdClass();
		$user->access = 1;

		$EresusAuthService = $this->getMock('stdClass', array('getUser', 'logout'));
		$EresusAuthService->expects($this->once())->method('getUser')->will($this->returnValue($user));
		$EresusAuthService->expects($this->once())->method('logout');

		$instance = new ReflectionProperty('EresusAuthService', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('EresusAuthService', $EresusAuthService);

		$HttpRequest = $this->getMock('HttpRequest', array('getLocal'));
		$HttpRequest->expects($this->any())->method('getLocal')->
			will($this->returnValue('/admin/logout/'));
		HTTP::$request = $HttpRequest;

		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->root = null;

		$ui = $this->getMock('stdClass', array('render'));
		$ui->expects($this->once())->method('render');

		$EresusAdminRouteService = $this->getMock('stdClass', array('call', 'init'));

		$instance = new ReflectionProperty('EresusAdminRouteService', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('EresusAdminRouteService', $EresusAdminRouteService);

		$EresusAdminFrontController = new EresusAdminFrontController($ui);
		$EresusAdminFrontController->render();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminFrontController::auth
	 */
	public function test_auth_GET()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$HttpRequest = $this->getMock('stdClass', array('getMethod'));
		$HttpRequest->expects($this->once())->method('getMethod')->will($this->returnValue('GET'));
		HTTP::$request = $HttpRequest;

		$ui = $this->getMock('stdClass', array('getAuthScreen'));
		$ui->expects($this->once())->method('getAuthScreen');

		$EresusAdminFrontController = new EresusAdminFrontController($ui);

		$auth = new ReflectionMethod('EresusAdminFrontController', 'auth');
		$auth->setAccessible(true);
		$auth->invoke($EresusAdminFrontController);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminFrontController::auth
	 */
	public function test_auth_POST_failed()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$HttpRequest = $this->getMock('stdClass', array('getMethod', 'arg'));
		$HttpRequest->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));
		$HttpRequest->expects($this->exactly(2))->method('arg')->will($this->returnArgument(0));
		HTTP::$request = $HttpRequest;

		$EresusAuthService = $this->getMock('stdClass', array('login'));
		$EresusAuthService->expects($this->once())->method('login')->will($this->returnValue(-1));
		$instance = new ReflectionProperty('EresusAuthService', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('EresusAuthService', $EresusAuthService);

		$ui = $this->getMock('stdClass', array('getAuthScreen'));
		$ui->expects($this->once())->method('getAuthScreen');

		$EresusAdminFrontController = new EresusAdminFrontController($ui);

		$auth = new ReflectionMethod('EresusAdminFrontController', 'auth');
		$auth->setAccessible(true);
		$auth->invoke($EresusAdminFrontController);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminFrontController::auth
	 */
	public function test_auth_POST_success()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$HttpRequest = $this->getMock('stdClass', array('getMethod', 'arg'));
		$HttpRequest->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));
		$HttpRequest->expects($this->exactly(3))->method('arg')->will($this->returnArgument(0));
		HTTP::$request = $HttpRequest;

		$EresusAuthService = $this->getMock('stdClass', array('login', 'setCookies'));
		$EresusAuthService->expects($this->once())->method('login')->
			will($this->returnValue(EresusAuthService::SUCCESS));
		$instance = new ReflectionProperty('EresusAuthService', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('EresusAuthService', $EresusAuthService);

		$ui = $this->getMock('stdClass', array('getAuthScreen'));
		$ui->expects($this->once())->method('getAuthScreen');

		$EresusAdminFrontController = new EresusAdminFrontController($ui);

		$auth = new ReflectionMethod('EresusAdminFrontController', 'auth');
		$auth->setAccessible(true);
		$auth->invoke($EresusAdminFrontController);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminFrontController::getContentHTML
	 */
	public function test_getContentHTML_using_EresusAdminRouteService()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$HttpRequest = $this->getMock('HttpRequest', array('getLocal'));
		$HttpRequest->expects($this->any())->method('getLocal')->will($this->returnValue(''));
		HTTP::$request = $HttpRequest;

		$ui = $this->getMock('stdClass', array('render'));

		$getContentHTML = new ReflectionMethod('EresusAdminFrontController', 'getContentHTML');
		$getContentHTML->setAccessible(true);

		$EresusAdminFrontController = new EresusAdminFrontController($ui);
		$getContentHTML->invoke($EresusAdminFrontController);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminFrontController::getContentHTML
	 */
	public function test_getContentHTML_using_arg_plugin()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$HttpRequest = $this->getMock('HttpRequest', array('getLocal'));
		$HttpRequest->expects($this->any())->method('getLocal')->will($this->returnValue(''));
		HTTP::$request = $HttpRequest;

		$ui = $this->getMock('stdClass', array('render'));

		$getContentHTML = new ReflectionMethod('EresusAdminFrontController', 'getContentHTML');
		$getContentHTML->setAccessible(true);

		$EresusAdminFrontController = new EresusAdminFrontController($ui);

		$GLOBALS['Eresus'] = new stdClass;
		$GLOBALS['Eresus']->request = array('arg' => array('mod' => 'ext-dummy'));

		$plugin = $this->getMock('stdClass', array('adminRender'));

		$GLOBALS['Eresus']->plugins = $this->getMock('stdClass', array('load'));
		$GLOBALS['Eresus']->plugins->expects($this->once())->method('load')->
			will($this->returnValue($plugin));

		$getContentHTML->invoke($EresusAdminFrontController);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusAdminFrontController::getContentHTML
	 */
	public function test_getContentHTML_using_arg_plugin_exception()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$HttpRequest = $this->getMock('HttpRequest', array('getLocal'));
		$HttpRequest->expects($this->any())->method('getLocal')->will($this->returnValue(''));
		HTTP::$request = $HttpRequest;

		$ui = $this->getMock('stdClass', array('render'));

		$getContentHTML = new ReflectionMethod('EresusAdminFrontController', 'getContentHTML');
		$getContentHTML->setAccessible(true);

		$EresusAdminFrontController = new EresusAdminFrontController($ui);

		$GLOBALS['Eresus'] = new stdClass;
		$GLOBALS['Eresus']->request = array('arg' => array('mod' => 'ext-dummy'));

		$plugin = $this->getMock('stdClass', array('adminRender'));
		$plugin->expects($this->once())->method('adminRender')->
			will($this->throwException(new Exception));

		$GLOBALS['Eresus']->plugins = $this->getMock('stdClass', array('load'));
		$GLOBALS['Eresus']->plugins->expects($this->once())->method('load')->
			will($this->returnValue($plugin));

		$getContentHTML->invoke($EresusAdminFrontController);
	}
	//-----------------------------------------------------------------------------

	/* */
}
