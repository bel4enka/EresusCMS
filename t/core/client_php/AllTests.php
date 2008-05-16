<?php
if (!defined('PHPUnit_MAIN_METHOD')) define('PHPUnit_MAIN_METHOD', 'Core_Client_php_AllTests::main');

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'ClientUITest.php';

class Core_Client_php_AllTests
{
		public static function main()
		{
				PHPUnit_TextUI_TestRunner::run(self::suite());
		}

		public static function suite()
		{
				$suite = new PHPUnit_Framework_TestSuite('PHPUnit Framework');

				$suite->addTestSuite('ClientUITest');

				return $suite;
		}
}

if (PHPUnit_MAIN_METHOD == 'Core_Client_php_AllTests::main') Core_Lib_Sections_AllTests::main();

?>