<?php
if (!defined('PHPUnit_MAIN_METHOD')) define('PHPUnit_MAIN_METHOD', 'Core_Lib_AllTests::main');

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'sections/AllTests.php';


class Core_Lib_AllTests
{
		public static function main()
		{
				PHPUnit_TextUI_TestRunner::run(self::suite());
		}

		public static function suite()
		{
				$suite = new PHPUnit_Framework_TestSuite('PHPUnit');

				$suite->addTest(Core_Lib_Sections_AllTests::suite());

				return $suite;
		}
}

if (PHPUnit_MAIN_METHOD == 'Core_Lib_AllTests::main') Core_Lib_AllTests::main();

?>