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
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Eresus\CmsBundle\Extensions\ContentPlugin;

/**
 * Управление контентом
 *
 * @package Eresus
 */
class Eresus_Admin_Controllers_Content extends Eresus_Admin_Controllers_Abstract
{
    /**
     * Возвращает разметку интерфейса управления контентом текущего раздела
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return Response|string  HTML
     */
    public function adminRender(Request $request)
    {
        if (!UserRights(EDITOR))
        {
            return '';
        }
        /** @var \Eresus\CmsBundle\AdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $result = '';
        /** @var \Doctrine\Common\Persistence\ObjectManager $om */
        $om = $this->getDoctrine()->getManager();
        /** @var \Eresus\CmsBundle\Entity\Section $section */
        $section = $om->find('CmsBundle:Section', $request->get('section'));

        /** @var \Eresus\CmsBundle\Extensions\Registry $extensions */
        $extensions = $this->get('extensions');
        $page->id = $section->id;
        if (false === $extensions->get($section->type))
        {
            switch ($section->type)
            {
                case 'list':
                    if ($request->request->get('update'))
                    {
                        $section->content = $request->request->get('content');
                        $om->flush();
                        return new RedirectResponse($request->request->get('submitURL'));
                    }
                    else
                    {
                        $form = array(
                            'name' => 'editURL',
                            'caption' => ADM_EDIT,
                            'width' => '100%',
                            'fields' => array (
                                array('type'=>'hidden','name'=>'update', 'value' => $section->id),
                                array('type' => 'html', 'name' => 'content',
                                    'label' => admTemplListLabel, 'height' => '300px',
                                    'value'=>isset($item['content'])
                                        ? $item['content']
                                        : '$(items)'),
                            ),
                            'buttons' => array('apply', 'cancel'),
                        );
                        $result = $page->renderForm($form);
                    }
                    break;
                case 'url':
                    if ($request->request->get('update'))
                    {
                        $section->content = $request->request->get('url');
                        $om->flush();
                        return new RedirectResponse($request->request->get('submitURL'));
                    }
                    else
                    {
                        $form = array(
                            'name' => 'editURL',
                            'caption' => ADM_EDIT,
                            'width' => '100%',
                            'fields' => array (
                                array('type'=>'hidden','name'=>'update', 'value' => $section->id),
                                array('type' => 'edit', 'name' => 'url', 'label' => 'URL:',
                                    'width' => '100%', 'value' => isset($item['content'])
                                        ? $item['content']
                                        : ''),
                            ),
                            'buttons' => array('apply', 'cancel'),
                        );
                        $result = $page->renderForm($form);
                    }
                    break;
                default:
                    throw new \Exception(sprintf(errContentPluginNotFound, $section->type));
                    break;
            }
        }
        else
        {
            $page->module = $extensions->get($section->type);
            $result = $page->module->adminRenderContent(); // TODO Исправить!
        }
        return $result;
    }
}

