<?php
/**
 * Test Eresus::init method
 */
require_once 'PHPUnit/Framework.php';

class EresusInitTest extends PHPUnit_Framework_TestCase {
	function testInit()
	{
		global $Eresus;

		load_server('Apache', 'basic');
		$this->assertNull($Eresus->init());
	}
	//-----------------------------------------------------------------------------
	function testMainConfig()
	{
		global $Eresus;

		$this->assertEquals('test_db', $Eresus->conf['db']['engine'], 'Configuration from "main.php" not loaded.');
	}
	//-----------------------------------------------------------------------------
	function testSectionsCreated()
	{
		global $Eresus;

		$this->assertTrue(is_object($Eresus->sections));
	}
	//-----------------------------------------------------------------------------
	function testSectionsClass()
	{
		global $Eresus;

		$this->assertEquals('Sections', get_class($Eresus->sections));
	}
	//-----------------------------------------------------------------------------
	/**
	 * BUG #0000200
	 * Ошибка внесена в ревизии 309: http://dev.procreat.ru/svnview/eresus2?view=rev&revision=309
	 * Если в URI не указано имя файла, то свойство $Eresus->request['file'] устанавливается в false.
	 * Это приводит к потере значения $Eresus->request['path'] при следующей опреациии.
	 */
	function testFor_PathLost()
	{
		global $Eresus;

		$s = $_SERVER['REQUEST_URI'];
		$_SERVER['REQUEST_URI'] = '/path/';
		$Eresus->init_request();

		$this->assertEquals('http://example.org/path/', $Eresus->request['path'], 'Lost "path" member of an "Eresus::request" property');

		$_SERVER['REQUEST_URI'] = $s;
		$Eresus->init_request();
	}
	//-----------------------------------------------------------------------------
	/**/
}
