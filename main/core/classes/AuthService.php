<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Служба авторизации
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 * @package EresusCMS
 *
 * $Id$
 */

/**
 * Служба аутентификации
 *
 * @package EresusCMS
 * @since 2.16
 */
class AuthService implements ServiceInterface
{
	/**
	 * Операция выполнена успешно
	 * @var boolean
	 */
	const SUCCESS = true;

	/**
	 * Неизвестный пользователь
	 * @var int
	 */
	const UNKNOWN_USER = 1;

	/**
	 * Неправильный пароль
	 * @var int
	 */
	const BAD_PASSWORD = 2;

	/**
	 * Учётная запись отключена
	 * @var int
	 */
	const ACCOUNT_DISABLED = 3;

	/**
	 * Подозрение на попытку подбора пароля
	 * @var int
	 */
	const BRUTEFORCING = 4;

	/**
	 * Экземпляр-одиночка
	 *
	 * @var AuthService
	 */
	private static $instance = null;

	/**
	 * Модель текущего пользователя
	 *
	 * @var User
	 */
	private $user = null;

	/**
	 * Возвращает экземпляр класса
	 *
	 * @return AuthService
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
	 * Возвращает модель текущего пользователя
	 *
	 * @return User|null
	 *
	 * @since 2.16
	 */
	public function getUser()
	{
		return $this->user;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проводит аутентификацию и авторизацию в системе
	 *
	 * @param string $username
	 * @param string $password
	 *
	 * @return bool|int  TRUE on success or error code
	 *
	 * @uses User::passwordHash()
	 * @uses loginByHash()
	 * @since 2.16
	 */
	public function login($username, $password)
	{
		$hash = User::passwordHash($password);
		return $this->loginByHash($username, $hash);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проводит аутентификацию и авторизацию в системе
	 *
	 * @param string $username  имя пользователя
	 * @param string $hash      хэш пароля
	 *
	 * @return bool|int  TRUE on success or error code
	 *
	 * @uses ORM::getTable()
	 * @since 2.16
	 */
	public function loginByHash($username, $hash)
	{
		$users = ORM::getTable('User')->findByUsername($username);

		if (!count($users))
		{
			return self::UNKNOWN_USER;
		}

		$user = $users[0];

		if (!$user->active)
		{
			return self::ACCOUNT_DISABLED;
		}

		if (time() - $user->lastLoginTime < $user->loginErrors)
		{
			return self::BRUTEFORCING;
		}

		if ($hash != $user->password)
		{
			return self::BAD_PASSWORD;
		}

		$this->user = $user;
		// Записываем время последнего входа
		$user->lastVisit = date('Y-m-d H:i:s');
		$user->lastLoginTime = time();
		$user->loginErrors = 0;
		try
		{
			$user->save();
		}
		catch (Exception $e)
		{
			EresusLogger::exception($e);
			throw new DomainException('Ошибка при обновлении состояния учётной записи');
		}
		// Наличие в сессии идентификатора пользователя - признак успешной аутентификации
		$_SESSION['user'] = $user->id;

		return self::SUCCESS;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выводит из системы текущего пользователя
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function logout()
	{
		$this->user = null;
		unset($_SESSION['user']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализуирет службу
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function init()
	{
		if (isset($_SESSION['user']) && $_SESSION['user'])
		{
			$id = intval($_SESSION['user']);
			$this->user = ORM::getTable('User')->find($id);
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
	}
	//-----------------------------------------------------------------------------

	/**
	 * Блокируем клонирование
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	private function __clone()
	// @codeCoverageIgnoreStart
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------
}
