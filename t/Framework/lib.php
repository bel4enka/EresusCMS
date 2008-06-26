<?php

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

class EresusFatalException extends Exception {}


function FatalError($message)
{
	throw new EresusFatalException(strip_tags($message));
}
//-----------------------------------------------------------------------------

$GLOBALS['__SERVERS'] = array(
	'Apache' => array(
		'basic' => array(
			'REQUEST_METHOD' => 'GET',
			'HTTP_HOST' => 'example.org',
		),
	),
);

function load_server($server, $suite)
{
	$_SERVER = $GLOBALS['__SERVERS'][$server][$suite];
}
//-----------------------------------------------------------------------------