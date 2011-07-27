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
require_once dirname(__FILE__) . '/../../../main/core/Auth.php';
require_once dirname(__FILE__) . '/../../../main/core/ACL.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_ACL_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_ACL::getInstance
	 */
	public function test_getInstance()
	{
		$this->assertInstanceOf('Eresus_ACL', Eresus_ACL::getInstance());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_ACL::isGranted
	 */
	public function test_isGranted()
	{
		$acl = Eresus_ACL::getInstance();

		$this->assertFalse($acl->isGranted('VIEW'));

		$user = new stdClass;

		$p_user = new ReflectionProperty('Eresus_Auth', 'user');
		$p_user->setAccessible(true);
		$auth = Eresus_Auth::getInstance();
		$p_user->setValue($auth, $user);

		$user->access = null;
		$this->assertFalse($acl->isGranted('VIEW'));

		$user->access = 0;
		$this->assertFalse($acl->isGranted('VIEW'));

		//ROOT
		$user->access = 1;
		$this->assertFalse($acl->isGranted('unknown'));
		$this->assertTrue($acl->isGranted('ADMIN'));
		$this->assertTrue($acl->isGranted('EDIT'));
		$this->assertTrue($acl->isGranted('VIEW'));

		//ADMIN
		$user->access = 2;
		$this->assertTrue($acl->isGranted('ADMIN'));
		$this->assertTrue($acl->isGranted('EDIT'));
		$this->assertTrue($acl->isGranted('VIEW'));

		//EDITOR
		$user->access = 3;
		$this->assertFalse($acl->isGranted('ADMIN'));
		$this->assertTrue($acl->isGranted('EDIT'));
		$this->assertTrue($acl->isGranted('VIEW'));

		//USER
		$user->access = 4;
		$this->assertFalse($acl->isGranted('ADMIN'));
		$this->assertFalse($acl->isGranted('EDIT'));
		$this->assertTrue($acl->isGranted('VIEW'));

		//GUEST
		$user->access = 5;
		$this->assertFalse($acl->isGranted('ADMIN'));
		$this->assertFalse($acl->isGranted('EDIT'));
		$this->assertFalse($acl->isGranted('VIEW'));

	}
	//-----------------------------------------------------------------------------

	/* */
}
