<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Контроллер административного интерфейса
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
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
 * @subpackage BusinessLogic
 *
 * $Id: EresusORM.php 1338 2011-01-14 20:39:53Z mk $
 */

/**
 * Контроллер административного интерфейса
 *
 * @package EresusCMS
 * @subpackage BusinessLogic
 * @since 2.16
 */
class EresusAdminFrontController
{
	/**
	 * Текущий модуль АИ
	 * @var object
	 */
	private $module = null;

	/**
	 * Объект UI
	 *
	 * @var AdminUI
	 */
	private $ui;

	/**
	 * Создаёт контроллер
	 *
	 * @param object $ui  Объект UI
	 *
	 * @return EresusAdminFrontController
	 *
	 * @since 2.16
	 */
	public function __construct($ui)
	{
		$this->ui = $ui;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает модуль АИ
	 *
	 * @param object $module
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function setModule($module)
	{
		$this->module = $module;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает объект текущего модуля АИ
	 *
	 * @return object|null
	 *
	 * @since 2.16
	 */
	public function getModule()
	{
		return $this->module;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отправляет созданную страницу пользователю
	 *
	 * @return string  HTML
	 *
	 * @uses EresusLogger::log()
	 * @uses UserRights()
	 * @uses HTTP::request()
	 * @uses HttpResponse::redirect()
	 */
	public function render()
	{
		EresusLogger::log(__METHOD__, LOG_DEBUG, '()');
		/* Проверям права доступа и, если надо, проводим авторизацию */
		if (!UserRights(EDITOR))
		{
			return $this->auth();
		}
		else
		{
			if (HTTP::request()->getLocal() == '/admin/logout/')
			{
				EresusAuthService::getInstance()->logout();
				HttpResponse::redirect($GLOBALS['Eresus']->root . 'admin/');
			}

			return $this->ui->render();
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Аутентификация
	 *
	 * @return string  HTML
	 *
	 * @uses HTTP::request()
	 * @uses EresusUser::USERNAME_FILTER
	 * @uses EresusAuthService::getInstance()
	 * @uses EresusAuthService::SUCCESS
	 * @uses HttpResponse::redirect()
	 */
	private function auth()
	{
		$req = HTTP::request();

		if ($req->getMethod() == 'POST')
		{
			$username = $req->arg('username', EresusUser::USERNAME_FILTER);
			$password = $req->arg('password');
			$state = EresusAuthService::getInstance()->login($username, $password);
			if ($state == EresusAuthService::SUCCESS)
			{
				if ($req->arg('autologin'))
				{
					EresusAuthService::getInstance()->setCookies();
				}
				HttpResponse::redirect('./admin.php');
			}
			return $this->ui->getAuthScreen('Неправильное имя пользователя или пароль');
		}
		return $this->ui->getAuthScreen();
	}
	//-----------------------------------------------------------------------------

}
