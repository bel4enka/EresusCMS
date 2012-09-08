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
	 * @covers Eresus_HTTP_Request::getLocalUrl
	 */
	public function test_localRoot()
	{
		/** @var Eresus_HTTP_Request $request */
		$request = Eresus_HTTP_Request::create('/example.com/admin.php', 'GET');
		$this->assertEquals('/example.com/admin.php', $request->getLocalUrl());

		$request = Eresus_HTTP_Request::create('/example.com/path/file.php', 'GET', array(), array(),
			array(), array (
				'SCRIPT_FILENAME' => '/home/user/public_html/example.com/index.php',
				'SCRIPT_NAME' => '/example.com/index.php',
			));
		$this->assertEquals('/path/file.php', $request->getLocalUrl());
	}

	/**
	 * @covers Eresus_HTTP_Request::getFilename
	 */
	public function test_getFilename()
	{
		/** @var Eresus_HTTP_Request $request */
		$request = Eresus_HTTP_Request::create('/example.com/admin.php', 'GET');
		$this->assertEquals('admin.php', $request->getFilename());
	}
}