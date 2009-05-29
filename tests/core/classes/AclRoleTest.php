<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модульные тесты роли доступа
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
class AclRoleTest extends PHPUnit_Framework_TestCase {

	/**
	 * Простая проверка создания
	 */
	public function testBaseConstruct()
	{
		$stub = new AclRole('SomeRole');

		$this->assertTrue($stub instanceof IAclRole);
		$this->assertEquals('SomeRole', $stub->getRoleId());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка указания родительской роли
	 */
	public function testParent()
	{
		$stub = new AclRole('SomeRole', 'ParentRole');

		$this->assertEquals(array('ParentRole'), $stub->getParentRoles());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка указания родительских ролей
	 */
	public function testParents()
	{
		$stub = new AclRole('SomeRole', array('ParentRole1', 'ParentRole2'));

		$this->assertEquals(array('ParentRole1', 'ParentRole2'), $stub->getParentRoles());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка исключения
	 * @expectedException EresusTypeException
	 */
	public function testParentsException()
	{
		$stub = new AclRole('SomeRole', 123);
	}
	//-----------------------------------------------------------------------------

	/* */
}