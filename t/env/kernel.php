<?php
	require_once('lib_sections.php');
	require_once('WebPage.php');
	require_once('Plugins.php');
	require_once('MySQL.php');

	require_once('../lang/ru.inc');
/*	require_once('../core/lib/sections.php');
	require_once('../core/lib/templates.php');

function arg()
{
	;
}
//-----------------------------------------------------------------------------
function useLib()
{
	;
}
//-----------------------------------------------------------------------------
function fileread()
{
	;
}
//-----------------------------------------------------------------------------
function option()
{
	;
}
//-----------------------------------------------------------------------------
*/
class Eresus {
	var $plugins;
	var $sections;
	var $db;
	var $request;
	function Eresus()
	{
		#$this->plugins = new Plugins();
		#$this->sections = new Sections();
		#$this->db = new MySQL();
	}
	//-----------------------------------------------------------------------------
}

$GLOBALS['Eresus'] = new Eresus();

?>