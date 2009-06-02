<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модульные тесты различных классов системы
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-${build.year}, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package EresusCMS
 * @subpackage Tests
 *
 * $Id$
 */

require_once 'backward/AllTests.php';

require_once 'AclResourceTest.php';
require_once 'AclRoleTest.php';
require_once 'AclTest.php';

require_once 'ListContentPluginTest.php';

class Core_Classes_AllTests {

	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Core Classes Tests');

		$suite->addTest(Core_Classes_Backward_AllTests::suite());

		$suite->addTestSuite('AclResourceTest');
		$suite->addTestSuite('AclRoleTest');
		$suite->addTestSuite('AclTest');

		$suite->addTestSuite('ListContentPluginTest');

		return $suite;
	}
}
