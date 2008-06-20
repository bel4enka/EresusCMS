<?php

class EresusTestClass extends Eresus {
	function FatalError()
	{
		;
	}
	//-----------------------------------------------------------------------------
}

$GLOBALS['Eresus'] = new EresusTestClass();

?>