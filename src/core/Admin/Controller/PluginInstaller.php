<?php
/**
 * ${product.title}
 *
 * Установщик модулей расширения
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
 * Установщик модулей расширения
 *
 * @package Eresus
 * @since 2.17
 */
class Eresus_Admin_Controller_PluginInstaller extends Eresus_Admin_Controller
{
	/**
	 * Возвращает разметку диалога выбора модулей для подключения
	 *
	 * @return string  HTML
	 *
	 * @since 2.17
	 */
	public function showSelectorDialogAction()
	{
		// Переменные для шаблона
		$vars = array('plugins' => array());

		$existed = $this->getLocalPlugins();
		$installed = $this->getInstalledPlugins();
		$notInstalled = $this->filterInstalled($existed, $installed);

		if (count($notInstalled))
		{
			// Удаляем из версии ядра все буквы, чтобы сравнивать только цифры
			$kernelVersion = preg_replace('/[^\d\.]/', '', $this->container->app->getVersion());

			/*
			 * Собираем информацию о неустановленных плагинах
			 */
			foreach ($notInstalled as $plugin)
			{
				$info = array(
					'info' => $plugin,
					'cms' => null,
					'requires' => null,
				);
				$required = $plugin->getRequiredKernel();
				if (
					version_compare($kernelVersion, $required['min'], '<') ||
					version_compare($kernelVersion, $required['max'], '>')
				)
				{
					$info['cms'] = $required;
				}

				$reqs = $plugin->requiredPlugins;
				foreach ($reqs as $required)
				{
					if (!$this->container->plugins->
						isAvailable($required['uid'], $required['min'], $required['max']))
					{
						$info['requires'] []= $required;
					}
				}
				$info['errors'] = (boolean) ($info['cms'] || $info['requires']);
				$vars['plugins'][$plugin->title] = $info;
			}
		}

		ksort($vars['plugins']);

		$tmpl = $GLOBALS['page']->getUITheme()->getTemplate('PluginManager/add-dialog.html');
		$html = $tmpl->compile($vars);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает расширения, выбранные в диалоге добавления
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	public function installAction()
	{
		$toInstall = arg('plugins');
		if (is_array($toInstall))
		{
			$rootPath = $this->container->app->getRootDir() . '/plugins/';
			foreach ($toInstall as $name)
			{
				$filename = $rootPath . $name. '/plugin.xml';
				if (file_exists($filename))
				{
					$plugin = Eresus_Plugin::loadFromFile($filename);
					$entity = new Eresus_Entity_Plugin();
					$entity->name = $plugin->name;
					$entity->save();
				}
				else
				{
					eresus_log(__METHOD__, LOG_ERR, 'Can not find file "%s" for plugin "%s"', $filename,
						$name);
					$msg = i18n('Не удалось найти файл "%s" для расширения "%s".', __CLASS__);
					$msg = sprintf($msg, $filename, $name);
					//FIXME ErrorMessage($msg);
				}
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список доступных локально плагинов
	 *
	 * @return Eresus_Plugin[]
	 *
	 * @since 2.17
	 */
	private function getLocalPlugins()
	{
		$plugins = array();
		$it = new DirectoryIterator($this->container->app->getRootDir() . '/plugins');
		foreach ($it as $fileInfo)
		{
			if ($fileInfo->isDir() && $fileInfo->getFilename() != '.' && $fileInfo->getFilename() != '..')
			{
				$filename = $fileInfo->getPathname() . '/plugin.xml';
				if (file_exists($filename))
				{
					$info = Eresus_Plugin::loadFromFile($filename);
					$plugins[$info->getUID()] = $info;
				}
			}
		}
		return $plugins;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список установленных расширений
	 *
	 * @return array
	 *
	 * @since 2.17
	 */
	private function getInstalledPlugins()
	{
		$plugins = Doctrine_Core::getTable('Eresus_Entity_Plugin')->findAll();

		$array = array();
		foreach ($plugins as $plugin)
		{
			$array[$plugin->uid] = $plugin;
		}
		return $array;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отбрасывает из списка плагинов уже установленные
	 *
	 * @param array $existed    все доступные плагины
	 * @param array $installed  установленные плагины
	 *
	 * @return array
	 *
	 * @since 2.17
	 */
	private function filterInstalled(array $existed, array $installed)
	{
		// FIXME TODO
		return $existed;
	}
	//-----------------------------------------------------------------------------
}
