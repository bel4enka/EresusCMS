<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
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
 * @package BusinessLogic
 *
 * $Id$
 */

/**
 * Управление контентом
 *
 * @package BusinessLogic
 */
class Eresus_Controller_Admin_Content extends Eresus_Controller_Admin_Abstract
{
	/**
	 * Описание раздела
	 * @var array
	 */
	private $item;

	/**
	 * Возвращает разметку интерфейса управления контентом текущего раздела
	 *
	 * @return string  HTML
	 * @uses EresusAdminFrontController::setController()
	 */
	public function actionIndex()
	{
		if (!UserRights(EDITOR))
		{
			return '';
		}

		$plugins = $GLOBALS['Eresus']->plugins;
		$plugin = null;

		$this->item = EresusORM::getTable('Eresus_Model_Section')->find(arg('section', 'int'));

		$GLOBALS['page']->id = $this->item['id'];

		if (!array_key_exists($this->item['type'], $plugins->list))
		{
			switch ($this->item['type'])
			{
				case 'default':
					$html = $this->contentTypeDefault();
				break;

				case 'list':
					$html = $this->contentTypeList();
				break;

				case 'url':
					$html = $this->contentTypeURL();
				break;

				default:
					$html = $GLOBALS['page']->box(sprintf(errContentPluginNotFound, $this->item['type']),
						'errorBox', errError);
				break;
			}
		}
		else
		{
			$plugins->load($this->item['type']);
			$plugin = $plugins->items[$this->item['type']];
			Eresus_CMS::app()->getFrontController()->setController($plugin);
			$html = $plugin->adminRenderContent();
		}

		$tmpl = Eresus_Template::fromFile('core/templates/ContentEditor/common.html');
		$data = array(
			'editor' => $html,
			'contentURL' => '#',
			'propertiesURL' => 'admin.php?mod=pages&id=' . arg('section', 'int'),
			'plugin' => $plugin,
			'clientURL' => $GLOBALS['page']->clientURL(arg('section', 'int'))
		);
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Редактор для разделов типа "По умолчанию"
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	private function contentTypeDefault()
	{
		$editor = new ContentPlugin();
		if (arg('update'))
		{
			$editor->update();
		}
		else
		{
			return $editor->adminRenderContent();
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Редактор для разделов типа "Список подразделов"
	 *
	 * @return string
	 *
	 * @since 2.16
	 * @uses HttpResponse::redirect()
	 */
	private function contentTypeList()
	{
		if (arg('action') == 'update')
		{
			$this->item['content'] = arg('content', 'dbsafe');
			$GLOBALS['Eresus']->sections->update($this->item);
			HttpResponse::redirect(arg('submitURL'));
		}
		else
		{
			$form = array(
				'name' => 'editURL',
				'caption' => admEdit,
				'width' => '100%',
				'fields' => array (
					array('type' => 'hidden', 'name' => 'section', 'value' => $this->item['id']),
					array('type' => 'hidden', 'name' => 'action', 'value' => 'update'),
					array ('type' => 'html', 'name' => 'content', 'label' => admTemplListLabel,
						'height' => '300px', 'value' => $this->item['content']),
				),
				'buttons' => array('apply', 'cancel'),
			);
			return $GLOBALS['page']->renderForm($form);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Редактор для разделов типа "URL"
	 *
	 * @return string
	 *
	 * @since 2.16
	 * @uses HttpResponse::redirect()
	 */
	private function contentTypeURL()
	{
		if (arg('update'))
		{
			$this->item->content = arg('url', 'dbsafe');
			$this->item->save();
			HttpResponse::redirect(arg('submitURL'));
		}
		else
		{
			$form = array(
				'name' => 'editURL',
				'caption' => admEdit,
				'width' => '100%',
				'fields' => array (
					array('type'=>'hidden','name'=>'update', 'value' => $this->item['id']),
					array ('type' => 'edit', 'name' => 'url', 'label' => 'URL:', 'width' => '100%',
						'value'=>isset($this->item['content']) ? $this->item['content']:''),
				),
				'buttons' => array('apply', 'cancel'),
			);
			return $GLOBALS['page']->renderForm($form);
		}
	}
	//-----------------------------------------------------------------------------
}