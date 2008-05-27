<?php
	require_once 'PHPUnit/Framework.php';

class WebPageTest extends PHPUnit_Framework_TestCase {
	static private $ClassName = 'WebPage';
	protected $fixture;
	function setUp()
	{
		global $Eresus;

		if (class_exists(self::$ClassName)) {
			$Eresus->db = new TestDB();
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
		$test = $this->fixture->clientURL(22);

		die("$test\n");

		$this->assertEquals(3, intval($test['id']), "loadPage can't find sections after 'main' one");
	}
	//-----------------------------------------------------------------------------
 /**
	* Test for bug #0000165
	*	http://eresus.ru/tracker/view.php?id=0000165
	* /
	function testMethod_loadPage_ChildOfMain()
	{
		global $Eresus;

		$Eresus->request['params'] = array('main', 'main_child');
		$test = $this->fixture->loadPage();

		$this->assertEquals(22, intval($test['id']), 'loadPage can\'t find child section of a root one');
	}*/
	//-----------------------------------------------------------------------------
}

?>