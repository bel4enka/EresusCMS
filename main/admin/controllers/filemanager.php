<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Файлвовый менеджер
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
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
 *
 * $Id: AuthService.php 1277 2010-12-10 12:31:54Z mk $
 */

/**
 * Файлвовый менеджер
 *
 * @package EresusCMS
 * @since 2.16
 */
class EresusFileManagerController extends EresusAdminController
{
	/**
	 * (non-PHPdoc)
	 * @see AdminModule::actionIndex()
	 */
	public function actionIndex($params = array())
	{
		$connector = new elFinderConnector();
		$html = $connector->getDataBrowser();
		return $html;
	}
	//-----------------------------------------------------------------------------
}
