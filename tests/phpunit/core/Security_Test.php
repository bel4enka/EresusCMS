<?php
/**
 * ${product.title} ${product.version}
 *
 * Модульные тесты
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
 * @package Eresus
 * @subpackage Tests
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../stubs.php';
require_once TESTS_SRC_ROOT . '/core/Security.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Security_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		Eresus_Tests::setStatic('Eresus_Security', null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		Eresus_Tests::setStatic('Eresus_Security', null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security::getInstance
	 */
	public function test_getInstance()
	{
		$test = Eresus_Security::getInstance();
		$this->assertSame($test, Eresus_Security::getInstance());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security::isGranted
	 */
	public function test_isGranted()
	{
		Eresus_Tests::setStatic('Eresus_Auth', null);

		$sec = Eresus_Security::getInstance();

		$this->assertFalse($sec->isGranted('ROLE_MEMBER'));

		$user = new stdClass;

		$p_user = new ReflectionProperty('Eresus_Auth', 'user');
		$p_user->setAccessible(true);
		$auth = Eresus_Auth::getInstance();
		$p_user->setValue($auth, $user);

		$user->access = null;
		$this->assertFalse($sec->isGranted('ROLE_MEMBER'));

		$user->access = 0;
		$this->assertFalse($sec->isGranted('ROLE_MEMBER'));

		//ROOT
		$user->access = 1;
		$this->assertFalse($sec->isGranted('unknown'));
		$this->assertTrue($sec->isGranted('ROLE_ADMIN'));
		$this->assertTrue($sec->isGranted('ROLE_EDITOR'));
		$this->assertTrue($sec->isGranted('ROLE_MEMBER'));

		//ADMIN
		$user->access = 2;
		$this->assertTrue($sec->isGranted('ROLE_ADMIN'));
		$this->assertTrue($sec->isGranted('ROLE_EDITOR'));
		$this->assertTrue($sec->isGranted('ROLE_MEMBER'));

		//EDITOR
		$user->access = 3;
		$this->assertFalse($sec->isGranted('ROLE_ADMIN'));
		$this->assertTrue($sec->isGranted('ROLE_EDITOR'));
		$this->assertTrue($sec->isGranted('ROLE_MEMBER'));

		//USER
		$user->access = 4;
		$this->assertFalse($sec->isGranted('ROLE_ADMIN'));
		$this->assertFalse($sec->isGranted('ROLE_EDITOR'));
		$this->assertTrue($sec->isGranted('ROLE_MEMBER'));

		//GUEST
		$user->access = 5;
		$this->assertFalse($sec->isGranted('ROLE_ADMIN'));
		$this->assertFalse($sec->isGranted('ROLE_EDITOR'));
		$this->assertFalse($sec->isGranted('ROLE_MEMBER'));

	}
	//-----------------------------------------------------------------------------

	/* */
}
