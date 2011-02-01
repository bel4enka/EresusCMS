<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Коннектор для TinyMCE
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * @package CoreExtensionsAPI
 *
 * $Id$
 */

/**
 * Класс-коннектор
 *
 * @package CoreExtensionsAPI
 */
class TinyMCEConnector extends EresusExtensionConnector
{
	/**
	 *
	 * @param Form  $form
	 * @param array $field
	 * @return array
	 */
	function forms_html($form, $field)
	{
		global $Eresus, $page, $locale;

		$value = isset($form->values[$field['name']]) ? $form->values[$field['name']] : (isset($field['value'])?$field['value']:'');
		$preset = isset($field['preset']) ? $field['preset'] : 'default';
		$result = "\t\t".'<tr><td colspan="2">'.$field['label'].'<br /><textarea name="wyswyg_'.$field['name'].'" class="tinymce_'.$preset.'" cols="80" rows="25" style="width: 100%; height: '.$field['height'].';">'.str_replace('$(httpRoot)', $Eresus->root, EncodeHTML($value)).'</textarea></td></tr>'."\n";

		$page->linkScripts($this->getRoot() . 'tiny_mce.js');
		$page->linkScripts($this->getRoot() . 'presets/' . $preset . '.js');

		return $result;
	}
	//-----------------------------------------------------------------------------
}
