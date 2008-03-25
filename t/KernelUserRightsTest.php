<?php
	require 'PHPUnit/Framework.php';
	require_once 'env/server.php';
	include_once('../core/kernel.php');

class KernelUserRightsTest extends PHPUnit_Framework_TestCase {
  function testAuthFlagClear()
  {
  	global $Eresus;

  	$Eresus->user['auth'] = false;
  	$this->assertTrue(UserRights(GUEST), 'Test GUEST');
  	$this->assertFalse(UserRights(USER), 'Test USER');
  	$this->assertFalse(UserRights(EDITOR), 'Test EDITOR');
  	$this->assertFalse(UserRights(ADMIN), 'Test ADMIN');
  	$this->assertFalse(UserRights(ROOT), 'Test ROOT');
  }
  //-----------------------------------------------------------------------------
  function testAuthFlagSet()
  {
  	global $Eresus;

  	$Eresus->user['auth'] = true;
  	$this->assertTrue(UserRights(GUEST), 'Test GUEST');
  	$this->assertFalse(UserRights(USER), 'Test USER');
  	$this->assertFalse(UserRights(EDITOR), 'Test EDITOR');
  	$this->assertFalse(UserRights(ADMIN), 'Test ADMIN');
  	$this->assertFalse(UserRights(ROOT), 'Test ROOT');
  }
  //-----------------------------------------------------------------------------
  function testLevelGuest()
  {
  	global $Eresus;

  	$this->assertTrue(UserRights(GUEST), 'Test GUEST');
  	$Eresus->user['access'] = USER;
  	$this->assertTrue(UserRights(GUEST), 'Test USER');
  	$Eresus->user['access'] = EDITOR;
  	$this->assertTrue(UserRights(GUEST), 'Test EDITOR');
   	$Eresus->user['access'] = ADMIN;
  	$this->assertTrue(UserRights(GUEST), 'Test ADMIN');
   	$Eresus->user['access'] = ROOT;
  	$this->assertTrue(UserRights(GUEST), 'Test ROOT');
  }
  //-----------------------------------------------------------------------------
  function testLevelUser()
  {
  	global $Eresus;

  	$Eresus->user['auth'] = true;
  	$Eresus->user['access'] = GUEST;
  	$this->assertFalse(UserRights(USER), 'Test GUEST');
  	$Eresus->user['access'] = USER;
  	$this->assertTrue(UserRights(USER), 'Test USER');
  	$Eresus->user['access'] = EDITOR;
  	$this->assertTrue(UserRights(USER), 'Test EDITOR');
   	$Eresus->user['access'] = ADMIN;
  	$this->assertTrue(UserRights(USER), 'Test ADMIN');
   	$Eresus->user['access'] = ROOT;
  	$this->assertTrue(UserRights(USER), 'Test ROOT');
  }
  //-----------------------------------------------------------------------------
  function testLevelEditor()
  {
  	global $Eresus;

  	$Eresus->user['auth'] = true;
  	$Eresus->user['access'] = GUEST;
  	$this->assertFalse(UserRights(EDITOR), 'Test GUEST');
  	$Eresus->user['access'] = USER;
  	$this->assertFalse(UserRights(EDITOR), 'Test USER');
  	$Eresus->user['access'] = EDITOR;
  	$this->assertTrue(UserRights(EDITOR), 'Test EDITOR');
   	$Eresus->user['access'] = ADMIN;
  	$this->assertTrue(UserRights(EDITOR), 'Test ADMIN');
   	$Eresus->user['access'] = ROOT;
  	$this->assertTrue(UserRights(EDITOR), 'Test ROOT');
  }
  //-----------------------------------------------------------------------------
  function testLevelAdmin()
  {
  	global $Eresus;

  	$Eresus->user['auth'] = true;
  	$Eresus->user['access'] = GUEST;
  	$this->assertFalse(UserRights(ADMIN), 'Test GUEST');
  	$Eresus->user['access'] = USER;
  	$this->assertFalse(UserRights(ADMIN), 'Test USER');
  	$Eresus->user['access'] = EDITOR;
  	$this->assertFalse(UserRights(ADMIN), 'Test EDITOR');
   	$Eresus->user['access'] = ADMIN;
  	$this->assertTrue(UserRights(ADMIN), 'Test ADMIN');
   	$Eresus->user['access'] = ROOT;
  	$this->assertTrue(UserRights(ADMIN), 'Test ROOT');
  }
  //-----------------------------------------------------------------------------
  function testLevelRoot()
  {
  	global $Eresus;

  	$Eresus->user['auth'] = true;
  	$Eresus->user['access'] = GUEST;
  	$this->assertFalse(UserRights(ROOT), 'Test GUEST');
  	$Eresus->user['access'] = USER;
  	$this->assertFalse(UserRights(ROOT), 'Test USER');
  	$Eresus->user['access'] = EDITOR;
  	$this->assertFalse(UserRights(ROOT), 'Test EDITOR');
   	$Eresus->user['access'] = ADMIN;
  	$this->assertFalse(UserRights(ROOT), 'Test ADMIN');
   	$Eresus->user['access'] = ROOT;
  	$this->assertTrue(UserRights(ROOT), 'Test ROOT');
  }
  //-----------------------------------------------------------------------------
}

?>