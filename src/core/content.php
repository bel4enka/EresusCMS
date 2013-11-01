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

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Управление контентом
 *
 * @package Eresus
 */
class TContent implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     * @since 3.01
     */
    private $container;

    /**
     * @param ContainerInterface $container
     * @since 3.01
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Возвращает разметку интерфейса управления контентом текущего раздела
     *
     * @param Request $request
     *
     * @return string|Eresus_HTTP_Response  HTML
     */
    public function adminRender(Request $request)
    {
        if (!UserRights(EDITOR))
        {
            return '';
        }

        /** @var \Eresus\Plugins\PluginManager $plugins */
        $plugins = $this->container->get('plugins');
        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();

        $result = '';
        /** @var \Eresus\Sections\SectionManager $sections */
        $sections = $this->container->get('sections');
        $section = $sections->get(arg('section', 'int'));

        $page->id = $section->getId();
        if (!array_key_exists($section->getType(), $plugins->list))
        {
            switch ($section->getType())
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
                        box(sprintf(errContentPluginNotFound, $section->getType()), 'errorBox',
                            errError);
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
            $page->module = $plugins->load($section->getType());
            $result = $page->module->adminRenderContent();
        }
        return $result;
    }
}

