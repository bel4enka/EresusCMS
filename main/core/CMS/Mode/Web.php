<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Веб-режим CMS
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
 * $Id: Service.php 1636 2011-05-21 11:29:03Z mk $
 */

/**
 * Веб-режим CMS
 *
 * @package CMS
 * @since 2.16
 */
class Eresus_CMS_Mode_Web extends Eresus_CMS_Mode
{
	/**
	 * Конструктор
	 *
	 * @return Eresus_CMS_Mode_Web
	 *
	 * @since 2.16
	 */
	public function __construct()
	{
		$this->initSession();

		$cms = Eresus_CMS::app();
		Eresus_Config::set('core.template.templateDir', $cms->getRootDir());
		Eresus_Config::set('core.template.compileDir', $cms->getRootDir() . '/var/cache/templates');
		Eresus_Template::setGlobalValue('cms', new Eresus_Helper_ArrayAccessDecorator($cms));

		if (substr($this->getRequest()->getBasePath(), 0, 6) == '/admin')
		{
			$this->ui = new Eresus_CMS_UI_Admin($this->getRequest());
		}
		else
		{
			$this->ui = new Eresus_CMS_UI_Client($this->getRequest());
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает объект запроса к CMS
	 *
	 * @return Eresus_CMS_Request
	 *
	 * @since 2.16
	 */
	protected function createRequest()
	{
		$req = Eresus_HTTP_Message::fromEnv(Eresus_HTTP_Message::TYPE_REQUEST);
		/*
		 * FIXME Нельзя передавать здесь корень сайта на основе модели сайта. Наоборот, модель сайта
		 * должна выбираться на основе адреса.
		 */
		return new Eresus_CMS_Request($req, $cms->getSite()->getRootURL());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация сессии
	 *
	 * @return void
	 * @uses Eresus_Logger::log()
	 * @uses Eresus_Service_Auth::getInstance()
	 */
	private function initSession()
	{
		//session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
		ini_set('session.use_only_cookies', true);
		session_name('sid');
		// Проверка на CLI для юнит-тестов
		Eresus_Kernel_PHP::isCLI() || session_start();

		Eresus_Service_Auth::getInstance()->init();
		$_SESSION['activity'] = time();
	}
	//-----------------------------------------------------------------------------
}
