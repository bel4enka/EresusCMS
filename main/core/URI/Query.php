<?php
/**
 * ${product.title}
 *
 * Строка запроса
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
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
 *
 * $Id$
 */

/**
 * Строка запроса для {@link Eresus_URI}
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_URI_Query
{
	/**
	 * Аргументы
	 *
	 * @var array
	 */
	private $args = array();

	/**
	 * Конструктор
	 *
	 * @param mixed $args  ассоциативный массив или строка аргументов
	 *
	 * @return Eresus_URI_Query
	 *
	 * @since 2.16
	 */
	public function __construct($args = null)
	{
		switch (true)
		{
			case is_array($args):
				$this->args = $args;
			break;

			case is_string($args):
				$this->args = $this->parse($args);
			break;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает строковое представление
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function __toString()
	{
		$args = array();
		foreach ($this->args as $key => $value)
		{
			$args []= $key . '=' . $value;
		}
		return implode('&', $args);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает аргумент
	 *
	 * @param string $name  имя аргумента
	 *
	 * @return mixed
	 *
	 * @since 2.16
	 */
	public function get($name)
	{
		$value = isset($this->args[$name]) ? $this->args[$name] : null;
		return $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает значение аргумента
	 *
	 * @param string $name   имя аргумента
	 * @param mixed  $value  значение аргумента
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function set($name, $value)
	{
		$this->args[$name] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Производит разбор строки запроса
	 *
	 * @param string $query
	 *
	 * @return array
	 *
	 * @since 2.16
	 */
	private function parse($query)
	{
		parse_str($query, $args);
		return $args;
	}
	//-----------------------------------------------------------------------------
}
