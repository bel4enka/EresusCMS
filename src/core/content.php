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

/**
 * Управление контентом
 *
 * @package Eresus
 */
class TContent
{

    /**
     * Возвращает разметку интерфейса управления контентом текущего раздела
     *
     * @param Eresus_CMS_Request $request
     *
     * @return string|Eresus_HTTP_Response  HTML
     */
    public function adminRender(Eresus_CMS_Request $request)
    {
        if (!UserRights(EDITOR))
        {
            return '';
        }

        $plugins = Eresus_Plugin_Registry::getInstance();
        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();

        $result = '';
        $sections = Eresus_Kernel::app()->getLegacyKernel()->sections;
        $item = $sections->get(arg('section', 'int'));

        $page->id = $item['id'];
        if (!array_key_exists($item['type'], $plugins->list))
        {
            switch ($item['type'])
            {
                case 'default':
                    $controller = new Eresus_Admin_Controller_Content_Default();
                    break;
                case 'list':
                    $controller = new Eresus_Admin_Controller_Content_List();
                    break;
                case 'url':
                    $controller = new Eresus_Admin_Controller_Content_Url();
                    break;
                default:
                    $result = $page->
                        box(sprintf(errContentPluginNotFound, $item['type']), 'errorBox', errError);
                    break;
            }
            if (isset($controller)
                && $controller instanceof Eresus_Admin_Controller_Content_Interface)
            {
                $result = $controller->getHtml($request);
            }
        }
        else
        {
            $page->module = $plugins->load($item['type']);
            $result = $page->module->adminRenderContent();
        }
        return $result;
    }
}

