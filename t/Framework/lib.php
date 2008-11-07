<?php

function require_test($filename)
{
	static $included = array();

	$fullname = realpath($filename);

	if (!$fullname) die("File '$filename' not found\n");

	if (!in_array($fullname, $included)) {

		$tempname = dirname(__FILE__).'/../tmp'.substr($fullname, strlen(realpath(dirname(__FILE__).'/../..')));

		$included[] = $fullname;
		$code = file_get_contents($fullname);
		$code = preg_replace('/^\s*###cut:start\s.*###cut:end.*$/Ums', '', $code);
		if (!is_dir(dirname($tempname))) mkdir(dirname($tempname), 0777, true);
		file_put_contents($tempname, $code);
		require($tempname);
	}
}
//-----------------------------------------------------------------------------
function overwrite($file, $path)
{
	$target = dirname(__FILE__).'/../tmp'.$path;
	@mkdir($target, 0777, true);
	$target .= $file;
	@unlink($target);
	copy(dirname(__FILE__).'/overwrite/'.$file, $target);
}
//-----------------------------------------------------------------------------

class EresusFatalException extends Exception {}


function FatalError($message)
{
	$message = iconv('cp1251', 'utf8', $message);
	throw new EresusFatalException(strip_tags($message));
}
//-----------------------------------------------------------------------------

$GLOBALS['__SERVERS'] = array(
	'Apache' => array(
		'basic' => array(
			'REQUEST_METHOD' => 'GET',
			'HTTP_HOST' => 'example.org',
			'REQUEST_URI' => '/first_dir/second_dir/some_file.ext?key1=val1&key2=val2',
		),
	),
);

function load_server($server, $suite)
{
	$_SERVER = $GLOBALS['__SERVERS'][$server][$suite];
}
//-----------------------------------------------------------------------------