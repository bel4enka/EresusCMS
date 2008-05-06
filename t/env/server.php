<?php

	$_SERVER['REQUEST_METHOD'] = 'POST';
	$_SERVER['HTTP_HOST'] = 'example.org';
	$_SERVER['HTTP_REFERER'] = 'http://example.org/some/referer/url/';
	$_SERVER['REQUEST_URI'] = '/some/virtual/url/script.cgi?arg1=value1&arg2=value2';
	$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/..');

?>