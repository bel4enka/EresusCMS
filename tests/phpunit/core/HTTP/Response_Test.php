<?php
/**
 * ${product.title}
 *
 * Модульные тесты
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
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/HTTP/Response.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_HTTP_Response_Test extends PHPUnit_Extensions_OutputTestCase
{
	/**
	 * @covers Eresus_HTTP_Response::__construct
	 */
	public function test_construct()
	{
		$o = new stdClass();
		$test = new Eresus_HTTP_Response($o);
		$p_body = new ReflectionProperty('Eresus_HTTP_Response', 'body');
		$p_body->setAccessible(true);
		$this->assertSame($o, $p_body->getValue($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Response::setBody
	 */
	public function test_setBody()
	{
		$o = new stdClass();
		$test = new Eresus_HTTP_Response();
		$test->setBody($o);
		$p_body = new ReflectionProperty('Eresus_HTTP_Response', 'body');
		$p_body->setAccessible(true);
		$this->assertSame($o, $p_body->getValue($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Response::setStatus
	 */
	public function test_setStatus()
	{
		$test = new Eresus_HTTP_Response();
		$test->setStatus(200);
		$p_status = new ReflectionProperty('Eresus_HTTP_Response', 'status');
		$p_status->setAccessible(true);
		$this->assertEquals(200, $p_status->getValue($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Response::getStatusMessage
	 */
	public function test_getStatusMessage()
	{
		$this->assertEquals('Continue',
			Eresus_HTTP_Response::getStatusMessage(Eresus_HTTP_Response::ST_CONTINUE));
		$this->assertEquals('', Eresus_HTTP_Response::getStatusMessage(0));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Response::send
	 * @covers Eresus_HTTP_Response::setHeader
	 */
	public function test_send()
	{
		$test = new Eresus_HTTP_Response('test');
		$test->setStatus(Eresus_HTTP_Response::ST_OK);
		$test->setHeader('X-Test', 'test');
		$test->send();
		$this->expectOutputString('test');
	}
	//-----------------------------------------------------------------------------

	/* */
}
