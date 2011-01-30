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
			$username = trim($req->arg('username', EresusUser::USERNAME_FILTER));
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
	 * @uses EresusCMS::app()
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

		if (arg('mod')) // TODO устаревшая функция
		{
			$controller = arg('mod', '/[^\w-]/');
			$controllerPath = EresusCMS::app()->getFsRoot() . "/core/$controller.php";
			if (file_exists($controllerPath))
			{
				include $controllerPath;
				$class = "T$controller";
				$this->setController(new $class);
			}
			elseif (substr($controller, 0, 4) == 'ext-')
			{
				$name = substr($controller, 4);
				$this->setController($Eresus->plugins->load($name));
			}
			else
			{
				ErrorMessage(errFileNotFound . ': "' . $controllerPath);
			}

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
						if (isset($name))
						{
							$logMsg = 'Error in plugin "' . $name . '"';
							$msg = I18n::getInstance()->getText('An error occured in plugin "%s".', __CLASS__);
							$msg = sprintf($msg, $name);
						}
						else
						{
							$msg = I18n::getInstance()->getText('An error occured module "%s".', __CLASS__);
							$msg = sprintf($msg, get_class($controller));
						}

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
			$router = EresusAdminRouteService::getInstance();
			$router->init(HTTP::request());
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
