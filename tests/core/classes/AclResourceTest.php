<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модульные тесты ресурса доступа
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

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class AclResourceTest extends PHPUnit_Framework_TestCase {

	/**
	 * Простая проверка создания
	 */
	public function testBaseConstruct()
	{
		$stub = new AclResource('SomeResource');

		$this->assertTrue($stub instanceof IAclResource);
		$this->assertEquals('SomeResource', $stub->getResourceId());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка указания родительской роли
	 */
	public function testParent()
	{
		$stub = new AclResource('SomeResource', 'ParentResource');

		$this->assertEquals(array('ParentResource'), $stub->getParentResources());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка указания родительских ролей
	 */
	public function testParents()
	{
		$stub = new AclResource('SomeResource', array('ParentResource1', 'ParentResource2'));

		$this->assertEquals(array('ParentResource1', 'ParentResource2'), $stub->getParentResources());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка исключения
	 * @expectedException EresusTypeException
	 */
	public function testParentsException()
	{
		$stub = new AclResource('SomeResource', 123);
	}
	//-----------------------------------------------------------------------------

	/* */
}