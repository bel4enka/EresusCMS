<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Контроллёр аутентификации в АИ
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-${build.year}, Eresus Project, http://eresus.ru/
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
 * Контроллёр аутентификации в АИ
 *
 * @package EresusCMS
 */
class AdminAuthController extends FrontController {

	/**
	 * Запуск
	 */
	public function execute()
	{
		$this->initRoutes();
		$route = $this->router->find();
		$route->process();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Диалог аутентификации
	 */
	public function authDialog()
	{
		$tmpl = new Template('auth.html');
		$html = $tmpl->compile();
		$this->response->setBody($html);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Вход в систему
	 */
	public function login()
	{
		$username = $this->request->arg('user');
		$password = $this->request->arg('password');

		$user = UserModel::findByCredentials($username, $password);

		if ($user) {
			UserModel::setCurrent($user);
			HTTP::goback();
		}

		die('Error!');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Установка путей для аутентификации
	 */
	protected function initRoutes()
	{
		$this->router = new Router($this->request, $this->response);
		$this->router->add(
			new Route('/login/', 'POST', array($this, 'login'))
		);
		$this->router->setDefault(new Route('', '*', array($this, 'authDialog')));
	}
	//-----------------------------------------------------------------------------

}
