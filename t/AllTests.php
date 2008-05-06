<?php
if (!defined('PHPUnit_MAIN_METHOD')) define('PHPUnit_MAIN_METHOD', 'AllTests::main');

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'core/AllTests.php';

class AllTests
{
		public static function main()
		{
				PHPUnit_TextUI_TestRunner::run(self::suite());
		}

		public static function suite()
		{
				$suite = new PHPUnit_Framework_TestSuite('PHPUnit');

				$suite->addTest(Core_AllTests::suite());

				return $suite;
		}
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') AllTests::main();

?>