<?php
	require 'PHPUnit/Framework.php';
	require_once 'env/EresusFake.php';
	include_once('../core/classes.php');

class CoreClassesExtensionsTest extends PHPUnit_Framework_TestCase {
 	var $fixture = null;

	function setUp()
	{
		global $Eresus;

		$Eresus = new EresusFake();
		$Eresus->conf['extensions'] = array(
			'forms' => array(
				'memo_syntax' => array(
					'codepress' => null,
				),
				'html' => array(
					'xinha' => null,
					'tiny_mce' => null,
				),
			),
		);
		$this->fixture = new Extensions();
	}
	//-----------------------------------------------------------------------------
	function tearDown()
	{
		unset($this->fixture);
	}
	//-----------------------------------------------------------------------------
  function testGetName()
  {
  	$this->assertEquals('codepress', $this->fixture->get_name('forms', 'memo_syntax', 'codepress'));
  	$this->assertEquals('codepress', $this->fixture->get_name('forms', 'memo_syntax', null));
  	$this->assertEquals('xinha', $this->fixture->get_name('forms', 'html'));
  	$this->assertFalse($this->fixture->get_name('forms', 'unexistent'));
  }
  //-----------------------------------------------------------------------------
}

?>