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

require_once dirname(__FILE__) . '/../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/Config.php';
require_once TESTS_SRC_DIR . '/core/Kernel.php';
require_once TESTS_SRC_DIR . '/core/Logger.php';

/**
 * @package Eresus
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
		Eresus_Config::set('eresus.cms.log.level', LOG_ERR);
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


