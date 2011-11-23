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
		$vars = array();

		$existed = $this->getLocalPlugins();
		$installed = $this->getInstalledPlugins();

		/* Оставляем только неустановленные */
		foreach ($installed as $plugin)
		{

		}

		/*
		 * Собираем информацию о неустановленных плагинах
		 */
		$data['plugins'] = array();
		$features = array();
		if (count($files))
		{
			foreach ($files as $file)
			{
				$errors = array();
				try
				{
					$info = Eresus_Plugin::loadFromFile($file);
					// Удаляем из версии ядра все буквы, чтобы сравнивать только цифры
					$kernelVersion = preg_replace('/[^\d\.]/', '', CMSVERSION);
					$required = $info->getRequiredKernel();
					if (
						version_compare($kernelVersion, $required[0], '<')/* ||
						version_compare($kernelVersion, $required[1], '>')*/
					)
					{
						$msg =  i18n('Требуется Eresus %s или выше.', __CLASS__);
						$errors []= sprintf($msg, /*implode(' - ', */$required[0]/*)*/);
					}
					/*}
					else
					{
						$msg =  I18n::getInstance()->getText('Class "%s" not found in plugin file', $this);
						$info['errors'] []= sprintf($msg, $info['name']);
					}*/
				}
				catch (RuntimeException $e)
				{
					$errors []= $e->getMessage();
					$info = new stdClass();
					$info->title = $info->name = basename($file, '.php');
					$info->version = '';
				}
				$available[$info->name] = $info->version;
				$data['plugins'][$info->title] = array('info' => $info, 'errors' => $errors);
			}
		}


		foreach ($data['plugins'] as &$item)
		{
			if ($item['info'] instanceof Eresus_Plugin)
			{
				$required = $item['info']->getRequiredPlugins();
				foreach ($required as $plugin)
				{
					list ($name, $minVer, $maxVer) = $plugin;
					if (
						!isset($available[$name]) ||
						($minVer && version_compare($available[$name], $minVer, '<')) ||
						($maxVer && version_compare($available[$name], $maxVer, '>'))
					)
					{
						{
							$msg = i18n('Требуется расширение %s', __CLASS__);
							$item['errors'] []= sprintf($msg, $name . ' ' . $minVer . '-' . $maxVer);
						}
					}
				}
			}
		}

		ksort($data['plugins']);

		$tmpl = $page->getUITheme()->getTemplate('PluginManager/add-dialog.html');
		$html = $tmpl->compile($data);

		return $html;
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
		return $plugins;
	}
	//-----------------------------------------------------------------------------
}
