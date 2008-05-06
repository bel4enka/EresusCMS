<?php
	require_once 'PHPUnit/Framework.php';

#	require_once 'env/kernel.php';
	include_once '../core/lib/sections.php';

class SectionsTest extends PHPUnit_Framework_TestCase {
	static private $ClassName = 'Sections';
	protected $fixture;
	function setUp()
	{
		if (class_exists(self::$ClassName)) {
			$this->fixture = new self::$ClassName;
			$this->fixture->index = array (
				4 => array (
					0 => '21',
				),
				0 => array (
					0 => '3',
					1 => '1',
					2 => '4',
					3 => '5',
					4 => '6',
					5 => '7',
					6 => '2',
					7 => '16',
					8 => '17',
				),
				5 => array (
					0 => '18',
					1 => '19',
					2 => '20',
				),
				3 => array (
					0 => '8',
					1 => '9',
					2 => '10',
					3 => '11',
					4 => '12',
				),
			);
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
	* ѕопытка дл€ несуществующего раздела
	*
	*/
	function testMethod_Parents_unexistend_id()
	{
		$this->assertNull($this->fixture->parents(999));
	}
	//-----------------------------------------------------------------------------
	/*function testMethod_Level_set()
	{
		$this->assertEquals(LOG_DEBUG, $this->fixture->level(LOG_DEBUG), 'New value not set');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Level_invalid()
	{
		$this->fixture->level(LOG_DEBUG);
		$this->assertEquals(LOG_DEBUG, $this->fixture->level(123456), 'Invalid argument not filtered');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Type_default()
	{
		$this->assertEquals(LOG_DEFAULT, $this->fixture->type(), 'Invalid default value');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Type_set()
	{
		$this->assertEquals(LOG_MAIL, $this->fixture->type(LOG_MAIL), 'New value not set');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Type_invalid()
	{
		$this->fixture->type(LOG_MAIL);
		$this->assertEquals(LOG_MAIL, $this->fixture->type(123456), 'Invalid argument not filtered');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Destination_default()
	{
		$this->assertNull($this->fixture->destination(), 'Invalid default value');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Destination_LOG_DEFAULT()
	{
		$this->fixture->type(LOG_DEFAULT);
		$this->assertNull($this->fixture->destination('some-test-value'), '$this->fixture->destination() must ever return NULL when log type is LOG_DEFAULT');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Destination_LOG_FILE()
	{
		$this->fixture->type(LOG_FILE);
		$tempfile = tempnam(sys_get_temp_dir(), 'eresus-core-');
	 	$this->assertEquals($tempfile, $this->fixture->destination($tempfile), 'Temp file name was not set');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Destination_LOG_FILE_isDirectory()
	{
		$this->fixture->type(LOG_FILE);
	 	$this->setExpectedException('EresusCoreException');
	 	$this->fixture->destination('/tmp');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Destination_LOG_FILE_notWritable()
	{
		$this->fixture->type(LOG_FILE);
	 	$this->setExpectedException('EresusCoreException');
	 	$this->fixture->destination('/dev/mem');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Scheme_set()
	{
		$this->assertEquals('TestValue', $this->fixture->scheme(LOG_WARNING, 'TestValue'), 'New value not set');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Scheme_invalid()
	{
		$this->assertNull($this->fixture->scheme(123456, 'TestValue'), 'Invalid argument not filtered');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Includes_set()
	{
		$value = array('value1', 'value2');
		$this->assertEquals($value, $this->fixture->includes($value), 'New value not set');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Includes_notArray()
	{
		$this->assertType('array', $this->fixture->includes(false), 'Invalid argument not filtered');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Excludes_set()
	{
		$value = array('value1', 'value2');
		$this->assertEquals($value, $this->fixture->excludes($value), 'New value not set');
	}
	//-----------------------------------------------------------------------------
	function testMethod_Excludes_notArray()
	{
		$this->assertType('array', $this->fixture->excludes(false), 'Invalid argument not filtered');
	}
	//-----------------------------------------------------------------------------
	function testMethod_isSenderFiltered_includes_1()
	{
		$this->fixture->level(LOG_DEBUG);

		$value = array('/.*test.* /');
		$this->fixture->includes($value);

		$this->assertFalse($this->fixture->isSenderFiltered_public('some-test-value'));
	}
	//-----------------------------------------------------------------------------
	function testMethod_isSenderFiltered_includes_2()
	{
		$this->fixture->level(LOG_DEBUG);

		$value = array('/.*test.* /');
		$this->fixture->includes($value);

		$this->assertTrue($this->fixture->isSenderFiltered_public('invalid-value'));
	}
	//-----------------------------------------------------------------------------
	function testMethod_isSenderFiltered_excludes_1()
	{
		$this->fixture->level(LOG_DEBUG);

		$value = array('/.*test.* /');
		$this->fixture->excludes($value);

		$this->assertTrue($this->fixture->isSenderFiltered_public('some-test-value'));
	}
	//-----------------------------------------------------------------------------
	function testMethod_isSenderFiltered_excludes_2()
	{
		$this->fixture->level(LOG_DEBUG);

		$value = array('/.*test.* /');
		$this->fixture->excludes($value);

		$this->assertFalse($this->fixture->isSenderFiltered_public('valid-value'));
	}
	//-----------------------------------------------------------------------------
	function testMethod_isSenderFiltered_includes_excludes_1()
	{
		$this->fixture->level(LOG_DEBUG);

		$value = array('/.*good.* /');
		$this->fixture->includes($value);
		$value = array('/.*bad.* /');
		$this->fixture->excludes($value);

		$this->assertFalse($this->fixture->isSenderFiltered_public('some-good-value'));
	}
	//-----------------------------------------------------------------------------
	function testMethod_isSenderFiltered_includes_excludes_2()
	{
		$this->fixture->level(LOG_DEBUG);

		$value = array('/.*good.* /');
		$this->fixture->includes($value);
		$value = array('/.*bad.* /');
		$this->fixture->excludes($value);

		$this->assertTrue($this->fixture->isSenderFiltered_public('some-bad-value'));
	}
	//-----------------------------------------------------------------------------
	function testMethod_isSenderFiltered_includes_excludes_3()
	{
		$this->fixture->level(LOG_DEBUG);

		$value = array('/.*good.* /');
		$this->fixture->includes($value);
		$value = array('/.*bad.* /');
		$this->fixture->excludes($value);

		$this->assertTrue($this->fixture->isSenderFiltered_public('not-good-not-bad'));
	}*/
	//-----------------------------------------------------------------------------
}

?>