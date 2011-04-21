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
 * @package BusinessLogic
 *
 * $Id: EresusORM.php 1338 2011-01-14 20:39:53Z mk $
 */

/**
 * Контроллер административного интерфейса
 *
 * @package BusinessLogic
 * @since 2.16
 */
class Eresus_Controller_Admin
{
	/**
	 * Текущий модуль АИ
	 * @var object
	 */
	private $controller = null;

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
	 * @param object $controller
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function setController($controller)
	{
		$this->controller = $controller;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает объект текущего модуля АИ
	 *
	 * @return object|null
	 *
	 * @since 2.16
	 */
	public function getController()
	{
		return $this->controller;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Строит страницу и возвращает её код
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

			$content = $this->getContentHTML();
			return $this->ui->render($content);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Аутентификация
	 *
	 * @return string  HTML
	 *
	 * @uses HTTP::request()
	 * @uses Eresus_Model_User::USERNAME_FILTER
	 * @uses EresusAuthService::getInstance()
	 * @uses EresusAuthService::SUCCESS
	 * @uses HttpResponse::redirect()
	 */
	private function auth()
	{
		$req = HTTP::request();

		if ($req->getMethod() == 'POST')
		{
			$username = trim($req->arg('username', Eresus_Model_User::USERNAME_FILTER));
			$password = trim($req->arg('password'));
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

	/**
	 * Создаёт разметку области контента, используя модуль АИ
	 *
	 * @return string
	 *
	 * @since 2.16
	 *
	 * @uses EresusLogger::log()
	 * @uses arg()
	 * @uses Eresus_CMS::app()
	 * @uses I18n::getInstance()
	 * @uses EresusLogger::exception()
	 * @uses ErrorMessage()
	 * @uses ErrorBox()
	 */
	private function getContentHTML()
	{
		global $Eresus;

		EresusLogger::log(__METHOD__, LOG_DEBUG, '()');

		$html = '';

		// TODO устаревшая функция
		if (arg('mod') && substr(arg('mod'), 0, 4) == 'ext-')
		{
			$name = arg('mod', '/[^\w-]/');
			$name = substr($name, 4);
			$this->setController($Eresus->plugins->load($name));

			/*
			 * Отрисовка контента плагином
			 */
			$controller = $this->getController();
			if (is_object($controller))
			{
				if (method_exists($controller, 'adminRender'))
				{
					try
					{
						$html .= $controller->adminRender();
					}
					catch (SuccessException $e)
					{
						throw $e;
					}
					catch (Exception $e)
					{
						$logMsg = 'Error in plugin "' . $name . '"';
						$msg = I18n::getInstance()->getText('An error occured in plugin "%s".', __CLASS__);
						$msg = sprintf($msg, $name);

						EresusLogger::exception($e);

						$msg .= '<br />' . $e->getMessage();
						$html .= ErrorBox($msg);
					}
				}
				else
				{
					$html .= ErrorBox(sprintf(errMethodNotFound, 'adminRender', get_class($controller)));
				}
			}
			else
			{
				EresusLogger::log(__METHOD__, LOG_ERR, '$controller property is not an object');
				$msg = I18n::getInstance()->getText('Unexpected error! See log for more info.', __CLASS__);
				$html .= ErrorBox($msg);
			}
		}
		else
		{
			$router = Eresus_Service_Admin_Router::getInstance();
			$router->init(HTTP::request());
			$this->setController($router->getController());
			$html = $router->call();
		}

		if (isset($_SESSION['msg']['information']) && count($_SESSION['msg']['information']))
		{
			$messages = '';
			foreach ($_SESSION['msg']['information'] as $message)
			{
				$messages .= InfoBox($message);
			}
			$html = $messages . $html;
			$_SESSION['msg']['information'] = array();
		}
		if (isset($_SESSION['msg']['errors']) &&
			count($_SESSION['msg']['errors']))
		{
			$messages = '';
			foreach ($_SESSION['msg']['errors'] as $message)
			{
				$messages .= ErrorBox($message);
			}
			$html = $messages . $html;
			$_SESSION['msg']['errors'] = array();
		}
		return $html;
	}
	//-----------------------------------------------------------------------------

}
