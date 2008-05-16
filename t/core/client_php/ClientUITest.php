<?php
	require_once 'PHPUnit/Framework.php';

	$php = file_get_contents('../core/client.php');
	$php = substr($php, 5, -3);
	$php = str_replace("dirname(__FILE__).DIRECTORY_SEPARATOR.'", "'../t/env/", $php);
	$php = preg_replace('/\$page = new TClientUI.*$/s', '', $php);
	eval($php);

class ClientUITest extends PHPUnit_Framework_TestCase {
	static private $ClassName = 'TClientUI';
	protected $fixture;
	function setUp()
	{
		global $Eresus;

		if (class_exists(self::$ClassName)) {
			$Eresus->sections = new Sections_fake();
			$Eresus->plugins = new Plugins_fake();
			$this->fixture = new TClientUI();
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
	* Test for bug #0000151
	*	http://eresus.ru/tracker/view.php?id=0000151
	*/
	function testMethod_loadPage_MainNotFirst()
	{
		global $Eresus;

		$Eresus->request['params'] = array('services');
		$test = $this->fixture->loadPage();

		$this->assertEquals(3, intval($test['id']), "loadPage can't find sections after 'main' one");
	}
	//-----------------------------------------------------------------------------
 /**
	* Test for bug #0000165
	*	http://eresus.ru/tracker/view.php?id=0000165
	*/
	function testMethod_loadPage_ChildOfMain()
	{
		global $Eresus;

		$Eresus->request['params'] = array('main', 'main_child');
		$test = $this->fixture->loadPage();

		$this->assertEquals(22, intval($test['id']), 'loadPage can\'t find child section of a root one');
	}
	//-----------------------------------------------------------------------------
}

?>