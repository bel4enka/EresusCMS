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
		return new Eresus_CMS_Response('admin');
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
}
