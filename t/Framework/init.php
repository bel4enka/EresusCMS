<?php
	require_once('lib.php');

	overwrite('main.php', '/cfg/');
	overwrite('settings.inc', '/cfg/');
	overwrite('test_db.php', '/core/lib/');

	require_test('../core/kernel.php');
	require_once('Eresus.class.php');

	require_test('../core/lib/sections.php');
	require_test('../core/classes.php');
	require_test('../core/client.php');
	require_test('../lang/ru.inc');


?>