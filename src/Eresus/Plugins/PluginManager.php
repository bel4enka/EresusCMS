<?php
/**
 * Менеджер модулей расширения
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
 */

namespace Eresus\Plugins;

use Doctrine\ORM\EntityManager;
use Eresus\Plugins\Plugin;
use Eresus\Plugins\Requirements\Checker;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Eresus_Kernel;
use Eresus_PluginInfo;
use Eresus_CMS_Request;
use Eresus_Client_Controller_Content_Default;
use Eresus_Client_Controller_Content_List;
use Eresus_Client_Controller_Content_Url;
use Eresus_Client_Controller_Content_Interface;
use TClientUI;
use ContentPlugin;
use Symfony\Component\EventDispatcher\Event;
use Eresus\Events\RenderEvent;
use Eresus\Events\ResponseEvent;
use i18n;
use TContentPlugin;
use Eresus\Events\UrlSectionFoundEvent;

/**
 * Менеджер модулей расширения
 *
 * @api
 * @since 3.01
 */
class PluginManager
{
    /**
     * Список всех активированных плагинов
     *
     * @var Plugin[]
     * @deprecated с 3.01
     */
    public $list = array();

    /**
     * Массив плагинов
     *
     * @var array
     * @deprecated с 3.01
     * @todo сделать private
     */
    public $items = array();

    /**
     * Таблица обработчиков событий
     *
     * @var array
     * @deprecated с 3.01
     * @todo сделать private
     */
    public $events = array();

    /**
     * Контейнер служб
     *
     * @var ContainerInterface
     *
     * @since 3.01
     */
    private $container;

    /**
     * Реестр установленных модулей расширения
     *
     * @var Plugin[]
     *
     * @since 3.01
     */
    private $registry = array();

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->registerBcEventListeners();

        /** @var EntityManager $om */
        $om = $this->container->get('doctrine')->getManager();
        $entities = $om->getRepository('Eresus\Entity\Plugin')->findAll();

        foreach ($entities as $entity)
        {
            $plugin = new Plugin($entity, $this->container);
            $this->registry[$plugin->getName()] = $plugin;
            if ($plugin->isEnabled())
            {
                $this->list[$plugin->getName()] = $plugin;
            }
        }

        /*
         * Проверяем зависимости
         */
        $checker = new Checker($this);
        foreach ($this->list as $plugin)
        {
            $list = $checker->getUnsatisfied($plugin);
            if (count($list) > 0)
            {
                unset($this->list[$plugin->getName()]);
                // TODO Сделать более подробное сообщение
                \Eresus_Kernel::log(__CLASS__, LOG_ERR, 'Plugin %s has unsatisfied requirements',
                    $plugin->getName());
            }
        }

        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Загружает активные плагины
     *
     * @return void
     *
     * @since 2.16
     * @deprecated с 3.01
     * @todo сделать приватным
     */
    public function init()
    {
        /* Загружаем плагины */
        foreach ($this->list as $plugin)
        {
            $this->load($plugin->getName());
        }
    }

