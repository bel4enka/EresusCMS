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
 *
 * @package EresusCMS
 */
class TPlgMgr
{
	/**
	 * Уровень доступа к модулю
	 * @var int
	 */
	private $access = ADMIN;

	private function toggle()
	{
		global $page, $Eresus;

		$Eresus->db->update('plugins', "`active` = NOT `active`", "`name`='".$Eresus->request['arg']['toggle']."'");
		$item = $Eresus->db->selectItem('plugins', "`name`='".$Eresus->request['arg']['toggle']."'");
		HTTP::redirect($page->url());
	}

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

	/**
	 * Подключает плагины
	 *
	 * @return void
	 * @see add()
	 */
	private function insert()
	{
		global $page, $Eresus;

		$files = arg('files');
		if ($files && is_array($files))
		{
			foreach ($files as $plugin => $install)
			{
				if ($install)
					$Eresus->plugins->install($plugin);
			}

		}
		HttpResponse::redirect('admin.php?mod=plgmgr');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает диалог добавления плагина
	 *
	 * @return string  HTML
	 */
	private function add()
	{
		global $page, $Eresus;

		$data = array();

		/*
		 * Составляем список доступных плагинов
		 */
		$files = glob($Eresus->froot . 'ext/*.php');
		if ($files === false)
		{
			$files = array();
		}

		/*
		 * Составляем список установленных плагинов
		 */
		$items = $Eresus->db->select('`plugins`', '', "`name`");
		$installed = array();
		foreach ($items as $item)
		{
			$installed []= $Eresus->froot . 'ext/' . $item['name'] . '.php';
		}

		// Оставляем только неустановленные
		$files = array_diff($files, $installed);

		/*
		 * Собираем информацию о неустановленных плагинах
		 */
		$data['plugins'] = array();
		if (count($files))
		{
			foreach ($files as $file)
			{
				$plugin = array('errors' => array());
				// Считываем исходник
				$s = file_get_contents($file);
				// Имя плагина должно совпадать с именем файла
				$plugin['name'] = basename($file, '.php');
				// Если нет класса "ИмяПлагина" или "TИмяПлагина" (старая форма) - это не файла плагина
				if (preg_match('/class\s+T?' . $plugin['name'] . '\s.*?{(.*?)({|})/is',	$s, $s))
				{
					// $s теперь содержит исходник плагина
					$s = $s[1];

					/* Ищем нужные свойства */
					preg_match('/\$kernel\s*=\s*(\'|")(.+)\1/', $s, $kernel);
					preg_match('/\$version\s*=\s*(\'|")(.+)\1/', $s, $version);
					preg_match('/\$title\s*=\s*(\'|")(.+)\1/', $s, $title);
					preg_match('/\$description\s*=\s*(\'|")(.+)\1/', $s, $description);

					// FIXME: Совместимость с версиями до 2.10b2. Надо сделать проверку на наличие $kernel
					if (count($version) && count($title) && count($description))
					{
						$plugin['title'] = $title[2];
						$plugin['version'] = $version[2];
						$plugin['description'] = $description[2];
					}
					else
					{
						$invalid = admPluginsNotRequiredFields;
					}

					/* PHP < 5.3 не понимает "rc", только "RC", но остальные буквы должны быть только
					 * в нижнем регистре
					 */
					if (isset($kernel[2]))
					{
						$plugin['kernel'] =  str_replace('rc','RC', $kernel[2]);
					}
					else
					{
						$plugin['kernel'] =  str_replace('rc','RC', $kernel);
					}

					$kernelVersion = str_replace('rc','RC', CMSVERSION);
					if (
						count($kernel) &&
						version_compare($v_plugin, $v_kernel, '>')
					)
					{
						$msg =  I18n::getInstance()->getText('admPluginsInvalidVersion', $this);
						$plugin['errors'] []= sprintf($msg, $plugin['kernel']);
					}
				}
				else
				{
					$msg =  I18n::getInstance()->getText('Class "%s" not found in plugin file', $this);
					$plugin['errors'] []= sprintf($msg, $plugin['name']);
				}
				$data['plugins'] []= $plugin;
			}
		}

		$tmpl = $page->getUITheme()->getTemplate('PluginManager/add-dialog.html');
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	private function up()
	{
		global $page, $Eresus;

		dbReorderItems('plugins','','name');
		$item = $Eresus->db->selectItem('plugins', "`name`='".arg('up', 'dbsafe')."'");
		if ($item['position'] > 0) {
			$Eresus->db->update('plugins', "`position` = `position`+1", "`position` = '".($item['position']-1)."'");
			$Eresus->db->update('plugins', "`position` = `position`-1", "`name` = '".$item['name']."'");
		}
		HTTP::redirect($page->url());
	}

	private function down()
	{
		global $page, $Eresus;

		dbReorderItems('plugins','','name');
		$item = $Eresus->db->selectItem('plugins', "`name`='".arg('down', 'dbsafe')."'");
		if ($item['position'] < $Eresus->db->count('plugins')-1) {
			$Eresus->db->update('plugins', "`position` = `position`-1", "`position` = '".($item['position']+1)."'");
			$Eresus->db->update('plugins', "`position` = `position`+1", "`name` = '".$item['name']."'");
		}
		HTTP::redirect($page->url());
	}

	/**
	 * Отрисовка контента модуля
	 *
	 * @return string
	 */
	public function adminRender()
	{
		global $page, $Eresus;

		if (!UserRights($this->access))
			return '';

		$result = '';
		$page->title = admPlugins;

		switch (true)
		{
			case arg('update') !== null:
				$this->update();
			break;

			case arg('toggle') !== null:
				$this->toggle();
			break;

			case arg('delete') !== null:
				$this->delete();
			break;

			case arg('id') !== null:
				$result = $this->edit();
			break;

			case arg('up') !== null:
				$this->up();
			break;

			case arg('down') !== null:
				$this->down();
			break;

			case arg('action') == 'add':
				$result = $this->add();
			break;

			case arg('action') == 'insert':
				$this->insert();
			break;

			default:
				$table = array (
					'name' => 'plugins',
					'key' => 'name',
					'sortMode' => 'position',
					'columns' => array(
						array('name' => 'title', 'caption' => admPlugin, 'width' => '90px', 'wrap'=>false),
						array('name' => 'description', 'caption' => admDescription),
						array('name' => 'version', 'caption' => admVersion, 'width'=>'70px','align'=>'center'),
					),
					'controls' => array (
						'delete' => '',
						'edit' => '',
						'toggle' => '',
						'position' => ''
					),
					'tabs' => array(
						'width'=>'180px',
						'items'=>array(
							array('caption'=>admPluginsAdd, 'name'=>'action', 'value'=>'add')
						)
					)
				);
				$result = $page->renderTable($table);
			break;
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
}
