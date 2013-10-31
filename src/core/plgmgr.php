<?php
/**
 * Управление плагинами
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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Eresus\Plugins\Plugin;

/**
 * Управление плагинами
 *
 * @package Eresus
 */
class TPlgMgr extends  ContainerAware
{
    /**
     * Уровень доступа к модулю
     * @var int
     */
    private $access = ADMIN;

    /**
     * Отрисовка контента модуля
     *
     * @return string
     */
    public function adminRender()
    {
        if (!UserRights($this->access))
        {
            Eresus_Kernel::log(__METHOD__, LOG_WARNING, 'Access denied for user "%s"',
                Eresus_CMS::getLegacyKernel()->user['name']);
            return '';
        }

        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '()');

        $result = '';
        Eresus_Kernel::app()->getPage()->title = admPlugins;

        switch (arg('action'))
        {
            case 'update':
                $this->update();
                break;
            case 'toggle':
                $this->toggleAction();
                break;
            case 'delete':
                $this->deleteAction();
                break;
            case 'settings':
                $result = $this->settingsAction();
                break;
            case 'add':
                $result = $this->addAction();
                break;
            case 'insert':
                $this->insertAction();
                break;
            default:
                $result = $this->indexAction();
                break;
        }
        return $result;
    }

    /**
     * Включает или отключает плагин
     *
     * @return void
     */
    private function toggleAction()
    {
        $registry = $this->getPluginRegistry();
        $plugin = $registry->get(arg('id'));
        if (is_null($plugin))
        {
            // TODO
        }
        if ($plugin->isEnabled())
        {
            $registry->disable($plugin);
        }
        else
        {
            $registry->enable($plugin);
        }
        HttpResponse::redirect(Eresus_Kernel::app()->getPage()->url());
    }

    /**
     *
     */
    private function deleteAction()
    {
        /** @var \Eresus\Plugins\Registry $plugins */
        $plugins = $this->container->get('plugins');
        $plugins->load(arg('delete'));
        $plugins->uninstall(arg('delete'));
        HTTP::redirect(Eresus_Kernel::app()->getPage()->url());
    }

    /**
     * @return mixed
     */
    private function settingsAction()
    {
        $registry = $this->getPluginRegistry();
        $plugin = $registry->get(arg('id'));
        if (is_null($plugin))
        {
            // TODO
        }
        $mainObject = $plugin->getMainObject();
        if (method_exists($mainObject, 'settings'))
        {
            $html = $mainObject->settings();
        }
        else
        {
            $form = array(
                'name' => 'InfoWindow',
                'caption' => Eresus_Kernel::app()->getPage()->title,
                'width' => '300px',
                'fields' => array (
                    array('type'=>'text','value'=>
                    '<div align="center"><strong>Этот плагин не имеет настроек</strong></div>'),
                ),
                'buttons' => array('cancel'),
            );
            $html = Eresus_Kernel::app()->getPage()->renderForm($form);
        }
        return $html;
    }

    /**
     *
     */
    private function update()
    {
        // TODO
        HTTP::redirect(arg('submitURL'));
    }

    /**
     * Подключает плагины
     *
     * @return void
     */
    private function insertAction()
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '()');

        $install = arg('install');
        if ($install && is_array($install))
        {
            $registry = $this->getPluginRegistry();
            foreach ($install as $name)
            {
                try
                {
                    $registry->install($name);
                }
                catch (DomainException $e)
                {
                    Eresus_Kernel::app()->getPage()->addErrorMessage($e->getMessage());
                }
            }

        }
        HttpResponse::redirect('admin.php?mod=plgmgr');
    }

    /**
     * Возвращает диалог добавления плагина
     *
     * @return string  HTML
     */
    private function addAction()
    {
        /*
         * Составляем список доступных плагинов
         */
        /** @var Eresus_CMS $app */
        $app = $this->container->getParameter('app');
        $it = new DirectoryIterator($app->getFsRoot() . '/ext');
        $found = array();
        foreach ($it as $path)
        {
            /** @var DirectoryIterator $path */
            if ($path->isDir() && !$path->isDot()
                && file_exists($path->getPathname() . '/plugin.xml'))
            {
                $found []= $path->getFilename();
            }
        }

        $registry = $this->getPluginRegistry();
        // Оставляем только неустановленные
        $notInstalled = array_diff($found, array_keys($registry->getAll()));

        $vars = array('plugins' => array());
        foreach ($notInstalled as $path)
        {
            $plugin = Plugin::createFromPath($path, $this->container);
            $vars['plugins'] []= $plugin;
        }

        $tmpl = Eresus_Template::loadFromFile('Eresus/Resources/views/PluginManager/Install.html');
        $html = $tmpl->compile($vars);

        return $html;
    }

    /**
     * Выводит список плагинов
     *
     * @return string
     *
     * @since 3.01
     */
    private function indexAction()
    {
        $registry = $this->getPluginRegistry();

        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();

        $vars = array(
            'baseUrl' => $page->url(),
            'plugins' => $registry->getAll()
        );
        $tmpl = Eresus_Template::loadFromFile('Eresus/Resources/views/PluginManager/Index.html');
        $html = $tmpl->compile($vars);
        return $html;
    }

    /**
     * @return \Eresus\Plugins\Registry
     */
    private function getPluginRegistry()
    {
        /** @var \Eresus\Plugins\Registry $registry */
        $registry = $this->container->get('plugins');
        return $registry;
    }
}

