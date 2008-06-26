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
	/**/
}
