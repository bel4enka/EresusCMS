<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * Класс-коннектор
 *
 */
class XinhaConnector extends EresusExtensionConnector
{
	function forms_html($form, $field)
	{
		global $Eresus, $page, $locale;

    $value = isset($form->values[$field['name']]) ? $form->values[$field['name']] : (isset($field['value'])?$field['value']:'');
    $result = "\t\t".'<tr><td colspan="2">'.$field['label'].'<br /><textarea name="wyswyg_'.$field['name'].'" id="wyswyg_'.$field['name'].'" style="width: 100%; height: '.$field['height'].';">'.str_replace('$(httpRoot)', $Eresus->root, EncodeHTML($value)).'</textarea></td></tr>'."\n";

    $page->addScripts(
      'var _editor_url  = "'.$this->root.'";'."\n".
      'var _editor_lang = "'.$locale['lang'].'";'."\n".
      'var _editor_skin = "";'."\n".
      "var xinha_editors = ['wyswyg_".$field['name']."'];\n"
    );
    $page->linkScripts($this->root.'htmlarea.js');
    $page->linkScripts($this->root.'editor.js');

		return $result;
	}
	//-----------------------------------------------------------------------------
}

?>