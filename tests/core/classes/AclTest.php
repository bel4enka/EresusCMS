<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модульные тесты списка контроля доступа
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

require_once 'core/classes/ACL.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class ACLTest extends PHPUnit_Framework_TestCase {

	/**
	 * Проверка работы "одиночки"
	 */
	public function testGetInstance()
	{
		$instance1 = ACL::getInstance();
		$instance2 = ACL::getInstance();

		$this->assertSame($instance1, $instance2);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка исключения при добавлении ресурса
	 *
	 * @expectedException EresusTypeException
	 */
	public function testAddResourceException()
	{
		$acl = new Acl();
		$acl->addResource(new StdClass);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка добавления ресурсов
	 */
	public function testAddResource()
	{
		$acl = new Acl();
		$this->assertEquals(0, count($acl->getResources()));

		$resource = new AclResource('TestResource');
		$acl->addResource($resource);
		$this->assertEquals(1, count($acl->getResources()), 'Invalid resource count');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка hasResource
	 */
	public function testHasResource()
	{
		$acl = new Acl();

		$this->assertTrue($acl->hasResource('*'), 'Resource "*" alwayes exists');

		$resource = new AclResource('TestResource');
		$acl->addResource($resource);

		$this->assertTrue($acl->hasResource($resource), 'Check by object');
		$this->assertTrue($acl->hasResource('TestResource'), 'Check by name');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка исключения при добавлении роли
	 *
	 * @expectedException EresusTypeException
	 */
	public function testAddRoleException()
	{
		$acl = new Acl();
		$acl->addRole(new StdClass);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка добавления ролей
	 */
	public function testAddRole()
	{
		$acl = new Acl();
		$this->assertEquals(0, count($acl->getRoles()));

		$role = new AclRole('TestRole');
		$acl->addRole($role);
		$this->assertEquals(1, count($acl->getRoles()), 'Invalid role count');

		$this->assertTrue($acl->hasRole($role), 'Check by object');
		$this->assertTrue($acl->hasRole('TestRole'), 'Check by name');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Базовая проверка установки и проверки прав
	 */
	public function testBasicSetGet()
	{
		$acl = new Acl();
		$acl->addRole(new AclRole('guest'));
		$acl->addResource(new AclResource('page'));

		$acl->allow('guest', 'page', 'view');

		$this->assertTrue($acl->isAllowed('guest', 'page', 'view'), 'Case 1');

		$acl = new Acl();
		$acl->addRole(new AclRole('guest'));
		$acl->addResource(new AclResource('page'));

		$acl->allow('guest', null, 'view');

		$this->assertTrue($acl->isAllowed('guest', null, 'view'), 'Case 2.1');
		$this->assertTrue($acl->isAllowed('guest', 'page', 'view'), 'Case 2.2');

		$acl = new Acl();
		$acl->addRole(new AclRole('guest'));
		$acl->addResource(new AclResource('page'));

		$acl->allow('guest');

		$this->assertTrue($acl->isAllowed('guest'), 'Case 3.1');
		$this->assertTrue($acl->isAllowed('guest', 'page'), 'Case 3.2');
		$this->assertTrue($acl->isAllowed('guest', null, 'view'), 'Case 3.3');
		$this->assertTrue($acl->isAllowed('guest', 'page', 'view'), 'Case 3.4');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Базовая проверка с применением объектов
	 */
	public function testObjects()
	{
		$role = new AclRole('TestRole');
		$resource = new AclResource('TestResource');

		$acl = new Acl();
		$acl->addRole($role);
		$acl->addResource($resource);
		$acl->allow('TestRole', 'TestResource', 'view');
		$this->assertTrue($acl->isAllowed($role, $resource, 'view'), 'Case 1');

		$acl = new Acl();
		$acl->addRole($role);
		$acl->addResource($resource);
		$acl->allow($role, $resource, 'view');
		$this->assertTrue($acl->isAllowed('TestRole', 'TestResource', 'view'), 'Case 2');

	}
	//-----------------------------------------------------------------------------

	/**/
}
