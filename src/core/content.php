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
 * ������ ��������� �������� ��������� ����������� ������������. ��
 * ������ �������������� �� �/��� �������������� � ������������ �
 * ��������� ������ 3 ���� (�� ������ ������) � ��������� ����� �������
 * ������ ����������� ������������ �������� GNU, �������������� Free
 * Software Foundation.
 *
 * �� �������������� ��� ��������� � ������� �� ��, ��� ��� ����� ���
 * ��������, ������ �� ������������� �� ��� ������� ��������, � ���
 * ����� �������� ��������� ��������� ��� ������� � ����������� ���
 * ������������� � ���������� �����. ��� ��������� ����� ���������
 * ���������� ������������ �� ����������� ������������ ��������� GNU.
 *
 * �� ������ ���� �������� ����� ����������� ������������ ��������
 * GNU � ���� ����������. ���� �� �� �� ��������, �������� �������� ��
 * <http://www.gnu.org/licenses/>
 *
 * @package Eresus
 *
 * $Id$
 */

/**
 * ���������� ���������
 *
 * @package Eresus
 */
class TContent
{

	/**
	 * ���������� �������� ���������� ���������� ��������� �������� �������
	 *
	 * @return string  HTML
	 */
	public function adminRender()
	{
		global $Eresus, $page;

		if (UserRights(EDITOR))
		{
			useLib('sections');
			$sections = new Sections();
			$item = $sections->get(arg('section', 'int'));

			$page->id = $item['id'];
			if (!array_key_exists($item['type'], $Eresus->plugins->list))
			{
				switch ($item['type'])
				{
					case 'default':
						$editor = new ContentPlugin();
						if (arg('update')) $editor->update();
						else $result = $editor->adminRenderContent();
					break;

					case 'list':
						if (arg('update'))
						{
							$original = $item['content'];
							$item['content'] = arg('content', 'dbsafe');
							$Eresus->sections->update($item);
							HTTP::redirect(arg('submitURL'));
						}
						else
						{
							$form = array(
								'name' => 'editURL',
								'caption' => admEdit,
								'width' => '100%',
								'fields' => array (
									array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
									array ('type' => 'html', 'name' => 'content', 'label' => admTemplListLabel, 'height' => '300px', 'value'=>isset($item['content'])?$item['content']:'$(items)'),
								),
								'buttons' => array('apply', 'cancel'),
							);
							$result = $page->renderForm($form);
						}
					break;

					case 'url':
						if (arg('update'))
						{
							$original = $item['content'];
							$item['content'] = arg('url', 'dbsafe');
							$Eresus->sections->update($item);
							HTTP::redirect(arg('submitURL'));
						}
						else
						{
							$form = array(
								'name' => 'editURL',
								'caption' => admEdit,
								'width' => '100%',
								'fields' => array (
									array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
									array ('type' => 'edit', 'name' => 'url', 'label' => 'URL:', 'width' => '100%', 'value'=>isset($item['content'])?$item['content']:''),
								),
								'buttons' => array('apply', 'cancel'),
							);
							$result = $page->renderForm($form);
						}
					break;

					default:
						$result = $page->box(sprintf(errContentPluginNotFound, $item['type']), 'errorBox', errError);
					break;
				}
			}
			else
			{
				$Eresus->plugins->load($item['type']);
				$page->module = $Eresus->plugins->items[$item['type']];
				$result = $Eresus->plugins->items[$item['type']]->adminRenderContent();
			}
			return $result;
		}
	}
	//-----------------------------------------------------------------------------
}