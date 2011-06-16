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
 * $Id: Plugin_Test.php 1628 2011-05-19 18:36:14Z mk $
 */

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/Config.php';
require_once dirname(__FILE__) . '/../../../../main/core/HTTP/Message.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_HTTP_Message_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_HTTP_Message::setType
	 * @covers Eresus_HTTP_Message::getType
	 */
	public function test_getsetType()
	{
		$test = new Eresus_HTTP_Message();

		$test->setType(Eresus_HTTP_Message::TYPE_REQUEST);
		$this->assertEquals(Eresus_HTTP_Message::TYPE_REQUEST, $test->getType());

		$test->setType(Eresus_HTTP_Message::TYPE_RESPONSE);
		$this->assertEquals(Eresus_HTTP_Message::TYPE_RESPONSE, $test->getType());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Message::setHttpVersion
	 * @covers Eresus_HTTP_Message::getHttpVersion
	 */
	public function test_getsetHttpVersion()
	{
		$test = new Eresus_HTTP_Message();

		$this->assertTrue($test->setHttpVersion('1.0'));
		$this->assertEquals('1.0', $test->getHttpVersion());

		$this->assertTrue($test->setHttpVersion('1.1'));
		$this->assertEquals('1.1', $test->getHttpVersion());

		$this->assertFalse($test->setHttpVersion('1.2'));
		$this->assertEquals('1.1', $test->getHttpVersion());

	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Message::setRequestMethod
	 * @covers Eresus_HTTP_Message::getRequestMethod
	 */
	public function test_setfetRequestMethod()
	{
		$test = new Eresus_HTTP_Message();

		$this->assertFalse($test->setRequestMethod('GET'));

		$test->setType(Eresus_HTTP_Message::TYPE_REQUEST);

		$this->assertTrue($test->setRequestMethod('GET'), 'GET');
		$this->assertEquals('GET', $test->getRequestMethod());

		$this->assertTrue($test->setRequestMethod('POST'), 'POST');
		$this->assertEquals('POST', $test->getRequestMethod());

		$this->assertFalse($test->setRequestMethod('XXX'), 'XXX');
		$this->assertEquals('POST', $test->getRequestMethod());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Message::setRequestUrl
	 * @covers Eresus_HTTP_Message::getRequestUrl
	 */
	public function test_setfetRequestUrl()
	{
		$test = new Eresus_HTTP_Message();

		$this->assertFalse($test->setRequestUrl('http://example.org/'));

		$test->setType(Eresus_HTTP_Message::TYPE_REQUEST);

		$this->assertTrue($test->setRequestUrl('http://example.org/'));
		$this->assertEquals('http://example.org/', $test->getRequestUrl());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Message::fromEnv
	 */
	public function test_fromEnv()
	{
		$_SERVER['HTTPS'] = 'on';
		$_SERVER['HTTP_HOST'] = 'example.org';
		$_SERVER['REQUEST_URI'] = '/dir1/dir2/file.ext?p1=v1&p2=v2';

		$test = Eresus_HTTP_Message::fromEnv(Eresus_HTTP_Message::TYPE_REQUEST);

		$this->assertEquals(Eresus_HTTP_Message::TYPE_REQUEST, $test->getType());
		$this->assertEquals('GET', $test->getRequestMethod());
		$this->assertEquals('1.0', $test->getHttpVersion());
		$this->assertEquals('https://example.org/dir1/dir2/file.ext?p1=v1&p2=v2',
			$test->getRequestUrl());

	}
	//-----------------------------------------------------------------------------
	/* */
}
