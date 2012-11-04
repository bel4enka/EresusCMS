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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Eresus\CmsBundle\Extensions\Plugin;

/**
 *
 * @package Eresus
 */
class Eresus_Admin_Controllers_Plgmgr
{
	/**
	 * Уровень доступа к модулю
	 * @var int
	 */
	private $access = ADMIN;

	/**
	 * Включает или отключает плагин
	 *
	 * @return Response
	 */
	private function toggle()
	{
		$q = DB::getHandler()->createUpdateQuery();
		$e = $q->expr;
		$q->update('plugins')
			->set('active', $e->not('active'))
			->where(
				$e->eq('name', $q->bindValue(arg('toggle')))
			);
		DB::execute($q);

        return new RedirectResponse(Eresus_Kernel::app()->getPage()->url());
	}

    /**
     * @return Response
     */
    private function delete()
    {
        Eresus_CMS::getLegacyKernel()->plugins->load(arg('delete'));
        Eresus_CMS::getLegacyKernel()->plugins->uninstall(arg('delete'));
        return new RedirectResponse(Eresus_Kernel::app()->getPage()->url());
    }

	private function edit()
	{
		Eresus_CMS::getLegacyKernel()->plugins->load(arg('id'));
		if (method_exists(Eresus_CMS::getLegacyKernel()->plugins->items[arg('id')], 'settings'))
		{
			$result = Eresus_CMS::getLegacyKernel()->plugins->items[arg('id', 'word')]->settings();
		}
		else
		{
			$form = array(
				'name' => 'InfoWindow',
				'caption' => Eresus_Kernel::app()->getPage()->title,
				'width' => '300px',
				'fields' => array (
					array('type'=>'text','value'=>
					'<div align="center"><strong>Этот плагин не имеет настроек</strong></div>'),
				),
				'buttons' => array('cancel'),
			);
			$result = Eresus_Kernel::app()->getPage()->renderForm($form);
		}
		return $result;
	}

    /**
     * @return Response
     */
    private function update()
	{
		Eresus_CMS::getLegacyKernel()->plugins->load(arg('update'));
		Eresus_CMS::getLegacyKernel()->plugins->items[arg('update')]->updateSettings();
		return new RedirectResponse(arg('submitURL'));
	}

	/**
	 * Подключает плагины
	 *
	 * @return Response
	 */
	private function insert()
	{
		$files = arg('files');
		if ($files && is_array($files))
		{
			foreach ($files as $plugin => $install)
			{
				if ($install)
				{
					try
					{
						Eresus_CMS::getLegacyKernel()->plugins->install($plugin);
					}
					catch (DomainException $e)
					{
						ErrorMessage($e->getMessage());
					}
				}
			}

		}
        return new RedirectResponse('admin.php?mod=plgmgr');
	}

	/**
	 * Возвращает диалог добавления плагина
	 *
	 * @return string  HTML
	 */
	private function add()
	{
		$data = array();

		/* Составляем список доступных плагинов */
		$files = glob(Eresus_CMS::getLegacyKernel()->froot . 'ext/*.php');
		if (false === $files)
		{
			$files = array();
		}

		/* Составляем список установленных плагинов */
		$items = Eresus_CMS::getLegacyKernel()->db->select('plugins', '', 'name, version');
		$installed = array();
		foreach ($items as $item)
		{
			$installed []= Eresus_CMS::getLegacyKernel()->froot . 'ext/' . $item['name'] . '.php';
		}
		// Оставляем только неустановленные
		$files = array_diff($files, $installed);

		/*
		 * Собираем информацию о неустановленных плагинах
		 */
		$data['plugins'] = array();
		if (count($files))
		{
			// Удаляем из версии CMS все буквы, чтобы сравнивать только цифры
			$kernelVersion = preg_replace('/[^\d\.]/', '', CMSVERSION);

			foreach ($files as $file)
			{
				$errors = array();
				try
				{
					$info = Eresus_PluginInfo::loadFromFile($file);
					$required = $info->getRequiredKernel();
					if (
						version_compare($kernelVersion, $required[0], '<')/* ||
						version_compare($kernelVersion, $required[1], '>')*/
					)
					{
						$msg =  Eresus_I18n::getInstance()->getText('admPluginsInvalidVersion', $this);
						$errors []= sprintf($msg, /*implode(' - ', */$required[0]/*)*/);
					}
				}
				catch (RuntimeException $e)
				{
					$errors []= $e->getMessage();
					$info = new Plugin();
					$info->title = $info->name = basename($file, '.php');
					$info->version = '';
				}
				$available[$info->name] = $info->version;
				$data['plugins'][$info->title] = array('info' => $info, 'errors' => $errors);
			}
		}

		$plugins = Eresus_CMS::getLegacyKernel()->plugins;

		foreach ($data['plugins'] as &$item)
		{
			if ($item['info'] instanceof Eresus_PluginInfo)
			{
				$required = $item['info']->getRequiredPlugins();
				foreach ($required as $plugin)
				{
					list ($name, $minVer, $maxVer) = $plugin;
					if (
						!isset($plugins->items[$name]) ||
						($minVer && version_compare($plugins->items[$name]->version, $minVer, '<')) ||
						($maxVer && version_compare($plugins->items[$name]->version, $maxVer, '>'))
					)
					{
						{
							$msg = Eresus_I18n::getInstance()->getText('Requires plugin: %s', $this);
							$item['errors'] []= sprintf($msg, $name . ' ' . $minVer . '-' . $maxVer);
						}
					}
				}
			}
		}

		ksort($data['plugins']);

		/* @var Eresus_AdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$tmpl = $page->getUITheme()->getTemplate('PluginManager/add-dialog.html');
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовка контента модуля
	 *
	 * @return Response|string
	 */
	public function adminRender()
	{
		if (!UserRights($this->access))
		{
			return '';
		}

		$result = '';
		Eresus_Kernel::app()->getPage()->title = admPlugins;

		switch (true)
		{
			case arg('update') !== null:
				return $this->update();
			    break;

			case arg('toggle') !== null:
				return $this->toggle();
			break;

			case arg('delete') !== null:
				return $this->delete();
			    break;

			case arg('id') !== null:
				$result = $this->edit();
			break;

			case arg('action') == 'add':
				$result = $this->add();
			break;

			case arg('action') == 'insert':
				return $this->insert();
			break;

			default:
				$table = array (
					'name' => 'plugins',
					'key' => 'name',
					'sortMode' => 'title',
					'columns' => array(
						array('name' => 'title', 'caption' => admPlugin, 'width' => '90px', 'wrap'=>false),
						array('name' => 'description', 'caption' => admDescription),
						array('name' => 'version', 'caption' => admVersion, 'width'=>'70px','align'=>'center'),
					),
					'controls' => array (
						'delete' => '',
						'edit' => '',
						'toggle' => '',
					),
					'tabs' => array(
						'width'=>'180px',
						'items'=>array(
							array('caption'=>admPluginsAdd, 'name'=>'action', 'value'=>'add')
						)
					)
				);
				$result = Eresus_Kernel::app()->getPage()->renderTable($table);
			break;
		}
		return $result;
	}
}
