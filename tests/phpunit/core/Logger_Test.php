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
 * @package Kernel
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/Logger.php';

/**
 * @package Kernel
 * @subpackage Tests
 */
class Eresus_Logger_Test extends PHPUnit_Extensions_OutputTestCase
{
	/**
	 * Log filename saver
	 *
	 * @var string
	 */
	protected $logSaver;

	/**
	 * Temporary log filename
	 *
	 * @var string
	 */
	protected $logFilename;

	/**
	 *
	 */
	protected function setUp()
	{
		$this->logSaver = ini_get('error_log');
		$TMP = isset($_ENV['TMP']) ? $_ENV['TMP'] : '/tmp';
		$this->logFilename = tempnam($TMP, 'eresus-core-');
		ini_set('error_log', $this->logFilename);
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	protected function tearDown()
	{
		@unlink($this->logFilename);
		ini_set('error_log', $this->logSaver);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Logger::sender2string
	 */
	public function test_sender2string()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('This test requires at PHP 5.3.2 or higher');
		}

		$method = new ReflectionMethod('Eresus_Logger', 'sender2string');
		$method->setAccessible(true);

		$this->assertEquals('MyClass::myMethod', $method->invoke(null, 'MyClass::myMethod'));
		$this->assertEquals('MyClass1/MyClass2', $method->invoke(null, array('MyClass1', 'MyClass2')));
		$this->assertEquals('unknown', $method->invoke(null, ''));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Logger::exception2string
	 */
	public function test_exception2string()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('This test requires at PHP 5.3.2 or higher');
		}

		$method = new ReflectionMethod('Eresus_Logger', 'exception2string');
		$method->setAccessible(true);

		$stub = new Exception('Some message', 123);
		$test = $method->invoke(null, $stub);
		$this->assertTrue(strpos($test, 'Some message') !== false,
			"METHOD CALL RESULT:\n" . $test);

		$stub = new Exception('Message 1', 0, new Exception('Message 2'));

		$s = $method->invoke(null, $stub);
		$msg1 = strpos($s, 'Message 1') !== false;
		$msg2 = strpos($s, 'Message 2') !== false;

		$this->assertTrue($msg1 && $msg2, $s);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Logger::substitute
	 */
	public function test_substitute()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('This test requires at PHP 5.3.2 or higher');
		}

		$method = new ReflectionMethod('Eresus_Logger', 'substitute');
		$method->setAccessible(true);

		$this->assertEquals('123 test',
			$method->invoke(null, '%d %s', array(null, null, null, 123, 'test')));
		$this->assertEquals('stdClass',
			$method->invoke(null, '%s', array(null, null, null, new stdClass())));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Logger::log
	 */
	public function test_LOG_EMERG()
	{
		Eresus_Logger::log('some_function', LOG_EMERG, 'message');
		$message = file_get_contents($this->logFilename);
		$test = (Eresus_Kernel::isWindows() ? '[critical]' : '[PANIC]') . ' some_function: message';
		$this->assertTrue(strpos($message, $test) !== false, "Invalid message: '$message'");
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Logger::log
	 */
	public function test_unknownSender()
	{
		Eresus_Logger::log('some_function', -1, 'message');
		$message = file_get_contents($this->logFilename);
		$this->assertTrue(strpos($message, '[unknown] some_function: message') !== false,
			"Invalid message: '$message'");
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Logger::log
	 */
	public function test_LOG_DEBUG()
	{
		Eresus_Logger::log('some_function', LOG_DEBUG, 'message');
		$message = file_get_contents($this->logFilename);
		$this->assertEquals('', $message);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Logger::exception
	 */
	public function test_exception()
	{
		Eresus_Logger::exception(new Exception(), 'message');
		$message = file_get_contents($this->logFilename);
		$this->assertTrue(strpos($message, 'Logger: Exception') !== false, "Invalid message: '$message'");
	}
	//-----------------------------------------------------------------------------

	/* */
}


