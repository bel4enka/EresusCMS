<?php
/**
 * ${product.title}
 *
 * Реестр настроек
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
 * Реестр настроек
 *
 * Реестр настроек — это глобально доступное хранилище именованных значений произвольного типа.
 * В нём хранятся настройки CMS.
 *
 * Некоторые ключи:
 *
 * - <b>eresus.cms.multiple</b>: bool — вкл. или выкл. режим обслуживания нескольких сайтов
 * - <b>eresus.cms.dsn</b>: string — DSN для подключения к БД
 * - <b>eresus.cms.dsn.prefix</b>: string — префикс имён таблиц БД
 * - <b>eresus.cms.locale</b>: string — код локали «ru_RU», «en_US» и т. д.
 * - <b>eresus.cms.timezone</b>: string — временна́я зона
 * - <b>eresus.cms.session.timeout</b>: int — тайм-аут сессии
 * - <b>eresus.cms.debug</b>: bool — вкл./выкл. режима отладки
 * - <b>eresus.cms.log.level</b>: int — уровень детализации журнала
 *
 * @package Eresus
 * @since 2.16
 * @link http://martinfowler.com/eaaCatalog/registry.html Registry pattern
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
	 * <b>Внимание!</b> Следующие префиксы зарезервированы для использования CMS. Авторам расширений
	 * запрещено использовать их для создания собственных ключей:
	 *
	 * - eresus.
	 * - php.
	 * - system.
	 *
	 * @param string $key    ключ
	 * @param mixed  $value  значение
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @see get(), drop()
	 */
	public static function set($key, $value)
	{
		self::$data[$key] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает значение из реестра
	 *
	 * @param string $key      ключ
	 * @param mixed  $default  опциональне значение по умолчанию, если ключа нет в реестре
	 *
	 * @return mixed  значение ключа, $default или null
	 *
	 * @since 2.16
	 * @see set(), drop()
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
	 * @param string $key  имя ключа
	 *
	 * @since 2.16
	 * @see get(), set()
	 */
	static public function drop($key)
	{
		unset(self::$data[$key]);
	}
	//-----------------------------------------------------------------------------
}
