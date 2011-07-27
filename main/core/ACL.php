<?php
/**
 * ${product.title}
 *
 * Служба списков контроля доступа
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
 * Служба списков контроля доступа
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_ACL
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_ACL
	 */
	private static $instance = null;

	/**
	 * Возвращает экземпляр службы
	 *
	 * @return Eresus_ACL
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
	 * Проверяет, наличие у пользователя прав на объект
	 *
	 * Переходный вариант от старой системы прав к ACL.
	 *
	 * @param string $permission  право ('ADMIN', 'EDIT', 'VIEW')
	 * @param mixed  $object      объект (пока что всегда должен быть null)
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	public function isGranted($permission, $object = null)
	{
		$user = Eresus_Auth::getInstance()->getUser();
		if (is_null($user) || is_null($user->access) || $user->access < 1)
		{
			return false;
		}
		switch ($permission)
		{
			case 'ADMIN':
				return $user->access <= 2;
			case 'EDIT':
				return $user->access <= 3;
			case 'VIEW':
				return $user->access <= 4;
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
