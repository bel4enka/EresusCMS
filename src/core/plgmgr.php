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
 * @package Eresus
 *
 * $Id$
 */

/**
 *
 * @package Eresus
 */
class TPlgMgr
{
	/**
	 * Уровень доступа к модулю
	 * @var int
	 */
	private $access = ADMIN;

	/**
	 * Включает или отключает плагин
	 *
	 * @return void
	 */
	private function toggle()
	{
		global $page, $Eresus;

		$q = DB::getHandler()->createUpdateQuery();
		$e = $q->expr;
		$q->update('plugins')
			->set('active', $e->not('active'))
			->where(
				$e->eq('name', $q->bindValue(arg('toggle')))
			);
		DB::execute($q);

		HttpResponse::redirect($page->url());
	}
	//-----------------------------------------------------------------------------

	private function delete()
	{
	global $page, $Eresus;

		$Eresus->plugins->load($Eresus->request['arg']['delete']);
		$Eresus->plugins->uninstall($Eresus->request['arg']['delete']);
		HTTP::redirect($page->url());
	}

	private function edit()
	{
	global $page, $Eresus;

		$Eresus->plugins->load($Eresus->request['arg']['id']);
		if (method_exists($Eresus->plugins->items[$Eresus->request['arg']['id']], 'settings')) {
			$result = $Eresus->plugins->items[arg('id', 'word')]->settings();
		} else {
			$form = array(
				'name' => 'InfoWindow',
				'caption' => $page->title,
				'width' => '300px',
				'fields' => array (
					array('type'=>'text','value'=>'<div align="center"><strong>Этот плагин не имеет настроек</strong></div>'),
				),
				'buttons' => array('cancel'),
			);
			$result = $page->renderForm($form);
		}
		return $result;
	}

	private function update()
	{
	global $page, $Eresus;

		$Eresus->plugins->load($Eresus->request['arg']['update']);
		$Eresus->plugins->items[$Eresus->request['arg']['update']]->updateSettings();
		HTTP::redirect($Eresus->request['arg']['submitURL']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовка контента модуля
	 *
	 * @return string
	 */
	public function adminRender()
	{
		global $page, $Eresus;

		if (!UserRights($this->access))
		{
			eresus_log(__METHOD__, LOG_WARNING, 'Access denied for user "%s"', $Eresus->user['name']);
			return '';
		}

		eresus_log(__METHOD__, LOG_DEBUG, '()');

		$result = '';
		$page->title = i18n('Модули расширения', __CLASS__);

		switch (arg('action'))
		{
			case 'update':
				$this->update();
			break;

			case 'toggle':
				$ctrl = new Eresus_Admin_Controller_Plugins(Eresus_Kernel::sc());
				$result = $ctrl->toggleAction();
			break;

			case 'delete':
				$this->delete();
			break;

			case 'edit':
				$result = $this->edit();
			break;

			case 'add':
				$ctrl = new Eresus_Admin_Controller_PluginInstaller(Eresus_Kernel::sc());
				$result = $ctrl->showSelectorDialogAction();
			break;

			case 'insert':
				$ctrl = new Eresus_Admin_Controller_PluginInstaller(Eresus_Kernel::sc());
				$ctrl->installAction();
				HttpResponse::redirect('admin.php?mod=plgmgr');
			break;

			default:
				$ctrl = new Eresus_Admin_Controller_Plugins(Eresus_Kernel::sc());
				$result = $ctrl->showListAction();
			break;
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
}
