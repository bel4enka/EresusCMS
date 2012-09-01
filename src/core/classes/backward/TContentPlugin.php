<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * $Id$
 */

/**
* Базовый класс для плагинов, предоставляющих тип контента
*
* @package Eresus
*/
class TContentPlugin extends TPlugin
{
	/**
	 * Конструктор
	 *
	 * Устанавливает плагин в качестве плагина контента и читает локальные настройки
	 */
	function __construct()
	{
	  parent::__construct();
		Eresus_Kernel::app()->getPage()->plugin = $this->name;
    if (count(Eresus_Kernel::app()->getPage()->options))
    {
      foreach (Eresus_Kernel::app()->getPage()->options as $key=>$value)
      {
        $this->settings[$key] = $value;
      }
    }
	}
	//------------------------------------------------------------------------------
/**
* Обновляет контент страницы в БД
*
* @param  string  $content  Контент
*/
function updateContent($content)
{
  $item = Eresus_CMS::getLegacyKernel()->db->
	  selectItem('pages', "`id`='".Eresus_Kernel::app()->getPage()->id."'");
  $item['content'] = $content;
	Eresus_CMS::getLegacyKernel()->db->
		updateItem('pages', $item, "`id`='".Eresus_Kernel::app()->getPage()->id."'");
}
//------------------------------------------------------------------------------
/**
* Обновляет контент страницы
*/
function update()
{
	$this->updateContent(arg('content', 'dbsafe'));
  HTTP::redirect(arg('submitURL'));
}
//------------------------------------------------------------------------------
/**
* Отрисовка клиентской части
*
* @return  string  Контент
*/
function clientRenderContent()
{
  return Eresus_Kernel::app()->getPage()->content;
}
//------------------------------------------------------------------------------
/**
* Отрисовка административной части
*
* @return  string  Контент
*/
function adminRenderContent()
{
  $item = Eresus_CMS::getLegacyKernel()->db->selectItem('pages', "`id`='".
	  Eresus_Kernel::app()->getPage()->id."'");
  $form = array(
    'name' => 'content',
    'caption' => Eresus_Kernel::app()->getPage()->title,
    'width' => '100%',
    'fields' => array (
      array ('type'=>'hidden','name'=>'update'),
      array ('type' => 'memo', 'name' => 'content', 'label' => strEdit, 'height' => '30'),
    ),
    'buttons' => array('apply', 'reset'),
  );

  $result = Eresus_Kernel::app()->getPage()->renderForm($form, $item);
  return $result;
}
//------------------------------------------------------------------------------
}
