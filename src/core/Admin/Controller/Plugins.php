<?php
/**
 * ${product.title}
 *
 * Управление модулями расширения
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
 * $Id$
 */


/**
 * Управление модулями расширения
 *
 * @package Eresus
 * @since 2.17
 */
class Eresus_Admin_Controller_Plugins extends Eresus_Admin_Controller
{
	/**
	 * Возвращает разметку списка расширений
	 *
	 * @return string  HTML
	 *
	 * @since 2.17
	 */
	public function showListAction()
	{
		$plugins = Doctrine_Core::getTable('Eresus_Entity_Plugin')->findAll();
		$provider = new Eresus_UI_List_DataProvider_Array($plugins->getData(),
			array('id' => 'uid', 'enabled' => 'active'));
		$list = new Eresus_UI_List($provider);
		$tmpl = Eresus_Template::fromFile('core/templates/plugins/list.html');
		return $tmpl->compile(array('list' => $list));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Включает и отключает расширения
	 *
	 * @return string  HTML
	 *
	 * @since 2.17
	 */
	public function toggleAction()
	{
		$uid = arg('id');
		$vars = array();
		$plugin = Doctrine_Core::getTable('Eresus_Entity_Plugin')->find($uid);
		if (false === $plugin)
		{
			throw new Eresus_CMS_Exception_NotFound;
		}

		if ($plugin->active)
		{
			$vars['dependent'] = $this->container->plugins->getDependent($uid);
			if ($vars['dependent'])
			{
				if (!arg('confirmed'))
				{
					$vars['deletion'] = false;
					$vars['plugin'] = $plugin->object;
					$tmpl = Eresus_Template::fromFile('core/templates/plugins/confirm.html');
					$html = $tmpl->compile($vars);
					return $html;
				}
				else
				{

				}
			}
		}

		$plugin->active = ! $plugin->active;
		$plugin->save();
		HTTP::redirect('admin.php?mod=plgmgr');
	}
	//-----------------------------------------------------------------------------
}
