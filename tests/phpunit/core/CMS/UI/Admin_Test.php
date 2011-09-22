<?php
/**
 * ${product.title} ${product.version}
 *
 * Модульные тесты
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package Eresus
 * @subpackage Tests
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../../../stubs.php';
require_once TESTS_SRC_ROOT . '/core/Auth.php';
require_once TESTS_SRC_ROOT . '/core/DB/Record.php';
require_once TESTS_SRC_ROOT . '/core/CMS/Exception/Forbidden.php';
require_once TESTS_SRC_ROOT . '/core/CMS/Exception/NotFound.php';
require_once TESTS_SRC_ROOT . '/core/CMS/UI.php';
require_once TESTS_SRC_ROOT . '/core/CMS/Request.php';
require_once TESTS_SRC_ROOT . '/core/CMS/Response.php';
require_once TESTS_SRC_ROOT . '/core/Config.php';
require_once TESTS_SRC_ROOT . '/core/HTML/Document.php';
require_once TESTS_SRC_ROOT . '/core/HTTP/Exception/HeadersSent.php';
require_once TESTS_SRC_ROOT . '/core/HTTP/Request.php';
require_once TESTS_SRC_ROOT . '/core/HTTP/Request/Arguments.php';
require_once TESTS_SRC_ROOT . '/core/HTTP/Response.php';
require_once TESTS_SRC_ROOT . '/core/i18n.php';
require_once TESTS_SRC_ROOT . '/core/Kernel.php';
require_once TESTS_SRC_ROOT . '/core/Logger.php';
require_once TESTS_SRC_ROOT . '/core/Model/User.php';
require_once TESTS_SRC_ROOT . '/core/Security.php';
require_once TESTS_SRC_ROOT . '/core/Template.php';
require_once TESTS_SRC_ROOT . '/core/Template/Service.php';
require_once TESTS_SRC_ROOT . '/core/UI/Admin/Theme.php';
require_once TESTS_SRC_ROOT . '/core/UI/Menu.php';
require_once TESTS_SRC_ROOT . '/core/UI/Menu/Admin.php';
require_once TESTS_SRC_ROOT . '/core/UI/Menu/Item.php';
require_once TESTS_SRC_ROOT . '/core/UI/Menu/Admin/Item.php';
require_once TESTS_SRC_ROOT . '/core/URI.php';
require_once TESTS_SRC_ROOT . '/core/URI/Query.php';
require_once TESTS_SRC_ROOT . '/core/WebServer.php';

require_once TESTS_SRC_ROOT . '/core/CMS/UI/Admin.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMS_UI_Admin_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		$app = $this->getMock('stdClass', array('getRootDir'));
		Eresus_Tests::setStatic('Eresus_Kernel', $app, 'app');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		Eresus_Config::drop('core.template.templateDir');
		Eresus_Tests::setStatic('Eresus_Kernel', null, 'app');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_UI_Admin::getTheme
	 */
	public function test_getTheme()
	{
		$ui = new Eresus_CMS_UI_Admin();
		$p_theme = new ReflectionProperty('Eresus_CMS_UI_Admin', 'theme');
		$p_theme->setAccessible(true);
		$p_theme->setValue($ui, 'theme');
		$this->assertEquals('theme', $ui->getTheme());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_UI_Admin::process
	 * @covers Eresus_CMS_UI_Admin::auth
	 * @covers Eresus_CMS_UI_Admin::getAuthScreen
	 */
	public function test_process_authDialog()
	{
		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT);
		$ui = new Eresus_CMS_UI_Admin();

		$user = new stdClass;
		$p_user = new ReflectionProperty('Eresus_Auth', 'user');
		$p_user->setAccessible(true);
		$auth = Eresus_Auth::getInstance();
		$p_user->setValue($auth, $user);

		$user->access = 0;

		$ui->process();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_UI_Admin::process
	 * @covers Eresus_CMS_UI_Admin::auth
	 * @covers Eresus_CMS_UI_Admin::getAuthScreen
	 * @expectedException Eresus_HTTP_Exception_HeadersSent
	 */
	public function test_process_auth()
	{
		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT);
		$ui = new Eresus_CMS_UI_Admin();

		$user = new stdClass;
		$p_user = new ReflectionProperty('Eresus_Auth', 'user');
		$p_user->setAccessible(true);
		$auth = Eresus_Auth::getInstance();
		$p_user->setValue($auth, $user);
		$user->access = 0;

		$post = $this->getMock('stdClass', array('get'));
		$post->expects($this->any())->method('get')->will($this->returnCallback(
			function ($key)
			{
				static $values = array(null, 'user', 'pass', true);
				return next($values);
			}
		));

		$request = $this->getMock('stdClass', array('isPOST', 'getPost', 'getRootPrefix', 'getHeader'));
		$request->expects($this->any())->method('isPOST')->will($this->returnValue(true));
		$request->expects($this->any())->method('getPost')->will($this->returnValue($post));
		Eresus_Tests::setStatic('Eresus_CMS_Request', $request);

		$table = $this->getMock('stdClass', array('findByUsername'));

		$doctrine = $this->getMock('stdClass', array('getTable'));
		$doctrine->expects($this->any())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($doctrine);

		$Eresus_Auth = $this->getMock('stdClass', array('login', 'getUser', 'setCookies'));
		$Eresus_Auth->expects($this->any())->method('login')->with('user', 'pass')->
			will($this->returnValue(Eresus_Auth::SUCCESS));
		$Eresus_Auth->expects($this->any())->method('getUser')->will($this->returnValue($user));
		Eresus_Tests::setStatic('Eresus_Auth', $Eresus_Auth);

		$ui->process();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_UI_Admin::process
	 * @covers Eresus_CMS_UI_Admin::auth
	 * @covers Eresus_CMS_UI_Admin::getAuthScreen
	 */
	public function test_process_auth_failed()
	{
		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT);
		$ui = new Eresus_CMS_UI_Admin();

		$user = new stdClass;
		$p_user = new ReflectionProperty('Eresus_Auth', 'user');
		$p_user->setAccessible(true);
		$auth = Eresus_Auth::getInstance();
		$p_user->setValue($auth, $user);
		$user->access = 0;

		$post = $this->getMock('stdClass', array('get'));
		$post->expects($this->any())->method('get')->will($this->returnCallback(
			function ($key)
			{
				static $values = array(null, 'user', 'pass', true);
				return next($values);
			}
		));

		$request = $this->getMock('stdClass', array('isPOST', 'getPost', 'getRootPrefix', 'getHeader'));
		$request->expects($this->any())->method('isPOST')->will($this->returnValue(true));
		$request->expects($this->any())->method('getPost')->will($this->returnValue($post));
		Eresus_Tests::setStatic('Eresus_CMS_Request', $request);

		$table = $this->getMock('stdClass', array('findByUsername'));

		$doctrine = $this->getMock('stdClass', array('getTable'));
		$doctrine->expects($this->any())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($doctrine);

		$Eresus_Auth = $this->getMock('stdClass', array('login', 'getUser', 'setCookies'));
		$Eresus_Auth->expects($this->any())->method('login')->with('user', 'pass')->
			will($this->returnValue(Eresus_Auth::BAD_PASSWORD));
		$Eresus_Auth->expects($this->any())->method('getUser')->will($this->returnValue($user));
		Eresus_Tests::setStatic('Eresus_Auth', $Eresus_Auth);

		$ui->process();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_UI_Admin::process
	 * @covers Eresus_CMS_UI_Admin::main
	 */
	public function test_process_main()
	{
		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT);

		$Eresus_Security = $this->getMock('stdClass', array('isGranted'));
		$Eresus_Security->expects($this->any())->method('isGranted')->
			will($this->returnValue(true));
		Eresus_Tests::setStatic('Eresus_Security', $Eresus_Security);

		$request = $this->getMock('stdClass', array('getRootPrefix', 'getBasePath', 'getNextParam'));
		$request->expects($this->any())->method('getRootPrefix')->will($this->returnValue(''));
		$request->expects($this->any())->method('getBasePath')->will($this->returnValue(''));
		$request->expects($this->any())->method('getNextParam')->will($this->returnValue(false));
		Eresus_Tests::setStatic('Eresus_CMS_Request', $request);

		$ui = new Eresus_CMS_UI_Admin();
		$response = $ui->process();
		$this->assertEquals(Eresus_CMS_Response::NOT_FOUND, $response->getCode());

		$request = $this->getMock('stdClass', array('getRootPrefix', 'getBasePath', 'getNextParam'));
		$request->expects($this->any())->method('getRootPrefix')->will($this->returnValue(''));
		$request->expects($this->any())->method('getBasePath')->will($this->returnValue(''));
		$request->expects($this->any())->method('getNextParam')->
			will($this->returnValue('test_ok'));
		Eresus_Tests::setStatic('Eresus_CMS_Request', $request);

		$response = $ui->process();
		$this->assertEquals(Eresus_CMS_Response::OK, $response->getCode());

		$request = $this->getMock('stdClass', array('getRootPrefix', 'getBasePath', 'getNextParam'));
		$request->expects($this->any())->method('getRootPrefix')->will($this->returnValue(''));
		$request->expects($this->any())->method('getBasePath')->will($this->returnValue(''));
		$request->expects($this->any())->method('getNextParam')->
			will($this->returnValue('test_forbidden'));
		Eresus_Tests::setStatic('Eresus_CMS_Request', $request);

		$response = $ui->process();
		$this->assertEquals(Eresus_CMS_Response::FORBIDDEN, $response->getCode());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_CMS_UI_Admin::process
	 * @expectedException Eresus_HTTP_Exception_HeadersSent
	 */
	public function test_process_logout()
	{
		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT);

		$Eresus_Security = $this->getMock('stdClass', array('isGranted'));
		$Eresus_Security->expects($this->any())->method('isGranted')->with('ROLE_EDITOR')->
			will($this->returnValue(true));
		Eresus_Tests::setStatic('Eresus_Security', $Eresus_Security);

		$request = $this->getMock('stdClass', array('getRootPrefix', 'getBasePath'));
		$request->expects($this->any())->method('getRootPrefix')->will($this->returnValue(''));
		$request->expects($this->any())->method('getBasePath')->
			will($this->returnValue('/admin/logout'));

		Eresus_Tests::setStatic('Eresus_CMS_Request', $request);

		$Eresus_Auth = $this->getMock('stdClass', array('logout'));
		$Eresus_Auth->expects($this->once())->method('logout');
		Eresus_Tests::setStatic('Eresus_Auth', $Eresus_Auth);

		$ui = new Eresus_CMS_UI_Admin();
		$ui->process();
	}
	//-----------------------------------------------------------------------------

	/* */
}


class Eresus_Admin_Controller_Test_OK
{
	public function execute()
	{
		return 'Content';
	}
	//-----------------------------------------------------------------------------
}

class Eresus_Admin_Controller_Test_Forbidden
{
	public function execute()
	{
		throw new Eresus_CMS_Exception_Forbidden;
	}
	//-----------------------------------------------------------------------------
}