    /**
     * Устанавливает плагин
     *
     * @param string $name  Имя плагина
     *
     * @return void
     */
    public function install($name)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '("%s")', $name);

        $plugin = Plugin::createFromPath($name, $this->container);
        if (!$plugin->getInfo()->isValid())
        {
            // TODO
        }
        $entity = $plugin->getEntity();
        $entity->setActive(true);
        /** @var EntityManager $om */
        $om = $this->container->get('doctrine')->getManager();
        $om->persist($entity);
    }

    /**
     * Деинсталлирует плагин
     *
     * @param Plugin $plugin
     */
    public function uninstall(Plugin $plugin)
    {
        if (!array_key_exists($this->registry, $plugin->getName()))
        {
            // TODO
        }

        $plugin->getMainObject()->uninstall();

        /** @var EntityManager $om */
        $om = $this->container->get('doctrine')->getManager();
        $om->remove($plugin->getEntity());
    }

    /**
     * Загружает плагин и возвращает его экземпляр
     *
     * Метод пытается загрузить плагин с именем $name (если он не был загружен ранее). В случае успеха
     * создаётся и возвращается экземпляр основного класса плагина (либо экземпляр, созданный ранее).
     *
     * @param string $name  Имя плагина
     *
     * @return Plugin|TContentPlugin|bool  Экземпляр плагина или false если не удалось загрузить плагин
     *
     * @since 2.10
     */
    public function load($name)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '("%s")', $name);

        /* Если плагин уже был загружен возвращаем экземпляр из реестра */
        if (array_key_exists($name, $this->items))
        {
            Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" already loaded', $name);
            return $this->items[$name];
        }

        /* Если такой плагин не зарегистрирован, возвращаем false */
        if (!array_key_exists($name, $this->list))
        {
            Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" not registered', $name);
            return false;
        }

        $legacyKernel = Eresus_Kernel::app()->getLegacyKernel();
        // Путь к файлу плагина
        $filename = $legacyKernel->froot . 'ext/' . $name . '.php';

        /* Если такого файла нет, возвращаем false */
        if (!file_exists($filename))
        {
            Eresus_Kernel::log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"',
                $filename, $name);
            return false;
        }

        /** @noinspection PhpIncludeInspection */
        include_once $filename;
        $className = $name;

        /* TODO: Обратная совместимость с версиями до 2.10b2. Отказаться в новых версиях */
        if (!class_exists($className, false) && class_exists('T' . $className))
        {
            $className = 'T' . $className;
        }

        if (!class_exists($className, false))
        {
            Eresus_Kernel::log(__METHOD__, LOG_ERR, 'Main class %s for plugin "%s" not found in "%s"',
                $className, $name, $filename);
            Eresus_Kernel::app()->getPage()->addErrorMessage(sprintf(errClassNotFound, $name));
            return false;
        }

        // Заносим экземпляр в реестр
        $plugin = new $className();
        if ($plugin instanceof ContainerAwareInterface)
        {
            $plugin->setContainer($this->container);
        }
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" loaded', $name);
        $this->items[$name] = $plugin;

        return $plugin;
    }

    /**
     * Отрисовка контента раздела
     *
     * @param Eresus_CMS_Request $request
     *
     * @return string  Контент
     */
    public function clientRenderContent(Eresus_CMS_Request $request)
    {
        /* @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $result = '';
        switch ($page->type)
        {
            case 'default':
                $controller = new Eresus_Client_Controller_Content_Default();
                break;
            case 'list':
                $controller = new Eresus_Client_Controller_Content_List();
                break;
            case 'url':
                $controller = new Eresus_Client_Controller_Content_Url();
                break;
            default:
                $plugin = $this->load($page->type);
                if (false === $plugin)
                {
                    $page->addErrorMessage(sprintf(errContentPluginNotFound, $page->type));
                    return '';
                }
                if ($plugin instanceof ContentPlugin || $plugin instanceof TContentPlugin)
                {
                    /** @var \ContentPlugin $plugin */
                    $result = $plugin->clientRenderContent();
                }
                else
                {
                    Eresus_Kernel::log(__METHOD__, LOG_ERR,
                        sprintf('Class "%s" must be descendant of ContentPlugin or TContentPlugin',
                            get_class($plugin)));
                    $page->addErrorMessage('Internal error');
                }
        }
        if (isset($controller)
            && $controller instanceof Eresus_Client_Controller_Content_Interface)
        {
            if ($controller instanceof ContainerAwareInterface)
            {
                $controller->setContainer($this->container);
            }
            $result = $controller->getHtml($request, $page);
        }
        return $result;
    }

    /**
     * @deprecated с 3.01
     */
    public function clientOnStart()
    {
        if (isset($this->events['clientOnStart']))
        {
            foreach ($this->events['clientOnStart'] as $plugin)
            {
                $this->items[$plugin]->clientOnStart();
            }
        }
    }

    /**
     * @param UrlSectionFoundEvent $event
     *
     * @deprecated с 3.01
     */
    public function clientOnURLSplit(UrlSectionFoundEvent $event)
    {
        if (isset($this->events['clientOnURLSplit']))
        {
            foreach ($this->events['clientOnURLSplit'] as $plugin)
            {
                $this->items[$plugin]->clientOnURLSplit($event->getSectionInfo(), $event->getUrl());
            }
        }
    }

    /**
     * @param RenderEvent $event
     * @deprecated с 3.01
     */
    public function clientOnContentRender(RenderEvent $event)
    {
        if (isset($this->events['clientOnContentRender']))
        {
            foreach ($this->events['clientOnContentRender'] as $plugin)
            {
                $event->setText($this->items[$plugin]->clientOnContentRender($event->getText()));
            }
        }
    }

    /**
     * @param RenderEvent $event
     *
     * @deprecated с 3.01
     */
    public function clientOnPageRender(RenderEvent $event)
    {
        if (isset($this->events['clientOnPageRender']))
        {
            foreach ($this->events['clientOnPageRender'] as $plugin)
            {
                $event->setText($this->items[$plugin]->clientOnPageRender($event->getText()));
            }
        }
    }

    /**
     * @param ResponseEvent $event
     * @deprecated с 3.01
     */
    public function clientBeforeSend(ResponseEvent $event)
    {
        if (isset($this->events['clientBeforeSend']))
        {
            foreach ($this->events['clientBeforeSend'] as $plugin)
            {
                $event->getResponse()
                    ->setContent($this->items[$plugin]
                        ->clientBeforeSend($event->getResponse()->getContent()));
            }
        }
    }

    /**
     * @param Event $event
     *
     * @deprecated с 3.01
     */
    public function adminOnMenuRender(Event $event)
    {
        if (isset($this->events['adminOnMenuRender']))
        {
            foreach ($this->events['adminOnMenuRender'] as $plugin)
            {
                if (method_exists($this->items[$plugin], 'adminOnMenuRender'))
                {
                    $this->items[$plugin]->adminOnMenuRender();
                }
                else
                {
                    Eresus_Kernel::app()->getPage()->addErrorMessage(
                        sprintf(errMethodNotFound, 'adminOnMenuRender', $plugin));
                }
            }
        }
    }

    /**
     * Автозагрузка классов плагинов
     *
     * @param string $className
     *
     * @return boolean
     *
     * @since 3.00
     */
    public function autoload($className)
    {
        $pluginName = strtolower(substr($className, 0, strpos($className, '_')));

        if ($this->load($pluginName))
        {
            $filename = Eresus_Kernel::app()->getFsRoot() . '/ext/' . $pluginName . '/classes/' .
                str_replace('_', '/', substr($className, strlen($pluginName) + 1)) . '.php';
            if (file_exists($filename))
            {
                /** @noinspection PhpIncludeInspection */
                include $filename;
                return Eresus_Kernel::classExists($className);
            }
        }

        return false;
    }

    /**
     * Возвращает настройки плагина
     *
     * @param string $pluginName
     *
     * @return array
     *
     * @since 3.01
     */
    public function getSettingsFor($pluginName)
    {
        return array_key_exists($pluginName, $this->list)
            ? decodeOptions($this->list[$pluginName]['settings'])
            : array();
    }

    /**
     * Возвращает все установленные модули
     *
     * @return Plugin[]
     *
     * @since 3.01
     */
    public function getAll()
    {
        return $this->registry;
    }

    /**
     * Возвращает модуль по его имени или null, если модуль не установлен или отключен
     *
     * @param string $name
     *
     * @return Plugin|null
     *
     * @since 3.01
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->registry))
        {
            $plugin = $this->registry[$name];
            if ($plugin->isEnabled())
            {
                return $plugin;
            }
        }
        return null;
    }

    /**
     * Включает модуль
     *
     * @param Plugin $plugin
     *
     * @since 3.01
     */
    public function enable(Plugin $plugin)
    {
        // TODO
    }

    /**
     * Отключает модуль
     *
     * @param Plugin $plugin
     *
     * @since 3.01
     */
    public function disable(Plugin $plugin)
    {
        // TODO
    }

    /**
     * Регистрирует старые методы обработки событий
     *
     * @since 3.01
     */
    private function registerBcEventListeners()
    {
        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $ed */
        $ed = $this->container->get('events');

        $ed->addListener('cms.admin.start', array($this, 'adminOnMenuRender'));

        $ed->addListener('cms.client.start', array($this, 'clientOnStart'));
        $ed->addListener('cms.client.url_section_found', array($this, 'clientOnURLSplit'));
        $ed->addListener('cms.client.render_content', array($this, 'clientOnContentRender'));
        $ed->addListener('cms.client.render_page', array($this, 'clientOnPageRender'));
        $ed->addListener('cms.client.response', array($this, 'clientBeforeSend'));
    }
}

