<?php
/**
 * ${product.title}
 *
 * Служба авторизации
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
 * Служба аутентификации
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_Auth
{
	/**
	 * Операция выполнена успешно
	 * @var boolean
	 */
	const SUCCESS = 0;

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
	 * @var Eresus_Auth
	 */
	private static $instance = null;

	/**
	 * Модель текущего пользователя
	 *
	 * @var Eresus_Model_User
	 */
	private $user = null;

	/**
	 * Возвращает экземпляр службы
	 *
	 * @return Eresus_Auth
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
	 * @return Eresus_Model_User|null
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
	 * @param string $username  имя пользователя
	 * @param string $password  пароль
	 *
	 * @return int  код результата (см. константы класса)
	 *
	 * @uses Eresus_Model_User::passwordHash()
	 * @see loginByHash()
	 * @since 2.16
	 */
	public function login($username, $password)
	{
		$hash = Eresus_Model_User::passwordHash($password);
		return $this->loginByHash($username, $hash);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проводит аутентификацию и авторизацию в системе по хэшу пароля
	 *
	 * @param string $username  имя пользователя
	 * @param string $hash      хэш пароля
	 *
	 * @return int  код результата (см. константы класса)
	 *
	 * @see login()
	 * @uses Eresus_DB_ORM::getTable()
	 * @uses Eresus_Logger::exception()
	 * @since 2.16
	 */
	public function loginByHash($username, $hash)
	{
		$users = Eresus_DB_ORM::getTable('Eresus_Model_User')->findByUsername($username);

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
			//TODO (вызывает ошибку в юнит-тестах) Eresus_Logger::exception($e);
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
		$this->clearCookies();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализуирет службу
	 *
	 * @return void
	 *
	 * @uses Eresus_DB_ORM::getTable
	 * @since 2.16
	 */
	public function init()
	{
		if (isset($_SESSION['user']) && $_SESSION['user'])
		{
			$id = intval($_SESSION['user']);
			$this->user = Eresus_DB_ORM::getTable('Eresus_Model_User')->find($id);
		}
		elseif (isset($_COOKIE['eresus_auth']))
		{
			$cookie = unserialize($_COOKIE['eresus_auth']);
			if (isset($cookie['u']) && isset($cookie['h']))
			{
				$this->loginByHash($cookie['u'], $cookie['h']);
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает куки для автоматического входа
	 *
	 * Метод устанавливает куки, содержащее информацию для автоматической аутентификации посетителя.
	 * Эта информация проверяется методом {@link Eresus_Auth::init()}.
	 *
	 * @return void
	 *
	 * @uses Eresus_Kernel::app()
	 * @uses Eresus_Kernel::get()
	 * @since 2.16
	 */
	public function setCookies()
	{
		if (!$this->user)
		{
			return;
		}

		$value = array(
			'u' => $this->user->username,
			'h' => $this->user->password
		);
		$value = serialize($value);
		//$site = Eresus_Kernel::app()->get('site');
		// TODO Куки должны устанавливаться на корень сайта и это не обязательно "/"
		$req = Eresus_CMS_Request::getInstance();
		setcookie('eresus_auth', $value, time() + 2592000, $req->getRootPrefix());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Удаляет куки, установленные {@link setCookies()}
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function clearCookies()
	{
		if (isset($_COOKIE['eresus_auth']))
		{
			setcookie('eresus_auth', $_COOKIE['eresus_auth'], time() - 3600, '/');
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
