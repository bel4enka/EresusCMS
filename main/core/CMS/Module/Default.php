<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модуль "По умолчанию"
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
 * $Id: ContentPlugin.php 1653 2011-06-16 06:53:17Z mk $
 */


/**
 * Модуль "По умолчанию"
 *
 * @package CMS
 */
class Eresus_CMS_Module_Default extends Eresus_CMS_ContentPlugin
{
	/**
	 * Действия при удалении раздела данного типа
	 * @param int     $id     Идентификатор удаляемого раздела
	 * @param string  $table  Имя таблицы
	 */
	public function onSectionDelete($id, $table = '')
	{
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обновляет контент страницы в БД
	 *
	 * @param  string  $content  Контент
	 */
	public function updateContent($content)
	{
		global $Eresus, $page;

		$item = Eresus_DB_ORM::getTable('Eresus_Model_Section')->find($page->id);
		$item->content = $content;
		$item->save();
	}
	//------------------------------------------------------------------------------

	/**
	* Обновляет контент страницы
	*/
	function adminUpdate()
	{
		$this->updateContent(arg('content', 'dbsafe'));
		HttpResponse::redirect(arg('submitURL'));
	}
	//------------------------------------------------------------------------------

	/**
	 * Отрисовывает область контента указанного раздела сайта
	 *
	 * @param Eresus_Model_Section $section  раздел, для которого надо отрисовать контент
	 *
	 * @return string  Контент
	 */
	public function clientRenderContent(Eresus_Model_Section $section)
	{
		/* Если в URL указано что-либо кроме адреса раздела, отправляет ответ 404 * /
		if ($Eresus->request['file'] || $Eresus->request['query'] || $page->subpage || $page->topic)
			$page->httpError(404);*/

		return $section->getContent();
	}
	//------------------------------------------------------------------------------

	/**
	 * Отрисовка административной части
	 *
	 * @return  string  Контент
	 */
	public function adminRenderContent()
	{
		global $page, $Eresus;

		if (arg('action') == 'update')
		{
			$this->adminUpdate();
		}
		$item = Eresus_DB_ORM::getTable('Eresus_Model_Section')->find($page->id);
		$form = array(
			'name' => 'editForm',
			'caption' => $page->title,
			'width' => '100%',
			'fields' => array (
				array ('type'=>'hidden','name'=>'action', 'value' => 'update'),
				array ('type' => 'memo', 'name' => 'content', 'label' => strEdit, 'height' => '30'),
			),
			'buttons' => array('apply', 'reset'),
		);

		$result = $page->renderForm($form, $item);
		return $result;
	}
	//------------------------------------------------------------------------------
}
