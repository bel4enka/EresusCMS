<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Служба списков контроля доступа
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
 * Служба списков контроля доступа
 *
 * @package Service
 * @since 2.16
 */
class Eresus_Service_ACL
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_Service_ACL
	 */
	private static $instance = null;

	/**
	 * Возвращает экземпляр службы
	 *
	 * @return Eresus_Service_ACL
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
	 * Проверяет, наличие у пользователя указанной роли
	 *
	 * @param string $role
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	public function isGranted($role)
	{
		$user = Eresus_Auth::getInstance()->getUser();
		if (is_null($user) || is_null($user->access) || $user->access < 1)
		{
			return false;
		}
		switch ($role)
		{
			case 'ROOT':
				return $user->access == 1;
			case 'ADMIN':
				return $user->access <= 2;
			case 'EDITOR':
				return $user->access <= 3;
			case 'USER':
				return $user->access <= 4;
			case 'GUEST':
				return $user->access <= 5;
		}
		return false;
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
