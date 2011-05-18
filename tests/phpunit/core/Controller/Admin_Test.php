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
require_once dirname(__FILE__) . '/../../../../main/core/CMS/Service.php';
require_once dirname(__FILE__) . '/../../../../main/core/Security/AuthService.php';
require_once dirname(__FILE__) . '/../../../../main/core/DB/Record.php';
require_once dirname(__FILE__) . '/../../../../main/core/Model/User.php';
require_once dirname(__FILE__) . '/../../../../main/core/kernel-legacy.php';
require_once dirname(__FILE__) . '/../../../../main/core/Service/Admin/Router.php';
require_once dirname(__FILE__) . '/../../../../main/core/i18n.php';
require_once dirname(__FILE__) . '/../../../../main/core/Controller/Admin.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_Controller_Admin_Test extends PHPUnit_Framework_TestCase
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
			$instance = new ReflectionProperty('Eresus_Service_Admin_Router', 'instance');
			$instance->setAccessible(true);
			$instance->setValue('Eresus_Service_Admin_Router', null);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Controller_Admin::__construct
	 */
	public function test_render_construct()
	{
		$ui = new stdClass();

		$test = new Eresus_Controller_Admin($ui);

		$this->assertAttributeSame($ui, 'ui', $test);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Controller_Admin::setController
	 * @covers Eresus_Controller_Admin::getController
	 */
	public function test_setgetController()
	{
		$controller = new stdClass();

		$mock = $this->getMockBuilder('Eresus_Controller_Admin')->setMethods(array('__constrcut'))->
			disableOriginalConstructor()->getMock();
		$mock->setController($controller);
		$this->assertSame($controller, $mock->getController());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Controller_Admin::render
	 */
	public function test_render_logged()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$user = new stdClass();
		$user->access = 1;

		$Eresus_Security_AuthService = $this->getMock('stdClass', array('getUser'));
		$Eresus_Security_AuthService->expects($this->once())->method('getUser')->
			will($this->returnValue($user));

		$instance = new ReflectionProperty('Eresus_Security_AuthService', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('Eresus_Security_AuthService', $Eresus_Security_AuthService);

		$HttpRequest = $this->getMock('HttpRequest', array('getLocal'));
		$HttpRequest->expects($this->any())->method('getLocal')->will($this->returnValue(''));
		HTTP::$request = $HttpRequest;

		$ui = $this->getMock('stdClass', array('render'));
		$ui->expects($this->once())->method('render');

		$Eresus_Service_Admin_Router = $this->getMock('stdClass', array('call', 'init', 'getController'));

		$instance = new ReflectionProperty('Eresus_Service_Admin_Router', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('Eresus_Service_Admin_Router', $Eresus_Service_Admin_Router);

		$Eresus_Controller_Admin = new Eresus_Controller_Admin($ui);
		$Eresus_Controller_Admin->render();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Controller_Admin::render
	 */
	public function test_render_logged_logout()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$user = new stdClass();
		$user->access = 1;

		$Eresus_Security_AuthService = $this->getMock('stdClass', array('getUser', 'logout'));
		$Eresus_Security_AuthService->expects($this->once())->method('getUser')->
			will($this->returnValue($user));
		$Eresus_Security_AuthService->expects($this->once())->method('logout');

		$instance = new ReflectionProperty('Eresus_Security_AuthService', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('Eresus_Security_AuthService', $Eresus_Security_AuthService);

		$HttpRequest = $this->getMock('HttpRequest', array('getLocal'));
		$HttpRequest->expects($this->any())->method('getLocal')->
			will($this->returnValue('/admin/logout/'));
		HTTP::$request = $HttpRequest;

		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->root = null;

		$ui = $this->getMock('stdClass', array('render'));
		$ui->expects($this->once())->method('render');

		$Eresus_Service_Admin_Router = $this->getMock('stdClass', array('call', 'init', 'getController'));

		$instance = new ReflectionProperty('Eresus_Service_Admin_Router', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('Eresus_Service_Admin_Router', $Eresus_Service_Admin_Router);

		$Eresus_Controller_Admin = new Eresus_Controller_Admin($ui);
		$Eresus_Controller_Admin->render();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Controller_Admin::auth
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

		$Eresus_Controller_Admin = new Eresus_Controller_Admin($ui);

		$auth = new ReflectionMethod('Eresus_Controller_Admin', 'auth');
		$auth->setAccessible(true);
		$auth->invoke($Eresus_Controller_Admin);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Controller_Admin::auth
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

		$Eresus_Security_AuthService = $this->getMock('stdClass', array('login'));
		$Eresus_Security_AuthService->expects($this->once())->method('login')->
			will($this->returnValue(-1));
		$instance = new ReflectionProperty('Eresus_Security_AuthService', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('Eresus_Security_AuthService', $Eresus_Security_AuthService);

		$ui = $this->getMock('stdClass', array('getAuthScreen'));
		$ui->expects($this->once())->method('getAuthScreen');

		$Eresus_Controller_Admin = new Eresus_Controller_Admin($ui);

		$auth = new ReflectionMethod('Eresus_Controller_Admin', 'auth');
		$auth->setAccessible(true);
		$auth->invoke($Eresus_Controller_Admin);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Controller_Admin::auth
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

		$Eresus_Security_AuthService = $this->getMock('stdClass', array('login', 'setCookies'));
		$Eresus_Security_AuthService->expects($this->once())->method('login')->
			will($this->returnValue(Eresus_Security_AuthService::SUCCESS));
		$instance = new ReflectionProperty('Eresus_Security_AuthService', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('Eresus_Security_AuthService', $Eresus_Security_AuthService);

		$ui = $this->getMock('stdClass', array('getAuthScreen'));
		$ui->expects($this->once())->method('getAuthScreen');

		$Eresus_Controller_Admin = new Eresus_Controller_Admin($ui);

		$auth = new ReflectionMethod('Eresus_Controller_Admin', 'auth');
		$auth->setAccessible(true);
		$auth->invoke($Eresus_Controller_Admin);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Controller_Admin::getContentHTML
	 */
	public function test_getContentHTML_using_Eresus_Service_Admin_Router()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$HttpRequest = $this->getMock('HttpRequest', array('getLocal'));
		$HttpRequest->expects($this->any())->method('getLocal')->will($this->returnValue(''));
		HTTP::$request = $HttpRequest;

		$ui = $this->getMock('stdClass', array('render'));

		$getContentHTML = new ReflectionMethod('Eresus_Controller_Admin', 'getContentHTML');
		$getContentHTML->setAccessible(true);

		$Eresus_Controller_Admin = new Eresus_Controller_Admin($ui);
		$getContentHTML->invoke($Eresus_Controller_Admin);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Controller_Admin::getContentHTML
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

		$getContentHTML = new ReflectionMethod('Eresus_Controller_Admin', 'getContentHTML');
		$getContentHTML->setAccessible(true);

		$Eresus_Controller_Admin = new Eresus_Controller_Admin($ui);

		$GLOBALS['Eresus'] = new stdClass;
		$GLOBALS['Eresus']->request = array('arg' => array('mod' => 'ext-dummy'));

		$plugin = $this->getMock('stdClass', array('adminRender'));

		$GLOBALS['Eresus']->plugins = $this->getMock('stdClass', array('load'));
		$GLOBALS['Eresus']->plugins->expects($this->once())->method('load')->
			will($this->returnValue($plugin));

		$getContentHTML->invoke($Eresus_Controller_Admin);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Controller_Admin::getContentHTML
	 */
	public function test_getContentHTML_using_arg_plugin_exception()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$app = $this->getMock('stdClass', array('getFsRoot'));
		$app->expects($this->once())->method('getFsRoot')->
			will($this->returnValue('/home/exmaple.org'));
		$appProp = new ReflectionProperty('Eresus_Kernel', 'app');
		$appProp->setAccessible(true);
		$appProp->setValue('Eresus_Kernel', $app);

		$HttpRequest = $this->getMock('HttpRequest', array('getLocal'));
		$HttpRequest->expects($this->any())->method('getLocal')->will($this->returnValue(''));
		HTTP::$request = $HttpRequest;

		$ui = $this->getMock('stdClass', array('render'));

		$getContentHTML = new ReflectionMethod('Eresus_Controller_Admin', 'getContentHTML');
		$getContentHTML->setAccessible(true);

		$Eresus_Controller_Admin = new Eresus_Controller_Admin($ui);

		$GLOBALS['Eresus'] = new stdClass;
		$GLOBALS['Eresus']->request = array('arg' => array('mod' => 'ext-dummy'));

		$plugin = $this->getMock('stdClass', array('adminRender'));
		$plugin->expects($this->once())->method('adminRender')->
			will($this->throwException(new Exception));

		$GLOBALS['Eresus']->plugins = $this->getMock('stdClass', array('load'));
		$GLOBALS['Eresus']->plugins->expects($this->once())->method('load')->
			will($this->returnValue($plugin));

		$getContentHTML->invoke($Eresus_Controller_Admin);
	}
	//-----------------------------------------------------------------------------

	/* */
}
