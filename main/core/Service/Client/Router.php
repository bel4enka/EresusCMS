<?php
/**
 * ${product.title} ${product.version}
 *
 * Служба роутинга КИ
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
 * @package Service
 *
 * $Id: Router.php 1628 2011-05-19 18:36:14Z mk $
 */

/**
 * Служба роутинга КИ
 *
 * @package Service
 *
 * @since 2.16
 */
class Eresus_Service_Client_Router implements Eresus_CMS_Service
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_Service_Client_Router
	 */
	private static $instance = null;

	/**
	 * Возвращает экземпляр класса
	 *
	 * @return Eresus_Service_Client_Router
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
	 * Возвращает метод на основе запроса
	 *
	 * @param Eresus_CMS_Request $request
	 *
	 * @throws Eresus_CMS_Exception_NotFound  если запрошенный ресурс не найден
	 *
	 * @return callback
	 *
	 * @since 2.16
	 */
	public function findAction(Eresus_CMS_Request $request)
	{
		throw new Eresus_CMS_Exception_NotFound;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Скрываем конструктор
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	// @codeCoverageIgnoreStart
	private function __construct()
	{
	}
	// @codeCoverageIgnoreEnd
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
