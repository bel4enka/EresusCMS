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

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_HTTP_RequestTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_HTTP_Request::setLocalRoot
	 * @covers Eresus_HTTP_Request::getLocalRoot
	 * @covers Eresus_HTTP_Request::getLocalPath
	 */
	public function test_localRoot()
	{
		/** @var Eresus_HTTP_Request $request */
		$request = Eresus_HTTP_Request::create('/folder/path/to/file.php', 'GET', array(), array(),
			array(),
			array(
				'HTTP_HOST' => 'example.org',
				'DOCUMENT_ROOT' => '/home/user/public_html',
				'SCRIPT_FILENAME' => '/home/user/public_html/folder/path/to/file.php',
				'REQUEST_URI' => '/folder/path/to/file.php',
				'SCRIPT_NAME' => '/folder/path/to/file.php',
				'PHP_SELF' => '/folder/path/to/file.php',
			));
		$this->assertEquals('/folder/path/to', $request->getLocalPath());

		$request->setLocalRoot('/folder');
		$this->assertEquals('/path/to', $request->getLocalPath());

		$request->setLocalRoot('/folder/');
		$this->assertEquals('/folder', $request->getLocalRoot());
		$this->assertEquals('/path/to', $request->getLocalPath());


		$request = Eresus_HTTP_Request::create('/path/to/file.php', 'GET', array(), array(),
			array(),
			array(
				'HTTP_HOST' => 'example.org',
				'DOCUMENT_ROOT' => '/home/user/public_html',
				'SCRIPT_FILENAME' => '/home/user/public_html/path/to/file.php',
				'REQUEST_URI' => '/path/to/file.php',
				'SCRIPT_NAME' => '/path/to/file.php',
				'PHP_SELF' => '/path/to/file.php',
			));
		$this->assertEquals('/path/to', $request->getLocalPath());
	}
}