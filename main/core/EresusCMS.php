<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Главный модуль
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
 * @subpackage Main
 *
 * $Id$
 */

class EresusCMS extends MvcApplication {

	/**
	 * Роутер
	 *
	 * @var Router
	 */
	protected $router;

	/**
	 * Основной метод приложения
	 *
	 * @return int  Код завершения для консольных вызовов
	 *
	 * @see framework/core/EresusApplication#main()
	 */
	public function main()
	{
		EresusClassAutoloader::add('cms.autoload.php');

		if (PHP::isCLI()) {

			return $this->runCLI();

		} else {

			$this->runWWW();
			return 0;

		}

	}
	//-----------------------------------------------------------------------------

	/**
	 * Выполнение в режиме WWW
	 */
	protected function runWWW()
	{
		$this->initWWW();

		/*
		 * Выбор маршрута и следование по нему
		 */
		$route = $this->router->find();

		try {

			$route->process();

		} catch (EresusRuntimeException $e) {

			Core::handleException($e);

		}

		/*
		 * Отправка ответа
		 */
		$this->response->send();

	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация WWW
	 */
	protected function initWWW()
	{
		$this->request = HTTP::request();
		$this->response = new HttpResponse();

		$this->initRoutes();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация маршрутов
	 */
	protected function initRoutes()
	{
		$this->router = new Router($this->request, $this->response);
		$this->router->add(
			new Route('/admin/', '*', 'AdminFrontController')
		);
		$this->router->setDefault(new Route('', '*', 'ClientFrontController'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выполнение в режиме CLI
	 */
	protected function runCLI()
	{
		$this->initCLI();
		return 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация CLI
	 */
	protected function initCLI()
	{
		;
	}
	//-----------------------------------------------------------------------------

}