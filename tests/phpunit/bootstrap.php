<?php
/**
 * ${product.title}
 *
 * Модульные тесты
 *
 * @version ${product.version}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
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

define('TESTS_SRC_DIR', realpath(__DIR__ . '/../../src'));

PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

mb_internal_encoding('utf-8');

require_once TESTS_SRC_DIR . '/../3rdparty/dependency-injection/sfServiceContainerAutoloader.php';
sfServiceContainerAutoloader::register();

/**
 * Универсальная заглушка
 *
 * @package Eresus
 * @subpackage Tests
 * @since 2.17
 */
class UniversalStub implements ArrayAccess
{
	public function __get($a)
	{
		return $this;
	}
	//-----------------------------------------------------------------------------

	public function __call($a, $b)
	{
		return $this;
	}
	//-----------------------------------------------------------------------------

	public function offsetExists($offset)
	{
		return true;
	}
	//-----------------------------------------------------------------------------

	public function offsetGet($offset)
	{
		return $this;
	}
	//-----------------------------------------------------------------------------

	public function offsetSet($offset, $value)
	{
		;
	}
	//-----------------------------------------------------------------------------

	public function offsetUnset($offset)
	{
		;
	}
	//-----------------------------------------------------------------------------

	public function __toString()
	{
		return '';
	}
	//-----------------------------------------------------------------------------
}

/**
 * Вспомогательный инструментарий для тестов
 *
 * @package Eresus
 * @subpackage Tests
 * @since 2.17
 */
class Eresus_Tests
{
	/**
	 * Устанавливает статическое приватное свойство класса
	 *
	 * @param string $className
	 * @param mixed  $value
	 * @param string $propertyName
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public static function setStatic($className, $value, $propertyName = 'instance')
	{
		$property = new ReflectionProperty($className, $propertyName);
		$property->setAccessible(true);
		$property->setValue($className, $value);
	}
	//-----------------------------------------------------------------------------
}

require_once __DIR__ . '/stubs.php';
