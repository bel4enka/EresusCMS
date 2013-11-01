<?php
/**
 * Контроллер АИ типа раздела «Список подразделов»
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
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

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;

/**
 * Контроллер АИ типа раздела «Список подразделов»
 *
 * @package Eresus
 */
class Eresus_Admin_Controller_Content_List extends ContainerAware
    implements Eresus_Admin_Controller_Content_Interface
{
    /**
     * Возвращает разметку области контента
     *
     * @param Request $request
     * @return string|Eresus_HTTP_Response
     * @since 3.01
     */
    public function getHtml(Request $request)
    {
        /** @var \Eresus\Sections\SectionManager $manager */
        $manager = $this->container->get('sections');
        $section = $manager->get($request->query->getInt('section'));
        $item = $section->toLegacyArray();

        if ($request->getMethod() == 'POST')
        {
            /** @var \Eresus\Sections\SectionManager $manager */
            $manager = $this->container->get('sections');
            $section = $manager->get($item['id']);
            $section->setContent($request->request->get('content'));
            return new Eresus_HTTP_Redirect(arg('submitURL'));
        }
        else
        {
            $form = array(
                'name' => 'editURL',
                'caption' => ADM_EDIT,
                'width' => '100%',
                'fields' => array (
                    array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
                    array ('type' => 'html', 'name' => 'content', 'label' => admTemplListLabel,
                        'height' => '300px',
                        'value' => isset($item['content']) ? $item['content'] : '$(items)'),
                ),
                'buttons' => array('apply', 'cancel'),
            );
            /** @var TAdminUI $page */
            $page = Eresus_Kernel::app()->getPage();
            return $page->renderForm($form);
        }
    }
}

