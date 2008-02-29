<?php
	require 'PHPUnit/Framework.php';
	include_once('../kernel.php');

class KernelTest extends PHPUnit_Framework_TestCase {
 /**
  * Проверяем что объект Eresus создаётся
  */
	function testEresusObjectCreate()
	{
    $this->assertTrue(isset($GLOBALS['Eresus']), 'Global object $Eresus does not exsists');
  }
  //-----------------------------------------------------------------------------
}

?>