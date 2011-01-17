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
			$module = arg('mod', '/[^\w-]/');
			$modulePath = EresusCMS::app()->getFsRoot() . "/core/$module.php";
			if (file_exists($modulePath))
			{
				include $modulePath;
				$class = "T$module";
				$this->setModule(new $class);
			}
			elseif (substr($module, 0, 4) == 'ext-')
			{
				$name = substr($module, 4);
				$this->setModule($Eresus->plugins->load($name));
			}
			else
			{
				ErrorMessage(errFileNotFound . ': "' . $modulePath);
			}

			/*
			 * Отрисовка контента плагином
			 */
			$module = $this->getModule();
			if (is_object($module))
			{
				if (method_exists($module, 'adminRender'))
				{
					try
					{
						$html .= $module->adminRender();
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
							$msg = sprintf($msg, $module);
						}

						EresusLogger::exception($e);

						$msg .= '<br />' . $e->getMessage();
						$html .= ErrorBox($msg);
					}
				}
				else
				{
					$html .= ErrorBox(sprintf(errMethodNotFound, 'adminRender', get_class($module)));
				}
			}
			else
			{
				EresusLogger::log(__METHOD__, LOG_ERR, '$module property is not an object');
				$msg = I18n::getInstance()->getText('Unexpected error! See log for more info.', __CLASS__);
				$html .= ErrorBox($msg);
			}
		}
		else
		{
			$router = AdminRouteService::getInstance();
			$router->init(HTTP::request());
			$html = $router->call();
		}

		if (isset($Eresus->session['msg']['information']) &&
			count($Eresus->session['msg']['information']))
		{
			$messages = '';
			foreach ($Eresus->session['msg']['information'] as $message)
			{
				$messages .= InfoBox($message);
			}
			$html = $messages . $html;
			$Eresus->session['msg']['information'] = array();
		}
		if (isset($Eresus->session['msg']['errors']) &&
			count($Eresus->session['msg']['errors']))
		{
			$messages = '';
			foreach ($Eresus->session['msg']['errors'] as $message)
			{
				$messages .= ErrorBox($message);
			}
			$html = $messages . $html;
			$Eresus->session['msg']['errors'] = array();
		}
		return $html;
	}
	//-----------------------------------------------------------------------------

}
