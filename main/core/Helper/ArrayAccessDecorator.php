<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Декторатор объектов, добавляющий функционал ArrayAccess
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
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
 * @package Helper
 *
 * $Id$
 */

/**
 * Декторатор объектов, добавляющий функционал ArrayAccess
 *
 * @package Helper
 *
 * @since 2.16
 */
class Eresus_Helper_ArrayAccessDecorator implements ArrayAccess
{
	/**
	 * Декорируемый объект
	 *
	 * @var object
	 * @since 2.16
	 */
	protected $object;

	/**
	 * Конструктор
	 *
	 * @param object $object
	 *
	 * @return Eresus_Helper_ArrayAccessDecorator
	 *
	 * @throws InvalidArgumentException если $object не объект
	 * @since 2.16
	 */
	public function __construct($object)
	{
		if (!is_object($object))
		{
			throw new InvalidArgumentException(
				'First argument of ' . __CLASS__ . '::__construct must be an object and not ' .
				gettype($object));
		}
		$this->object = $object;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset)
	{
		$this->checkOffsetType($offset);
		return property_exists($this->object, $offset);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset)
	{
		$this->checkOffsetType($offset);

		$getter = 'get' . $offset;
		if (method_exists($this->object, $getter))
		{
			return $this->object->$getter();
		}

		return $this->object->$offset;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value)
	{
		$this->checkOffsetType($offset);

		$setter = 'set' . $offset;
		if (method_exists($this->object, $setter))
		{
			$this->object->$setter($value);
		}
		else
		{
			$this->object->$offset = $value;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see ArrayAccess::offsetUnset()
	 */
	// @codeCoverageIgnoreStart
	public function offsetUnset($offset)
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет тип ключа
	 *
	 * @param mixed $offset
	 *
	 * @return void
	 *
	 * @throws InvalidArgumentException если у ключа не скалярное значение
	 * @since 2.15
	 */
	protected function checkOffsetType($offset)
	{
		if (!is_string($offset) || is_numeric($offset))
		{
			throw new InvalidArgumentException('Invalid property type or name');
		}
	}
	//-----------------------------------------------------------------------------

}