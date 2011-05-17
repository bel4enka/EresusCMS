<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Реестр настроек
 *
 * @copyright 2004, Eresus Project, http://eresus.ru/
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
 * @package Core
 *
 * $Id$
 */

/**
 * Реестр настроек
 *
 * @package Core
 * @since 2.16
 */
class Eresus_Config
{
	/**
	 * Данные реестра
	 *
	 * @var array
	 */
	private static $data = array();

	/**
	 * Записывает значение в реестр
	 *
	 * @param string $key   Ключ
	 * @param mixed $value  Значение
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @see get, drop
	 * @link http://martinfowler.com/eaaCatalog/registry.html Registry pattern
	 */
	public static function set($key, $value)
	{
		self::$data[$key] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает значение из реестра
	 *
	 * @param string $key      Ключ
	 * @param mixed  $default  Опциональне значение по умолчанию, если ключа нет в реестре
	 *
	 * @return mixed  Значение ключа, $default или null
	 *
	 * @see set, drop
	 */
	static public function get($key, $default = null)
	{
		if (isset(self::$data[$key]))
		{
			return self::$data[$key];
		}
		return $default;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Удаляет ключ из реестра
	 *
	 * @param string $key
	 *
	 * @see get, set
	 */
	static public function drop($key)
	{
		unset(self::$data[$key]);
	}
	//-----------------------------------------------------------------------------
}
