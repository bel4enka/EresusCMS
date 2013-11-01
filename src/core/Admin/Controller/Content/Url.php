<?php
/**
 * Контроллер АИ типа раздела «URL»
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
 * Контроллер АИ типа раздела «URL»
 *
 * @package Eresus
 */
class Eresus_Admin_Controller_Content_Url extends ContainerAware
    implements Eresus_Admin_Controller_Content_Interface
{
    /**
     * Возвращает разметку области контента
     *
     * @param Request $request
     *
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
            $section->setContent($request->request->get('url'));
            return new Eresus_HTTP_Redirect(arg('submitURL'));
        }

        $form = array(
            'name' => 'editURL',
            'caption' => ADM_EDIT,
            'width' => '100%',
            'fields' => array(
                array('type' => 'hidden', 'name' => 'update', 'value' => $item['id']),
                array('type' => 'edit', 'name' => 'url', 'label' => 'URL:', 'width' => '100%',
                    'value' => isset($item['content']) ? $item['content'] : ''),
            ),
            'buttons' => array('apply', 'cancel'),
        );
        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $html = $page->renderForm($form);
        return $html;
    }
}

