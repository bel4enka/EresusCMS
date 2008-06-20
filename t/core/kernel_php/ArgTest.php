<?php
/**
 * Test 'arg' function
 */
	require_once 'PHPUnit/Framework.php';

class ArgTest extends PHPUnit_Framework_TestCase {
	function testFunction_exists()
	{
		$this->assertTrue(function_exists('arg'), 'Function does not exsists in "/core/kernel.php"');
	}
	//-----------------------------------------------------------------------------
	function testUnexistent_value()
	{
		$this->assertNull(arg('some_unexistent_arg'), 'Function should return NULL when argument not exsits');
	}
	//-----------------------------------------------------------------------------
}

?>