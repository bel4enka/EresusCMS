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
use Eresus\CmsBundle\Extensions\Plugin;
use Eresus\CmsBundle\Extensions\Registry;
use Eresus\CmsBundle\AdminUI;

/**
 * Управление модулями расширения
 *
 * @package Eresus
 */
class Eresus_Admin_Controllers_Plgmgr extends Eresus_Admin_Controllers_Abstract
{
    /**
     * Отрисовка контента модуля
     *
     * @param Request $request
     *
     * @return Response|string
     */
    public function adminRender(Request $request)
    {
        if (!UserRights(ADMIN))
        {
            return '';
        }

        switch ($request->get('action'))
        {
            case null:
                $response = $this->indexAction($request);
                break;
            case 'config':
                $response = $this->configAction($request);
                break;
            case 'toggle':
                $response = $this->toggleAction($request);
                break;
            case 'install':
                $response = $this->installAction($request);
                break;
            case 'uninstall':
                $response = $this->uninstallAction($request);
                break;
            default:
                $response = '';
        }
        return $response;
    }

    /**
     * Выводит список плагинов
     *
     * @param Request $request
     *
     * @return Response|string
     *
     * @since 4.00
     */
    public function indexAction(/** @noinspection PhpUnusedParameterInspection */
        Request $request)
    {
        $registry = Eresus_CMS::getLegacyKernel()->plugins;
        $plugins = $registry->getInstalled();
        usort($plugins,
            function ($a, $b)
            {
                if ($a->title == $b->title)
                {
                    return 0;
                }
                return $a->title > $b->title ? 1 : -1;
            }
        );
        return $this->renderView('CmsBundle:PluginManager:Index.html.twig',
            array('plugins' => $plugins));
    }

    /**
     * Включает или отключает плагин
     *
     * @param Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return Response
     * @since 4.00
     */
    public function toggleAction(Request $request)
    {
        $namespace = $request->query->get('id');
        $registry = Eresus_CMS::getLegacyKernel()->plugins;
        $installed = $registry->getInstalled();
        if (!array_key_exists($namespace, $installed))
        {
            throw $this->createNotFoundException();
        }
        $plugin = $installed[$namespace];
        $plugin->enabled = !$plugin->enabled;
        $registry->update($plugin);

        return new RedirectResponse(Eresus_Kernel::app()->getPage()->url(array('id' => '')));
    }

    /**
     * Установка плагина
     *
     * @param Request $req
     *
     * @return Response|string
     * @since 4.00
     */
    public function installAction(Request $req)
    {
        $registry = Eresus_CMS::getLegacyKernel()->plugins;
        if ('POST' === $req->getMethod())
        {
            $install = $req->request->get('install');
            foreach ($install as $namespace)
            {
                $plugin = new Plugin($namespace);
                $registry->install($plugin);
            }
            return new RedirectResponse(Eresus_Kernel::app()->getPage()->url(array('id' => '')));
        }

        $installed = $registry->getInstalled();
        $all = $registry->getAll();
        // Плагины, доступные для установки
        $available = array_diff_key($all, $installed);
        usort($available,
            function ($a, $b)
            {
                if ($a->title == $b->title)
                {
                    return 0;
                }
                return $a->title > $b->title ? 1 : -1;
            }
        );
        return $this->renderView('CmsBundle:PluginManager:Install.html.twig',
            array('plugins' => $available));
    }

    /**
     * Удаляет плагин
     *
     * @param Request $req
     *
     * @return Response
     *
     * @since 4.00
     */
    public function uninstallAction(Request $req)
    {
        $registry = Eresus_CMS::getLegacyKernel()->plugins;
        $plugin = $registry->get($req->query->get('id'));
        $registry->uninstall($plugin);
        return new RedirectResponse(Eresus_Kernel::app()->getPage()->url(array('id' => '')));
    }

    /**
     * Диалог настройки плагина
     *
     * @param Request $req
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return Response|string
     * @since 4.00
     */
    public function configAction(Request $req)
    {
        $registry = Eresus_CMS::getLegacyKernel()->plugins;
        $plugin = $registry->get($req->get('id'));
        $controller = $plugin->getConfigController();
        if (!$controller->isAvailable())
        {
            throw $this->createNotFoundException();
        }
        return $controller->mainAction($req);
    }
}

