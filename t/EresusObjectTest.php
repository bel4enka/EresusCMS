<?php
	require 'PHPUnit/Framework.php';
	require_once 'env/server.php';
	include_once('../core/kernel.php');

class EresusObjectTest extends PHPUnit_Framework_TestCase {
 /**
  * Тестируемый объект
  *
  * @var Eresus
  */
	var $fixture = null;
 /**
  * Конструктор
  *
  * @return KernelTest
  */
	function EresusObjectTest()
	{
		$this->fixture = isset($GLOBALS['Eresus']) ? $GLOBALS['Eresus'] : null;
	}
	//-----------------------------------------------------------------------------
 /**
  * Проверяем что объект Eresus создаётся
  */
	function testEresusObjectCreate()
	{
    $this->assertTrue(isset($GLOBALS['Eresus']), 'Global object $Eresus does not exsists');
  }
  //-----------------------------------------------------------------------------
  function testPropertyExtensions()
  {
  	$this->assertEquals('EresusExtensions', get_class($this->fixture->extensions), '$Eresus->extensions has invalid class');
  }
  //-----------------------------------------------------------------------------
  function testPropertyDb()
  {
  	$this->assertEquals($this->fixture->conf['db']['engine'], strtolower(get_class($this->fixture->db)), '$Eresus->db has invalid type');
  }
  //-----------------------------------------------------------------------------
  function testPropertyPlugins()
  {
  	$this->assertEquals('Plugins', get_class($this->fixture->plugins), '$Eresus->plugins has invalid type');
  }
  //-----------------------------------------------------------------------------
  function testPropertyHost()
  {
  	$this->assertEquals('example.org', $this->fixture->host, '$Eresus->host has invalid value');
  }
  //-----------------------------------------------------------------------------
  function testPropertyPath()
  {
  	$this->assertEquals('/', $this->fixture->path, '$Eresus->path has invalid value');
  }
  //-----------------------------------------------------------------------------
  function testPropertyRoot()
  {
  	$this->assertEquals('http://example.org/', $this->fixture->root, '$Eresus->root has invalid value');
  }
  //-----------------------------------------------------------------------------
  function testPropertyData()
  {
  	$this->assertEquals('http://example.org/data/', $this->fixture->data, '$Eresus->data has invalid value');
  }
  //-----------------------------------------------------------------------------
  function testPropertyStyle()
  {
  	$this->assertEquals('http://example.org/style/', $this->fixture->style, '$Eresus->style has invalid value');
  }
  //-----------------------------------------------------------------------------
  function testPropertyFroot()
  {
  	$this->assertEquals(realpath(dirname(__FILE__).'/..').'/', $this->fixture->froot, '$Eresus->froot has invalid value');
  }
  //-----------------------------------------------------------------------------
  function testPropertyFdata()
  {
  	$this->assertEquals(realpath(dirname(__FILE__).'/..').'/data/', $this->fixture->fdata, '$Eresus->fdata has invalid value');
  }
  //-----------------------------------------------------------------------------
  function testPropertyFstyle()
  {
  	$this->assertEquals(realpath(dirname(__FILE__).'/..').'/style/', $this->fixture->fstyle, '$Eresus->fstyle has invalid value');
  }
  //-----------------------------------------------------------------------------
  function testPasswordHash()
  {
  	$this->fixture->conf['backward']['weak_password'] = false;
 		$this->assertEquals(md5(md5('test')), $this->fixture->password_hash('test'), '$Eresus->password_hash returns invalid hash');

  	$this->fixture->conf['backward']['weak_password'] = true;
 		$this->assertEquals(md5('test'), $this->fixture->password_hash('test'), '$Eresus->password_hash returns invalid hash in a "weak_password" mode');
  }
  //-----------------------------------------------------------------------------
}

?>