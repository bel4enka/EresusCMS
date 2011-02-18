<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Устаревший файл модуля "Файловый менеджер". В следующих версиях будет удалён.
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
 * @package BusinessLogic
 * @deprecated
 *
 * $Id$
 */

/**
 * Файловый менеджер
 *
 * @package BusinessLogic
 * @deprecated с 2.16
 */
class TFiles
{
	/**
	 * Выполняет переадресацию на новый модуль
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @uses HttpResponse::redirect()
	 */
	public function adminRender()
	{
		HttpResponse::redirect($GLOBALS['Eresus']->root . 'admin/filemanager/');
	}
}
