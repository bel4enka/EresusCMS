<?php
/**
 * ${product.title}
 *
 * Базовый класс для плагинов, предоставляющих тип контента
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
 */

/**
 * Базовый класс для плагинов, предоставляющих тип контента
 *
 * @package Eresus
 */
class ContentPlugin extends Plugin
{
	/**
	 * Конструктор
	 *
	 * Устанавливает плагин в качестве плагина контента и читает локальные настройки
	 */
	public function __construct()
	{
		parent::__construct();

		/* @var TClientUI $page */
		$page = Eresus_Kernel::app()->getPage();
		if ($page instanceof TClientUI)
		{
			$page->plugin = $this->name;
			if (isset($page->options) && count($page->options))
			{
				foreach ($page->options as $key=>$value)
				{
					$this->settings[$key] = $value;
				}
			}
		}
	}

	/**
	 * Возвращает информацию о плагине
	 *
	 * @param  array  $item  Предыдущая версия информации (по умолчанию null)
	 *
	 * @return  array  Массив информации, пригодный для записи в БД
	 */
	public function __item($item = null)
	{
		$result = parent::__item($item);
		$result['content'] = true;
		return $result;
	}

	/**
	 * Действия при удалении раздела данного типа
	 * @param int     $id     Идентификатор удаляемого раздела
	 * @param string  $table  Имя таблицы
	 */
	public function onSectionDelete($id, $table = '')
	{
		if (count($this->dbTable($table)))
		{
			$this->dbDelete($table, $id, 'section');
		}
	}

	/**
	 * Обновляет контент страницы в БД
	 *
	 * @param  string  $content  Контент
	 */
	public function updateContent($content)
	{
		$item = Eresus_CMS::getLegacyKernel()->db->
			selectItem('pages', "`id`='".Eresus_Kernel::app()->getPage()->id."'");
		$item['content'] = $content;
		Eresus_CMS::getLegacyKernel()->db->
			updateItem('pages', $item, "`id`='".Eresus_Kernel::app()->getPage()->id."'");
	}

	/**
	 * Обновляет контент страницы
	 */
	public function adminUpdate()
	{
		$this->updateContent(arg('content', 'dbsafe'));
		HTTP::redirect(arg('submitURL'));
	}

	/**
	 * Отрисовка клиентской части
	 *
	 * @return  string  Контент
	 */
	public function clientRenderContent()
	{
		/** @var TClientUI $page */
		$page = Eresus_Kernel::app()->getPage();
		/* Если в URL указано что-либо кроме адреса раздела, отправляет ответ 404 */
		if (Eresus_CMS::getLegacyKernel()->request['file'] ||
			Eresus_CMS::getLegacyKernel()->request['query'] ||
			$page->subpage || $page->topic)
		{
			$page->httpError(404);
		}

		return $page->content;
	}

	/**
	 * Отрисовка административной части
	 *
	 * @return  string  Контент
	 */
	public function adminRenderContent()
	{
		if (arg('action') == 'update')
		{
			$this->adminUpdate();
		}
		/** @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$item = Eresus_CMS::getLegacyKernel()->db->selectItem('pages', "`id`='" . $page->id . "'");
		$form = array(
			'name' => 'editForm',
			'caption' => Eresus_Kernel::app()->getPage()->title,
			'width' => '100%',
			'fields' => array (
				array ('type'=>'hidden','name'=>'action', 'value' => 'update'),
				array ('type' => 'memo', 'name' => 'content', 'label' => STR_EDIT, 'height' => '30'),
			),
			'buttons' => array('apply', 'reset'),
		);

		$result = $page->renderForm($form, $item);
		return $result;
	}
}