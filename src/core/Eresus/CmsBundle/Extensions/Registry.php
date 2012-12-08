<?php
/**
 * ${product.title}
 *
 * Работа с плагинами
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
 *
 * @package Eresus
 */

namespace Eresus\CmsBundle\Extensions;

use DirectoryIterator;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Yaml\Yaml;

use Eresus\CmsBundle\Exceptions\ConfigException;
use Eresus\CmsBundle\Extensions\Plugin;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Работа с плагинами
 *
 * @package Eresus
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
     * Загружает плагин и возвращает его экземпляр
     *
     * Метод пытается загрузить плагин с пространством именем $ns (если он не был загружен ранее).
     * В случае успеха создаётся и возвращается экземпляр основного класса плагина (либо экземпляр,
     * созданный ранее).
     *
     * @param string $ns  пространство имён плагина
     *
     * @return Plugin|bool  экземпляр плагина или false
     *
     * @since 4.00
     */
    public function get($ns)
    {
        /* Если плагин уже был загружен возвращаем экземпляр из реестра */
        if (isset($this->plugins[$ns]))
        {
            return $this->plugins[$ns];
        }

        /* Если такой плагин не зарегистрирован или отключен, возвращаем false */
        if (
            !isset($this->config[$ns])
            || !isset($this->config[$ns]['enabled'])
            || !$this->config[$ns]['enabled']
        )
        {
            return false;
        }

        $plugin = $this->createPluginInstance($ns, $this->config[$ns]);
        $this->plugins[$ns] = $plugin;

        return $plugin;
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
        foreach ($this->config as $ns => $config)
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
        /** @var \Eresus_Kernel $kernel */
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
     * Создаёт экземпляр плагина
     *
     * @param string $namespace
     * @param array  $config
     *
     * @return Plugin
     *
     * @since 4.00
     */
    protected function createPluginInstance($namespace, array $config = array())
    {
        return new Plugin($namespace, $this->container, $config);
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
                $this->get($ns);
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

