<?php
/**
 * ${product.title} ${product.version}
 *
 * Служба событий
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
 * Служба событий
 *
 * @package Service
 * @since 2.16
 */
class Eresus_Service_Events implements Eresus_CMS_Service
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_Service_Events
	 */
	private static $instance = null;

	/**
	 * Реестр подписчиков
	 *
	 * @var Eresus_Helper_Collection
	 */
	private $registry;

	/**
	 * Возвращает экземпляр класса
	 *
	 * @return Eresus_Service_Events
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
	 * Добавляет подписчика
	 *
	 * @param string   $event     событие
	 * @param callback $listener  подписчик
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function addListener($event, $listener)
	{
		$this->registry[$event] []= $listener;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отправляет извещение о событии
	 *
	 * @param string $event  событие
	 * @param mixed $args… дополнительные аргументы
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function dispatch($event)
	{
		$listeners = $this->registry[$event];
		if ($listeners)
		{
			$args = func_get_args();
			array_shift($args);
			foreach ($listeners as $listener)
			{
				call_user_func_array($listener, $args);
			}
		}
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
		$this->registry = new Eresus_Helper_Collection();
		$this->registry->setDefaultValue(array());
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
