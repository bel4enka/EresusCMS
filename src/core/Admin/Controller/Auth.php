<?php
/**
 * ${product.title}
 *
 * Контроллер аутентификации
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
 * Контроллер аутентификации
 *
 * @package Eresus
 * @since 2.17
 */
class Eresus_Admin_Controller_Auth extends Eresus_Admin_Controller
{
	/**
	 * Запрашивает имя и проводит аутентификаицю
	 *
	 * @return string  HTML
	 *
	 * @since 2.17
	 */
	public function authAction()
	{
		$req = HTTP::request();
		$user = $req->arg('user', '/[^a-z0-9_\-\.\@]/');
		$password = $req->arg('password');
		$autologin = $req->arg('autologin');

		$data = array('errors' => array());
		$data['user'] = $user;
		$data['autologin'] = $autologin;

		if ($req->getMethod() == 'POST')
		{
			if ($GLOBALS['Eresus']->login($req->arg('user'), Eresus_Entity_User::passwordHash($password),
				$autologin))
			{
				HTTP::redirect('./admin.php');
			}
		}

		$session =& $GLOBALS['Eresus']->session;
		if (isset($session['msg']['errors']) && count($session['msg']['errors']))
		{
			foreach ($session['msg']['errors'] as $message)
			{
				$data['errors'] []= $message;
			}

			$session['msg']['errors'] = array();
		}

		$tmpl = Eresus_Template::fromFile('core/templates/auth.html');
		$html = $tmpl->compile($data);
		return $html;
	}
	//-----------------------------------------------------------------------------
}
