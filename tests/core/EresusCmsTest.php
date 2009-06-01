<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модульные тесты класса EresusCMS
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-${build.year}, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 * @package EresusCMS
 * @subpackage Tests
 *
 * $Id$
 */

require_once 'core/EresusCMS.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class EresusCmsTest extends PHPUnit_Framework_TestCase {

	/**
	 *
	 */
	protected function tearDown()
	{
		Core::testModeUnset('PHP::isCLI');
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function testMainWeb()
	{
		$stub = $this->getMock(
			'EresusCMS',
			array('runWeb', 'runCLI')
		);

		$stub->expects($this->never())
			->method('runCLI');

		$stub->expects($this->once())
			->method('runWeb')
			->will($this->returnValue(0));

		Core::testModeSet('PHP::isCLI', false);
		$this->assertEquals(0, $stub->main());
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function testMainCLI()
	{
		$stub = $this->getMock(
			'EresusCMS',
			array('runWeb', 'runCLI')
		);

		$stub->expects($this->never())
			->method('runWeb');

		$stub->expects($this->once())
			->method('runCLI')
			->will($this->returnValue(0));

		$this->assertEquals(0, $stub->main());
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function testDetectWebRoot()
	{
		$stub = new EresusCmsTest_EresusCMS();

		$_SERVER['DOCUMENT_ROOT'] = realpath(ERESUS_TEST_ROOT . DIRECTORY_SEPARATOR . '..');
		$SUFFIX = substr(ERESUS_TEST_ROOT, strlen($_SERVER['DOCUMENT_ROOT']));
		$stub->test_setRequest(new HttpRequest('http://example.com/'.$SUFFIX.'/path/to/script.cgi'));
		$stub->detectWebRoot();
		$this->assertEquals($SUFFIX, $stub->test_getRequest()->getLocalRoot());
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function testInitRoutes()
	{
		$stub = new EresusCmsTest_EresusCMS;

		$stub->initRoutes();
		$this->assertEquals('Router', get_class($stub->test_getRouter()));
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function testInitWeb()
	{
		$this->markTestSkipped();
		$stub = $this->getMock(
			'EresusCmsTest_EresusCMS',
			array('initRoutes')
		);

		$stub->initWeb();
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 * /
	public function testRunWeb()
	{
		$stub = new EresusCmsTest_EresusCMS();

		$stub->runWeb();
	}
	//-----------------------------------------------------------------------------

	/**/
}


class EresusCmsTest_EresusCMS extends EresusCMS {

	/**
	 *
	 */
	public function test_getRequest()
	{
		return $this->request;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @param HttpRequest $request
	 */
	public function test_setRequest($request)
	{
		$this->request = $request;
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function test_getRouter()
	{
		return $this->router;
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see main/core/EresusCMS#initRoutes()
	 */
	public function initRoutes()
	{
		return parent::initRoutes();
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see main/core/EresusCMS#initWeb()
	 */
	public function initWeb()
	{
		return parent::initWeb();
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see main/core/EresusCMS#detectWebRoot()
	 */
	public function detectWebRoot()
	{
		return parent::detectWebRoot();
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see main/core/EresusCMS#runWeb()
	 */
	public function runWeb()
	{
		return parent::runWeb();
	}
	//-----------------------------------------------------------------------------
}