<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Административный интерфейс CMS
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
 * @package CMS
 *
 * $Id$
 */

/**
 * Административный интерфейс CMS
 *
 * @package CMS
 * @since 2.16
 */
class Eresus_CMS_UI_Admin extends Eresus_CMS_UI
{
	/**
	 * @see Eresus_CMS_UI::process()
	 */
	public function process()
	{
		if (!Eresus_Service_ACL::getInstance()->isGranted('EDITOR'))
		{
			return $this->auth();
		}
		else
		{
			return new Eresus_CMS_Response('admin');
		}
/*		$router = Eresus_Service_Client_Router::getInstance();
		$request = $this->get('request');

		try
		{
			$this->section = $router->findSection($request);
			$this->module = $this->section->getModule();
			$response = new Eresus_CMS_Response($this->module->clientRenderContent($this->section));
		}
		catch (Eresus_CMS_Exception_Forbidden $e)
		{
			$tmpl = Eresus_Service_Templates::getInstance()->get('errors/403');
			$html = $tmpl ? $tmpl->compile() : 'Access denied';
			$response = new Eresus_CMS_Response($html, Eresus_CMS_Response::FORBIDDEN);
		}
		catch (Eresus_CMS_Exception_NotFound $e)
		{
			$tmpl = Eresus_Service_Templates::getInstance()->get('errors/404');
			$html = $tmpl ? $tmpl->compile() : 'Not Found';
			$response = new Eresus_CMS_Response($html, Eresus_CMS_Response::NOT_FOUND);
		}

		return $response;*/
	}
	//-----------------------------------------------------------------------------

	/**
	 * Аутентификация
	 *
	 * @return string  HTML
	 *
	 * @uses Eresus_Model_User::USERNAME_FILTER
	 * @uses Eresus_Service_Auth::getInstance()
	 * @uses Eresus_Service_Auth::SUCCESS
	 * @uses Eresus_HTTP_Response::redirect()
	 */
	private function auth()
	{
		$req = $this->get('request');

		if ($req->isPOST())
		{
			$username = trim($req->query->get('username', Eresus_Model_User::USERNAME_FILTER));
			$password = trim($req->query->get('password'));
			$state = Eresus_Service_Auth::getInstance()->login($username, $password);
			if ($state == Eresus_Service_Auth::SUCCESS)
			{
				if ($req->arg('autologin'))
				{
					Eresus_Service_Auth::getInstance()->setCookies();
				}
				Eresus_HTTP_Response::redirect('./admin.php');
			}
			$html = $this->getAuthScreen('Неправильное имя пользователя или пароль');
		}
		$html = $this->getAuthScreen();
		return new Eresus_CMS_Response($html);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает диалог аутентификации
	 *
	 * @param string $errorMessage  сообщение об ошибке
	 * @return string
	 */
	public function getAuthScreen($errorMessage = '')
	{
		$req = $this->get('request');

		$data = array(
			'username' => $req->getPost()->get('username', Eresus_Model_User::USERNAME_FILTER),
			'password' => $req->getPost()->get('password'),
			'autologin' => $req->getPost()->get('autologin'),
			'error' => $errorMessage
		);
		$ts = Eresus_Service_Templates::getInstance();
		$tmpl = $ts->get('auth', 'core');
		$html = $tmpl->compile($data);
		return $html;
	}
	//-----------------------------------------------------------------------------

}
