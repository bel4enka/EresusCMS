<?php
	require_once('lib.php');

	overwrite('main.php', '/cfg/');
	overwrite('settings.php', '/cfg/');
	overwrite('test_db.php', '/core/lib/');

	define('ERESUS_CODE_ROOT', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'));

	require_test(ERESUS_CODE_ROOT.'/core/kernel.php');
	require_once('Eresus.class.php');

	require_test(ERESUS_CODE_ROOT.'/core/lib/sections.php');
	require_test(ERESUS_CODE_ROOT.'/core/classes.php');
	require_test(ERESUS_CODE_ROOT.'/core/client.php');
	require_test(ERESUS_CODE_ROOT.'/lang/ru.php');
