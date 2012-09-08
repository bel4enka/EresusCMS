<?php
/**
 * ${product.title}
 *
 * Тесты
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
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
 */

require_once TESTS_SRC_DIR . '/core/Eresus/WebServer.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class WebServerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		parent::setUp();

		$instance = new ReflectionProperty('Eresus_WebServer', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('Eresus_WebServer', null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		parent::tearDown();

		$instance = new ReflectionProperty('Eresus_WebServer', 'instance');
		$instance->setAccessible(true);
		$instance->setValue('Eresus_WebServer', null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_WebServer::__construct
	 * @covers Eresus_WebServer::getInstance
	 * @covers Eresus_WebServer::getDocumentRoot
	 */
	public function test_getDocumentRoot()
	{
		$dir = dirname(__FILE__);
		$_SERVER['DOCUMENT_ROOT'] = $dir;
		$server = Eresus_WebServer::getInstance();
		$docRoot = $server->getDocumentRoot();
		// Проверяем наличие прямых слэшей
		$this->assertRegExp('/^.*\/.*$/', $docRoot);
		// realpath необходим под Windows для приведения пути к системному виду
		$this->assertEquals($dir, realpath($docRoot));
	}
	//-----------------------------------------------------------------------------

	/* */
}
