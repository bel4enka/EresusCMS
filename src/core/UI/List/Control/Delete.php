<?php
/**
 * ${product.title}
 *
 * Действие «Удалить»
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
 * $Id: Kernel.php 1978 2011-11-22 14:49:17Z mk $
 */


/**
 * Действие «Удалить»
 *
 * @package Eresus
 * @since 2.17
 */
class Eresus_UI_List_Control_Delete extends Eresus_UI_List_Control
{
	/**
	 * @see Eresus_UI_List_Control::render()
	 */
	public function render(Eresus_UI_List_Item_Interface $item)
	{
		return '<a href="' . $this->list->getURL()->getDelete($item) . '" title="' . i18n('Удалить') .
			'" onclick="return askdel(this);"><img src="' . $GLOBALS['Eresus']->root .
			$GLOBALS['page']->getUITheme()->getIcon('/actions/edit-delete.png') . '" alt="' . i18n('Удалить') .
			'"></a> ';
	}
	//-----------------------------------------------------------------------------
}