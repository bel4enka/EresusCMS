<?php
/**
 * ${product.title}
 *
 * Плагин «theme» для шаблонизатора
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
 * @subpackage Dwoo
 *
 * $Id: Admin.php 1761 2011-07-31 06:47:24Z mk $
 */


/**
 * Возвращает путь к ресурсу темы оформления АИ
 *
 * Пример:
 * <code>
 * {theme '/path/image.png'}
 * </code>
 *
 * @param Dwoo   $dwoo
 * @param string $path
 *
 * @return string
 *
 * @since 2.16
 */
function Dwoo_Plugin_theme(Dwoo $dwoo, $path = '')
{
	$ui = Eresus_CMS_UI::getInstance();
	if ($ui instanceof Eresus_CMS_UI_Admin)
	{
		return Eresus_CMS_Request::getInstance()->getRootPrefix() . $ui->getTheme()->getResource($path);
	}
	return '';
}
