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

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/WebServer.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_WebServer_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_WebServer::__construct
	 * @covers Eresus_WebServer::getInstance
	 * @covers Eresus_WebServer::getDocumentRoot
	 */
	public function test_getDocumentRoot()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}
		$instnce = new ReflectionProperty('Eresus_WebServer', 'instance');
		$instnce->setAccessible(true);
		$instnce->setValue('Eresus_WebServer', null);

		$dir = dirname(__FILE__);
		$_SERVER['DOCUMENT_ROOT'] = $dir;

		$server = Eresus_WebServer::getInstance();
		$this->assertEquals($dir, $server->getDocumentRoot());
	}
	//-----------------------------------------------------------------------------

	/* */
}
