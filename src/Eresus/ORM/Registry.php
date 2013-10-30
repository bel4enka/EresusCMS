<?php
/**
 * Реестр менеджеров ORM
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

namespace Eresus\ORM;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Eresus\ORM\Extensions\TablePrefix;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

/**
 * Реестр менеджеров ORM
 *
 * @api
 * @since 3.01
 */
class Registry
{
    /**
     * @var ContainerInterface
     * @since 3.01
     */
    private $container;

    /**
     * @var null|EntityManager
     * @since 3.01
     */
    private $manager = null;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Возвращает менеджер сущностей
     *
     * @return EntityManager
     *
     * @since 3.01
     */
    public function getManager()
    {
        if (is_null($this->manager))
        {
            $isDebugMode = $this->container->getParameter('debug');

            $cache = /*$isDebugMode  TODO Надо ли проверять наличие APC для использования ApcCache?
                ? */new ArrayCache
                /*: new ApcCache*/;

            $config = new Configuration;
            $config->setMetadataCacheImpl($cache);
            $config->setQueryCacheImpl($cache);

            /** @var \Eresus_CMS $app */
            $app = $this->container->getParameter('app');

            $entityDir = $app->getFsRoot() . '/core/Entity';
            $driver = $config->newDefaultAnnotationDriver($entityDir, false);

            /** @var \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain $chain */
            $chain = $this->container->get('doctrine.driver_chain');
            $chain->addDriver($driver, 'Eresus\Entity');
            $config->setMetadataDriverImpl($chain);

            $config->setProxyDir($app->getCacheDir() . '/doctrine/proxies');
            $config->setProxyNamespace('Eresus\Proxies');
            $config->setAutoGenerateProxyClasses($isDebugMode);

            $params = array(
                'driver' => $this->container->getParameter('db.driver'),
                'host' => $this->container->getParameter('db.host'),
                'user' => $this->container->getParameter('db.username'),
                'password' => $this->container->getParameter('db.password'),
                'dbname' => $this->container->getParameter('db.dbname'),
            );

            $prefix = $this->container->getParameter('db.prefix');
            if ($prefix)
            {
                $evm = new EventManager();
                $tablePrefix = new TablePrefix($prefix);
                $evm->addEventListener(Events::loadClassMetadata, $tablePrefix);
            }
            else
            {
                $evm = null;
            }
            $this->manager = EntityManager::create($params, $config, $evm);
        }
        return $this->manager;
    }
}

