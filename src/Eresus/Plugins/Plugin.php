<?php
/**
 * Модуль расширения
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

use Eresus\Entity\Plugin as PluginEntity;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Модуль расширения
 *
 * @api
 * @since 3.01
 */
class Plugin
{
    /**
     * Контейнер служб
     *
     * @var ContainerInterface
     *
     * @since 3.01
     */
    private $container;

    /**
     * Модель
     *
     * @var PluginEntity
     *
     * @since 3.01
     */
    private $entity;

    /**
     * Описание модуля
     *
     * @var null|Info
     *
     * @since 3.01
     */
    private $info = null;

    /**
     * Основной объект модуля
     *
     * @var \Eresus_Plugin|\TContentPlugin
     *
     * @since 3.01
     */
    private $mainObject = null;

    /**
     * Создаёт объект модуля на основе папки
     *
     * @param string             $path
     * @param ContainerInterface $container
     *
     * @return Plugin
     *
     * @since 3.01
     */
    public static function createFromPath($path, ContainerInterface $container)
    {
        $entity = new PluginEntity;
        $path = rtrim($path, '/');
        $entity->setName(basename($path));
        $entity->setActive(false);
        $plugin = new self($entity, $container);
        return $plugin;
    }

    /**
     * @param PluginEntity       $entity
     * @param ContainerInterface $container
     *
     * @since 3.01
     */
    public function __construct(PluginEntity $entity, ContainerInterface $container)
    {
        $this->container = $container;
        $this->entity = $entity;
    }

    /**
     * Возвращает имя модуля
     *
     * @return string
     *
     * @since 3.01
     */
    public function getName()
    {
        return $this->entity->getName();
    }

    /**
     * Возвращает true, если модуль включен
     *
     * @return bool
     *
     * @since 3.01
     */
    public function isEnabled()
    {
        return $this->entity->isActive();
    }

    /**
     * Возвращает путь к папке модуля
     *
     * @return string
     *
     * @since 3.01
     */
    public function getFolder()
    {
        /** @var \Eresus_CMS $app */
        $app = $this->container->getParameter('app');
        return $app->getFsRoot() . '/ext/' . $this->getName();
    }

    /**
     * Возвращает модель данных
     *
     * @return PluginEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Возвращает сведения о модуле
     *
     * @return Info
     *
     * @since 3.01
     */
    public function getInfo()
    {
        if (is_null($this->info))
        {
            $this->info = new Info($this->getFolder() . '/plugin.xml');
        }
        return $this->info;
    }

    /**
     * Возвращает главный объект модуля
     *
     * @return \Eresus_Plugin|\TContentPlugin
     *
     * @since 3.01
     */
    public function getMainObject()
    {
        if (is_null($this->mainObject))
        {
            // Путь к файлу основного класса
            $filename = $this->getFolder() . '/main.php';

            if (!file_exists($filename))
            {
                // TODO
            }

            /** @noinspection PhpIncludeInspection */
            include_once $filename;

            $className = $this->getName();
            /* TODO: Обратная совместимость с версиями до 2.10b2. Отказаться в новых версиях */
            if (!class_exists($className, false) && class_exists('T' . $className))
            {
                $className = 'T' . $className;
            }

            if (!class_exists($className, false))
            {
                // TODO
            }

            $this->mainObject = new $className();
            if ($this->mainObject instanceof ContainerAwareInterface)
            {
                $this->mainObject->setContainer($this->container);
            }
        }
        return $this->mainObject;
    }
}

