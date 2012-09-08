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

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMSTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_CMS::detectWebRoot
	 */
	public function test_detectWebRoot()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		/* Подменяем DOCUMENT_ROOT */
		$webServer = Eresus_WebServer::getInstance();
		$documentRoot = new ReflectionProperty('Eresus_WebServer', 'documentRoot');
		$documentRoot->setAccessible(true);
		$documentRoot->setValue($webServer, '/home/user/public_html');

		$obj = new Eresus_CMS;
		// Подменяем результат getFsRoot
		$obj->fsRoot = '/home/user/public_html';
		$httpRequest = new HttpRequest();

		$request = new ReflectionProperty('Eresus_CMS', 'request');
		$request->setAccessible(true);
		$request->setValue($obj, $httpRequest);

		$detectWebRoot = new ReflectionMethod('Eresus_CMS', 'detectWebRoot');
		$detectWebRoot->setAccessible(true);
		$detectWebRoot->invoke($obj);

		$this->assertEquals('', $httpRequest->localRoot);
	}

	/**
	 * @covers Eresus_CMS::detectWebRoot
	 */
	public function test_detectWebRoot_notRoot()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		/* Подменяем DOCUMENT_ROOT */
		$webServer = Eresus_WebServer::getInstance();
		$documentRoot = new ReflectionProperty('Eresus_WebServer', 'documentRoot');
		$documentRoot->setAccessible(true);
		$documentRoot->setValue($webServer, '/home/user/public_html');

		$obj = new Eresus_CMS;
		// Подменяем результат getFsRoot
		$obj->fsRoot = '/home/user/public_html/example.org';
		$httpRequest = new HttpRequest();

		$request = new ReflectionProperty('Eresus_CMS', 'request');
		$request->setAccessible(true);
		$request->setValue($obj, $httpRequest);

		$detectWebRoot = new ReflectionMethod('Eresus_CMS', 'detectWebRoot');
		$detectWebRoot->setAccessible(true);
		$detectWebRoot->invoke($obj);

		$this->assertEquals('/example.org', $httpRequest->localRoot);
	}

	/**
	 * @covers Eresus_CMS::detectWebRoot
	 */
	public function test_detectWebRoot_windows()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		/* Подменяем DOCUMENT_ROOT */
		$webServer = Eresus_WebServer::getInstance();
		$documentRoot = new ReflectionProperty('Eresus_WebServer', 'documentRoot');
		$documentRoot->setAccessible(true);
		$documentRoot->setValue($webServer, FS::canonicalForm('C:\Program Files\Apache Webserver\docs'));

		$obj = new Eresus_CMS;
		// Подменяем результат getFsRoot
		$obj->fsRoot = FS::canonicalForm('C:\Program Files\Apache Webserver\docs\example.org');
		$httpRequest = new HttpRequest();

		$request = new ReflectionProperty('Eresus_CMS', 'request');
		$request->setAccessible(true);
		$request->setValue($obj, $httpRequest);

		$detectWebRoot = new ReflectionMethod('Eresus_CMS', 'detectWebRoot');
		$detectWebRoot->setAccessible(true);
		$detectWebRoot->invoke($obj);

		$this->assertEquals('/example.org', $httpRequest->localRoot);
	}

	/**
	 * @covers Eresus_CMS::getPage
	 */
	public function test__getPage()
	{	
		$p_page = new ReflectionProperty("Eresus_CMS", "page");
		$p_page->setAccessible(true);
		
		$eresus = new Eresus_CMS();
		$p_page->setValue($eresus,'foo');
		
		$this->assertEquals('foo', $eresus->getPage());
	}

	/**
	 * @covers Eresus_CMS::runWeb
	 */
	public function test_runWeb()
	{
		$runWeb = new ReflectionMethod('Eresus_CMS', 'runWeb');
		$runWeb->setAccessible(true);

		$request = new ReflectionProperty('Eresus_CMS', 'request');
		$request->setAccessible(true);

		$cms = $this->getMock('Eresus_CMS',
			array('initWeb', 'call3rdPartyExtension', 'runWebAdminUI', 'runWebClientUI'));
		$cms->expects($this->once())->method('call3rdPartyExtension');
		$cms->expects($this->once())->method('runWebAdminUI');
		$cms->expects($this->once())->method('runWebClientUI');

		/* call3rdPartyExtension */
		$requestMock = $this->getMock('stdClass', array('getLocal'));
		$requestMock->expects($this->any())->method('getLocal')->will($this->returnValue('/ext-3rd'));
		$request->setValue($cms, $requestMock);
		$runWeb->invoke($cms);

		/* runWebAdminUI */
		$requestMock = $this->getMock('stdClass', array('getLocal'));
		$requestMock->expects($this->any())->method('getLocal')->will($this->returnValue('/admin'));
		$request->setValue($cms, $requestMock);
		$runWeb->invoke($cms);

		/* runWebClientUI */
		$requestMock = $this->getMock('stdClass', array('getLocal'));
		$requestMock->expects($this->any())->method('getLocal')->will($this->returnValue('/'));
		$request->setValue($cms, $requestMock);
		$runWeb->invoke($cms);
	}
}
