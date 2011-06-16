<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Null
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
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
 * Null
 *
 * @package Helper
 *
 * @since 2.16
 */
class Eresus_Helper_NullObject
{
	/**
	 * Исключение при создании объекта
	 *
	 * @var LogicException
	 */
	private $previous;

	/**
	 * Конструктор
	 *
	 * @return Eresus_Helper_NullObject
	 *
	 * @since 2.16
	 */
	public function __construct()
	{
		$this->previous = new LogicException('Null object created instead of real one');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перехватчик чтения свойства
	 *
	 * @param string $name
	 *
	 * @throws LogicException всегда
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function __get($name)
	{
		$e = $this->createException('Trying to get property "' . $name . '" of unexistent object');
		throw $e;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перехватчик записи свойства
	 *
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @throws LogicException всегда
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function __set($name, $value)
	{
		$e = $this->createException('Trying to set property "' . $name . '" of unexistent object');
		throw $e;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перехватчик обращения к методам
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @throws LogicException всегда
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function __call($name, $args)
	{
		$e = $this->createException('Trying to call method "' . $name . '" of unexistent object');
		throw $e;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Создаёт исключение для последующего вброса
	 *
	 * @param string $message
	 *
	 * @return LogicException
	 *
	 * @since 2.16
	 */
	private function createException($message)
	{
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			return new LogicException($message, 0, $this->previous);
		}
		else
		{
			return new LogicException($message . "\nObject created in:\n" .
				$this->previous->getTraceAsString());
		}
	}
	//-----------------------------------------------------------------------------
}
