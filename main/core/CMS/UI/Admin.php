<?php
/**
 * ${product.title}
 *
 * Административный интерфейс CMS
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
 * Административный интерфейс CMS
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_CMS_UI_Admin extends Eresus_CMS_UI
{
	/**
	 * Тема оформления
	 *
	 * @var Eresus_UI_Admin_Theme
	 */
	private $theme;

	/**
	 * Возвращает тему оформления
	 *
	 * @return Eresus_UI_Admin_Theme
	 *
	 * @since 2.16
	 */
	public function getTheme()
	{
		return $this->theme;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @uses Eresus_ACL::getInstance()
	 * @uses Eresus_ACL::isGranted()
	 * @see Eresus_CMS_UI::process()
	 */
	public function process()
	{
		if (!Eresus_ACL::getInstance()->isGranted('EDIT'))
		{
			return $this->auth();
		}
		else
		{
			$req = Eresus_CMS_Request::getInstance();
			if ($req->getBasePath() == '/admin/logout')
			{
				Eresus_Auth::getInstance()->logout();
				Eresus_HTTP_Response::redirect($req->getRootPrefix() . '/admin/');
				//@codeCoverageIgnoreStart
			}
			//@codeCoverageIgnoreEnd

			return $this->main();
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Основной метод интерфейса
	 *
	 * @return Eresus_CMS_Response
	 *
	 * @since 2.16
	 */
	private function main()
	{
		$this->theme = new Eresus_UI_Admin_Theme();
		Eresus_Template::setGlobalValue('theme', $this->theme);

		$this->document->setTemplate('page.default', 'core');

		$ts = Eresus_Template_Service::getInstance();
		$req = Eresus_CMS_Request::getInstance();

		$controllerName = $req->getNextParam();
		if ($controllerName === false)
		{
			$controllerName = 'Dashboard';
		}
		$controllerClass = 'Eresus_Admin_Controller_' . $controllerName;

		try
		{
			if (!class_exists($controllerClass))
			{
				throw new Eresus_CMS_Exception_NotFound;
			}

			$controller = new $controllerClass;
			$contents = $controller->execute($this->document);
			$this->document->setVar('content', $contents);
			$code = Eresus_CMS_Response::OK;
		}
		catch (Eresus_CMS_Exception_Forbidden $e)
		{
			$tmpl = $ts->get('errors/Forbidden', 'core');
			$this->document->setVar('content', $tmpl->compile(array('error' => $e)));
			$code = Eresus_CMS_Response::FORBIDDEN;
		}
		catch (Eresus_CMS_Exception_NotFound $e)
		{
			$tmpl = $ts->get('errors/NotFound', 'core');
			$this->document->setVar('content', $tmpl->compile(array('error' => $e)));
			$code = Eresus_CMS_Response::NOT_FOUND;
		}
		return new Eresus_CMS_Response($this->document->compile(), $code);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Аутентификация
	 *
	 * @return string  HTML
	 *
	 * @uses Eresus_Model_User::USERNAME_FILTER
	 * @uses Eresus_Auth::getInstance()
	 * @uses Eresus_Auth::SUCCESS
	 * @uses Eresus_HTTP_Response::redirect()
	 */
	private function auth()
	{
		$req = Eresus_CMS_Request::getInstance();

		if ($req->isPOST())
		{
			$username = trim($req->getPost()->get('username', Eresus_Model_User::USERNAME_FILTER));
			$password = trim($req->getPost()->get('password'));
			$state = Eresus_Auth::getInstance()->login($username, $password);
			if ($state == Eresus_Auth::SUCCESS)
			{
				if ($req->getPost()->get('autologin'))
				{
					Eresus_Auth::getInstance()->setCookies();
				}
				Eresus_HTTP_Response::redirect($req->getHeader('Referer'));
				//@codeCoverageIgnoreStart
			}
			//@codeCoverageIgnoreEnd
			$html = $this->getAuthScreen(i18n('Invalid username or password', 'admin.auth'));
		}
		else
		{
			$html = $this->getAuthScreen();
		}
		return new Eresus_CMS_Response($html);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает диалог аутентификации
	 *
	 * @param string $errorMessage  сообщение об ошибке
	 * @return string
	 */
	private function getAuthScreen($errorMessage = '')
	{
		$req = Eresus_CMS_Request::getInstance();

		$data = array(
			'username' => $req->getPost()->get('username', Eresus_Model_User::USERNAME_FILTER),
			'password' => $req->getPost()->get('password'),
			'autologin' => $req->getPost()->get('autologin'),
			'error' => $errorMessage
		);
		$ts = Eresus_Template_Service::getInstance();
		$tmpl = $ts->get('auth', 'core');
		$html = $tmpl->compile($data);
		return $html;
	}
	//-----------------------------------------------------------------------------

}
