<?php
	require_once 'PHPUnit/Framework.php';

class WebPageTest extends PHPUnit_Framework_TestCase {
	static private $ClassName = 'WebPage';
	protected $fixture;
	function setUp()
	{
		global $Eresus;

		if (class_exists(self::$ClassName)) {
			$this->fixture = new WebPage();
		}
	}
	//-----------------------------------------------------------------------------
	function tearDown()
	{
		unset($this->fixture);
	}
	//-----------------------------------------------------------------------------
	function testClass_presense()
	{
		$this->assertTrue(class_exists(self::$ClassName), 'Class does not exsists');
	}
	//-----------------------------------------------------------------------------
 /**
	* Test for bug #0000168
	*	http://eresus.ru/tracker/view.php?id=0000168
	*/
	function testMethod_clientURL_ordering()
	{
		$expect = 'http://example.org/tmp/services/delivery/level3/level4/';

		$this->assertEquals($expect, $this->fixture->clientURL(24), self::$ClassName."::clientURL: invalid order of page names");
	}
	//-----------------------------------------------------------------------------
	/**/
}

?>