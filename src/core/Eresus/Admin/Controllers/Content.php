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

/**
 * Управление контентом
 *
 * @package Eresus
 */
class Eresus_Admin_Controllers_Content
{

	/**
	 * Возвращает разметку интерфейса управления контентом текущего раздела
	 *
	 * @return Response|string  HTML
	 */
	public function adminRender()
	{
		if (!UserRights(EDITOR))
		{
			return '';
		}
		$result = '';
		$sections = new Eresus_Sections();
		$item = $sections->get(arg('section', 'int'));

		Eresus_Kernel::app()->getPage()->id = $item['id'];
		if (!array_key_exists($item['type'], Eresus_CMS::getLegacyKernel()->plugins->list))
		{
			switch ($item['type'])
			{
				case 'default':
					$editor = new Eresus_Extensions_ContentPlugin();
					if (arg('update'))
					{
						$editor->update();
					}
					else
					{
						$result = $editor->adminRenderContent();
					}
				    break;

				case 'list':
					if (arg('update'))
					{
						$item['content'] = arg('content', 'dbsafe');
						/** @var Eresus_Sections $sections */
						$sections = Eresus_Kernel::get('sections');
						$sections->update($item);
						return new RedirectResponse(arg('submitURL'));
					}
					else
					{
						$form = array(
							'name' => 'editURL',
							'caption' => ADM_EDIT,
							'width' => '100%',
							'fields' => array (
								array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
								array ('type' => 'html', 'name' => 'content', 'label' => admTemplListLabel, 'height' => '300px', 'value'=>isset($item['content'])?$item['content']:'$(items)'),
							),
							'buttons' => array('apply', 'cancel'),
						);
						$result = Eresus_Kernel::app()->getPage()->renderForm($form);
					}
				break;

				case 'url':
					if (arg('update'))
					{
						$item['content'] = arg('url', 'dbsafe');
						/** @var Eresus_Sections $sections */
						$sections = Eresus_Kernel::get('sections');
						$sections->update($item);
						return new RedirectResponse(arg('submitURL'));
					}
					else
					{
						$form = array(
							'name' => 'editURL',
							'caption' => ADM_EDIT,
							'width' => '100%',
							'fields' => array (
								array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
								array ('type' => 'edit', 'name' => 'url', 'label' => 'URL:', 'width' => '100%', 'value'=>isset($item['content'])?$item['content']:''),
							),
							'buttons' => array('apply', 'cancel'),
						);
						$result = Eresus_Kernel::app()->getPage()->renderForm($form);
					}
				break;

				default:
					$result = Eresus_Kernel::app()->getPage()->
						box(sprintf(errContentPluginNotFound, $item['type']), 'errorBox', errError);
				break;
			}
		}
		else
		{
			Eresus_CMS::getLegacyKernel()->plugins->load($item['type']);
			Eresus_Kernel::app()->getPage()->module = Eresus_CMS::getLegacyKernel()->plugins->items[$item['type']];
			$result = Eresus_CMS::getLegacyKernel()->plugins->items[$item['type']]->adminRenderContent();
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
}