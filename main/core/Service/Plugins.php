<?php
/**
 * ${product.title} ${product.version}
 *
 * Служба по работе с модулями расширения
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
 * @package Service
 *
 * $Id$
 */

/**
 * Служба по работе с модулями расширения
 *
 * @package Service
 * @since 2.16
 */
class Eresus_Service_Plugins
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_Service_Plugins
	 */
	private static $instance = null;

	/**
	 * Реестр модулей
	 *
	 * @var array
	 */
	private $registry = array();

	/**
	 * Возвращает экземпляр класса
	 *
	 * @return Eresus_Service_Plugins
	 *
	 * @since 2.16
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает основной объект модуля расширения
	 *
	 * @param string $name
	 *
	 * @throws Eresus_CMS_Exception_NotFound  если модуль $name не найден
	 *
	 * @return Eresus_CMS_Plugin
	 *
	 * @since 2.16
	 */
	public function get($name)
	{
		throw new Eresus_CMS_Exception_NotFound('Module "' . $name . '" not found');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Скрываем конструктор
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	private function __construct()
	{
		$plugins = Eresus_DB_ORM::getTable('Eresus_Model_Plugin')->findAll();
		if (count($plugins))
		{
			foreach($plugins as $plugins)
			{
				$this->registry[$plugin->name] = $plugin;
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Блокируем клонирование
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	// @codeCoverageIgnoreStart
	private function __clone()
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------
}
