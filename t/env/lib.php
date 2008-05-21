<?php
function require_test($filename)
{
	static $included = array();

	$filename = realpath($filename);

	if (!in_array($filename, $included)) {
		$included[] = $filename;
		$code = file_get_contents($filename);
		$code = substr($code, 5, -3);
		$code = preg_replace('/^###cut:start\s.*###cut:end.*$/Ums', '', $code);
		eval($code);
	}
}
//-----------------------------------------------------------------------------
function fake($class)
{
	$filename = "$class.fake.php";
	require_once $filename;
}
//-----------------------------------------------------------------------------
?>