<?php
/**
 * Реестр модулей расширения
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
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
 */

namespace Eresus\CmsBundle\Extensions;

use DirectoryIterator;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Yaml\Yaml;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Eresus\CmsBundle\Exceptions\ConfigException;
use Eresus\CmsBundle\Extensions\Plugin;
use Eresus\CmsBundle\Content\ContentTypeRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Eresus\CmsBundle\Kernel;

/**
 * Реестр модулей расширения
 *
 * @since 4.00
 */
class Registry implements ContainerAwareInterface
{
    /**
     * Контейнер служб
     * @var ContainerInterface
     * @since 4.00
     */
    private $container;

    /**
     * Настройки плагинов
     *
     * @var array
     * @since 4.00
     */
    private $config = array();

    /**
     * Список плагинов
     *
     * @var Plugin[]
     * @since 4.00
     */
    private $plugins = array();

    /**
     * Конструктор
     *
     * @param ContainerInterface $container
     *
     * @since 4.00
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->init();
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @since 4.00
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Возвращает объект плагина
     *
     * @param string $ns  пространство имён плагина
     *
     * @return Plugin|bool  экземпляр плагина или false, если такого плагина нет или он отключен
     *
     * @since 4.00
     */
    public function get($ns)
    {
        if (isset($this->plugins[$ns]))
        {
            return $this->plugins[$ns];
        }
        return false;
    }

    /**
     * Регистрирует плагин в системе
     *
     * @param Plugin $plugin
     *
     * @since 4.00
     */
    public function register(Plugin $plugin)
    {
        /*
         * Регистрируем пространство имён в автоматическом загрузчике классов
         * Это действие нельзя перенести в ядро потому что для работы метода Plugin::getBundle
         * нужно чтобы автозагрузка уже работала для пространства имён плагина. И не только чтобы
         * найти сам класс Bundle (файл с которым можно подключить и ручками), но и чтобы найти все
         * классы, которые может использовать Bundle.
         */
        /** @var \Eresus_Kernel $kernel */
        $kernel = $this->container->get('kernel');
        $kernel->getClassLoader()->add($plugin->namespace, $kernel->getRootDir() . '/plugins');

        /*Регистрируем пакет плагина в ядре */
        $kernel->registerBundle($plugin->getBundle());

        /*
         * Регистрируем типы контента
         */
        /** @var ContentTypeRegistry $registry */
        $registry = $this->container->get('content_types');
        foreach ($plugin->getContentTypes() as $type)
        {
            $registry->register($type);
        }

        $this->plugins[$plugin->namespace] = $plugin;
    }

    /**
     * Возвращает включенные плагины
     *
     * @return Plugin[]
     *
     * @since 4.00
     */
    public function getEnabled()
    {
        return $this->plugins;
    }

    /**
     * Возвращает установленные плагины (включая отключенные)
     *
     * @return Plugin[]
     *
     * @since 4.00
     */
    public function getInstalled()
    {
        $installed = $this->plugins;
        foreach (array_keys($this->config) as $ns)
        {
            if (!isset($installed[$ns]))
            {
                $installed[$ns] = new Plugin($ns, $this->container);
            }
        }
        return $installed;
    }

    /**
     * Возвращает все плагины
     *
     * @return Plugin[]
     *
     * @since 4.00
     */
    public function getAll()
    {
        /** @var Kernel $kernel */
        $kernel = $this->container->get('kernel');
        $vendors = new DirectoryIterator($kernel->getRootDir() . '/plugins');
        $all = $this->plugins;
        foreach ($vendors as $vendor)
        {
            /** @var DirectoryIterator $vendor */
            if ($vendor->isDir() && !$vendor->isDot())
            {
                $plugins = new DirectoryIterator($vendor->getPathname());
                foreach ($plugins as $plugin)
                {
                    /** @var DirectoryIterator $plugin */
                    if ($plugin->isDir() && !$plugin->isDot())
                    {
                        $namespace = $vendor->getBasename() . '\\' . $plugin->getBasename();
                        if (!isset($all[$namespace]))
                        {
                            $all[$namespace] = new Plugin($namespace, $this->container);
                        }
                    }
                }
            }
        }
        return $all;
    }

    /**
     * Сохраняет изменения в плагине в БД
     *
     * @param Plugin $plugin
     *
     * @since 4.00
     */
    public function update(Plugin $plugin)
    {
        $this->config[$plugin->namespace] = array(
            'enabled' => $plugin->enabled,
            'settings' => $plugin->settings->toArray()
        );
        $yml = Yaml::dump($this->config);
        file_put_contents($this->getDbFilename(), $yml);
    }

    /**
     * Устанавливает плагин
     *
     * @param Plugin $plugin
     *
     * @return void
     *
     * @since 4.00
     */
    public function install(Plugin $plugin)
    {
        $plugin->enabled = true;
        $this->update($plugin);

        $em = $this->getEntityManager();
        $schemaTool = new SchemaTool($em);
        $schemaTool->updateSchema($em->getMetadataFactory()->getAllMetadata());
    }

    /**
     * Удаляет плагин из БД
     *
     * @param Plugin $plugin
     *
     * @return void
     * @since 4.00
     */
    public function uninstall(Plugin $plugin)
    {
        $em = $this->getEntityManager();
        $entityMetaData = $em->getMetadataFactory()->getAllMetadata();
        $targetNamespace = $plugin->namespace . '\Entity';
        $classes = array();
        foreach ($entityMetaData as $classMetaData)
        {
            /** @var \Doctrine\ORM\Mapping\ClassMetadata $classMetaData */
            if ($classMetaData->namespace == $targetNamespace)
            {
                $classes []= $classMetaData;
            }
        }
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema($classes);

        unset($this->config[$plugin->namespace]);
        $yml = Yaml::dump($this->config);
        file_put_contents($this->getDbFilename(), $yml);
    }

    /**
     * Возвращает путь к файлу базы данных
     *
     * @return string
     *
     * @since 4.00
     */
    private function getDbFilename()
    {
        /** @var \Symfony\Component\Config\FileLocator $locator */
        $locator = $this->container->get('config_locator');
        return $locator->locate('plugins.yml');
    }

    /**
     * Загружает активные плагины
     *
     * @throws ConfigException
     *
     * @return void
     *
     * @since 4.00
     */
    private function init()
    {
        $filename = $this->getDbFilename();
        $this->config = Yaml::parse($filename);
        if (is_string($this->config))
        {
            throw new ConfigException('Error parsing ' . $filename);
        }
        if (null === $this->config)
        {
            $this->config = array();
        }
        foreach ($this->config as $ns => $config)
        {
            if (isset($config['enabled']) && $config['enabled'])
            {
                $plugin = new Plugin($ns, $this->container, $this->config[$ns]);
                $this->plugins[$ns] = $plugin;
                $this->register($plugin);
            }
        }
    }

    /**
     * Возвращает менеджер сущностей
     *
     * @return EntityManager
     *
     * @since 4.00
     */
    private function getEntityManager()
    {
        /** @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine */
        $doctrine = $this->container->get('doctrine');
        /** @var EntityManager $em */
        $em = $doctrine->getManager();
        return $em;
    }
}

