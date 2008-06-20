<?php

class EresusFatalException extends Exception {}

function require_test($filename)
{
	static $included = array();

	$filename = realpath($filename);

	if (!in_array($filename, $included)) {

		$tempname = dirname(__FILE__).'/php'.substr($filename, strlen(realpath(dirname(__FILE__).'/../..')));
		$included[] = $filename;
		$code = file_get_contents($filename);
		$code = preg_replace('/^\s*###cut:start\s.*###cut:end.*$/Ums', '', $code);
		file_put_contents($tempname, $code);
		require($tempname);
	}
}
//-----------------------------------------------------------------------------
function FatalError($message)
{
	throw new EresusFatalException(strip_tags($message));
}
//-----------------------------------------------------------------------------
?>