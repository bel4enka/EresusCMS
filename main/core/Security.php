<?php
/**
 * ${product.title}
 *
 * Служба безопасности
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
 * Служба безопасности
 *
 * Центральный элемент управления доступом.
 *
 * @package Eresus
 * @since 2.20
 */
class Eresus_Security
{
	/**
	 * Гость
	 *
	 * @var string
	 */
	const ROLE_GUEST = 'ROLE_GUEST';

	/**
	 * Простой пользователь
	 *
	 * @var string
	 */
	const ROLE_MEMBER = 'ROLE_MEMBER';

	/**
	 * Редактор
	 *
	 * @var string
	 */
	const ROLE_EDITOR = 'ROLE_EDITOR';

	/**
	 * Администратор
	 *
	 * @var string
	 */
	const ROLE_ADMIN = 'ROLE_ADMIN';

	/**
	 * Суперпользователь
	 *
	 * @var string
	 */
	const ROLE_ROOT = 'ROLE_ROOT';

	/**
	 * Право на просмотр
	 *
	 * @var string
	 */
	const PERM_VIEW = 'VIEW';

	/**
	 * Право на изменение
	 *
	 * Включает в себя PERM_VIEW
	 *
	 * @var string
	 */
	const PERM_EDIT = 'EDIT';

	/**
	 * Право на удаление
	 *
	 * Включает в себя PERM_VIEW и PERM_EDIT
	 *
	 * @var string
	 */
	const PERM_DELETE = 'DELETE';

	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_Security
	 */
	private static $instance = null;

	/**
	 * Возвращает экземпляр класса
	 *
	 * @return Eresus_Security
	 *
	 * @since 2.20
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
	 * Проверяет, наличие у пользователя роли или права на объект
	 *
	 * Переходный вариант от старой системы прав.
	 *
	 * @param string $subject  роль или право см. константы класса
	 * @param object $target   объект доступа (пока что всегда должен быть null)
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	public function isGranted($subject, $target = null)
	{
		$user = Eresus_Auth::getInstance()->getUser();
		if (is_null($user) || is_null($user->access) || $user->access < 1)
		{
			return false;
		}
		switch ($subject)
		{
			case self::ROLE_ROOT:
				return $user->access == 1;
			case self::ROLE_ADMIN:
				return $user->access <= 2;
			case self::ROLE_EDITOR:
				return $user->access <= 3;
			case self::ROLE_MEMBER:
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
	 * @since 2.20
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
	 * @since 2.20
	 */
	// @codeCoverageIgnoreStart
	private function __clone()
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

}
