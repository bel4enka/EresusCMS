<?php
	require 'PHPUnit/Framework.php';
	require_once 'env/server.php';
	require_once 'env/EresusFake.php';
	include_once('../core/classes.php');

class CoreClassesPluginTest extends PHPUnit_Framework_TestCase {
 	var $fixture = null;

	function setUp()
	{
		global $Eresus;

		$Eresus = new EresusFake();
		/*$Eresus->plugins->list['plugin']['settings'] = array(
			'key' => 'value',
		);
		*/
		$this->fixture = new Plugin;
	}
	//-----------------------------------------------------------------------------
	function tearDown()
	{
		unset($this->fixture);
	}
	//-----------------------------------------------------------------------------
  function testName()
  {
  	$this->assertEquals('plugin', $this->fixture->name);
  }
  //-----------------------------------------------------------------------------
}

?>