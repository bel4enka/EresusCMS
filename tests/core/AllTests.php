<?php
if (!defined('PHPUnit_MAIN_METHOD')) define('PHPUnit_MAIN_METHOD', 'Core_AllTests::main');

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'kernel_php/AllTests.php';
require_once 'lib/AllTests.php';
require_once 'classes_php/AllTests.php';
require_once 'client_php/AllTests.php';


class Core_AllTests
{
		public static function main()
		{
				PHPUnit_TextUI_TestRunner::run(self::suite());
		}

		public static function suite()
		{
				$suite = new PHPUnit_Framework_TestSuite('PHPUnit');

				$suite->addTest(Core_Kernel_php_AllTests::suite());
				$suite->addTest(Core_Lib_AllTests::suite());
				$suite->addTest(Core_Classes_php_AllTests::suite());
				$suite->addTest(Core_Client_php_AllTests::suite());

				return $suite;
		}
}

if (PHPUnit_MAIN_METHOD == 'Core_AllTests::main') Core_AllTests::main();

?>