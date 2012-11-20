<?php
/**
 * ${product.title}
 *
 * Родительский класс для всех плагинов
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

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

use Eresus\CmsBundle\ContentType;
use Eresus\CmsBundle\Extensions\Controllers\ConfigDialog;
use Eresus\CmsBundle\Extensions\Exceptions\LogicException;

use Eresus_Kernel;
use Eresus_CMS;

/**
 * Родительский класс для всех плагинов
 *
 * @property-read  string          $namespace    пространство имён
 * @property-read  string          $title        название
 * @property-read  string          $version      версия
 * @property-read  string          $description  описание
 * @property-read  ArrayCollection $settings     настройки
 *
 * @package Eresus
 */
class Plugin
{
    /**
     * Включен или отключен
     *
     * @var bool
     * @since 4.00
     */
    public $enabled;

    /**
     * Контейнер служб
     *
     * @var ContainerInterface
     * @since 4.00
     */
    private $container;

    /**
     * Пространство имён
     *
     * @var string
     * @since 4.00
     */
    private $namespace;

    /**
     * Название плагина
     *
     * @var string|array
     */
    private $title;

    /**
     * Версия плагина
     *
     * @var string
     */
    private $version;

    /**
     * Описание плагина
     *
     * @var string|array
     */
    private $description;

    /**
     * Зависимости
     *
     * @var array
     * @since 4.00
     */
    private $requirements;

    /**
     * Настройки плагина
     *
     * @var ArrayCollection
     */
    private $settings;

    /**
     * Контроллер диалога настройки
     * @var ConfigDialog
     * @since 4.00
     */
    private $configController = null;

    /**
     * Создаёт основной объект плагина из указанного пространства имён
     *
     * @param string             $ns         пространство имён плагина
     * @param ContainerInterface $container  контейнер служб
     * @param array              $config     настройки плагина
     *
     * @throws LogicException
     */
    public function __construct($ns, ContainerInterface $container, array $config = null)
    {
        $this->settings = new ArrayCollection;
        $this->container = $container;
        $this->load($ns, $config);
    }

    /**
     * Проверяет, установлено ли свойство
     *
     * @param string $name  имя свойства
     *
     * @return bool
     */
    public function __isset($name)
    {
        return property_exists($this, $name) && isset($this->{$name});
    }

    /**
     * Возвращает значение свойства
     *
     * @param string $name  имя свойства
     *
     * @throws LogicException
     *
     * @return mixed
     *
     * @since 4.00
     */
    public function __get($name)
    {
        if (!property_exists($this, $name))
        {
            throw new LogicException(
                'Access to unknown property "' . $name . '" of class "' . get_class($this) . '"');
        }

        $value = $this->{$name};

        if (in_array($name, array('title', 'description')) && is_array($value))
        {
            $value = $value['ru']; // TODO Переделать с текущей локалью
        }
        return $value;
    }

    /**
     * Возвращает контроллер диалога настройки
     *
     * @throws LogicException
     *
     * @return ConfigDialog
     * @since 4.00
     */
    public function getConfigController()
    {
        if (null === $this->configController)
        {
            $className = $this->namespace . '\Controllers\Admin\ConfigDialog';
            if (class_exists($className))
            {
                $controller = new $className($this);
                if (!($controller instanceof ConfigDialog))
                {
                    throw new LogicException(sprintf('Class %s" should be descendant of "%s"',
                        get_class($controller), get_class(new ConfigDialog($this))));
                }
                $this->configController = $controller;
            }
            else
            {
                $this->configController = new ConfigDialog($this);
            }
            $this->configController->setContainer($this->container);
        }
        return $this->configController;
    }

    /**
     * Возвращает список неудовлетворённых зависимостей
     *
     * @return array
     *
     * @since 4.00
     */
    public function getUnresolvedRequirements()
    {
        /** @var \Eresus\CmsBundle\Extensions\Registry $extensions */
        $extensions = $this->get('extensions');
        $installed = $extensions->getInstalled();
        $unresolved = array();
        foreach ($this->requirements as $id => $versions)
        {
            /* Определяем требуемые минимальную и максимальную версии */
            $min = isset($versions['min']) ? $versions['min'] : '99.99';
            $max = isset($versions['max']) ? $versions['max'] : $min;

            /* Определяем наличие и версию зависимости */
            if (0 === strcasecmp($id, 'cms'))
            {
                // Удаляем из версии CMS все буквы, чтобы сравнивать только цифры
                $actual = preg_replace('/[^\d\.]/', '', CMSVERSION);
            }
            else
            {
                $actual = isset($installed[$id]) ? $installed[$id]->version : false;
            }

            if (false === $actual
                || version_compare($min, $actual, '>')
                || version_compare($max, $actual, '<'))
            {
                $unresolved[$id] = array('min' => $min, 'max' => $max);
            }
        }
        return $unresolved;
    }

    /**
     * Возвращает службу по её идентификатору
     *
     * @param string $id
     *
     * @return object
     *
     * @since 4.00
     */
    private function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Загружает сведения о плагине из файла
     *
     * @param string $ns      пространство имён плагина
     * @param array  $config  настройки плагина
     *
     * @throws LogicException
     */
    private function load($ns, array $config = null)
    {
        $this->namespace = $ns;
        // Путь к файлу описания плагина
        $filename = '/plugins/' . str_replace('\\', '/', $ns) . '/plugin.yml';
        if (!file_exists(dirname(Eresus_Kernel::app()->getFsRoot() . $filename)))
        {
            throw new LogicException("Plugin not exists: $ns");
        }
        if (!file_exists(Eresus_Kernel::app()->getFsRoot() . $filename))
        {
            throw new LogicException("File not exists: $filename");
        }
        $info = Yaml::parse(Eresus_Kernel::app()->getFsRoot() . $filename);

        /* Проверяем наличие необходимых полей */
        $required = array('title', 'version', 'require');
        $missed = array_diff($required, array_keys($info));
        if (count($missed))
        {
            throw new LogicException(sprintf('Missing required fields "%s" in plugin.yml of %s',
                implode(', ', $missed), $ns));
        }

        $this->title = $info['title'];
        $this->version = $info['version'];
        $this->requirements = $info['require'];
        $this->description = isset($info['description']) ? $info['description'] : '';

        if (null === $config)
        {
            $config = array('enabled' => false, 'settings' => array());
        }

        $this->enabled = $config['enabled'];
        $this->settings = new ArrayCollection(array_replace(
            is_array($info['settings']) ? $info['settings'] : array(),
            is_array($config['settings']) ? $config['settings'] : array()
        ));

        /*
         * Регистрируем пространство имён в автозагрузчике классов
         */
        /** @var Eresus_Kernel $kernel */
        $kernel = $this->container->get('kernel');
        $kernel->getClassLoader()->add($this->namespace, $kernel->getRootDir() . '/plugins');

        /*
         * Регистрируем типы контента
         */
        if ($info['content_types'])
        {
            /** @var \Eresus\CmsBundle\CmsBundle $cms */
            $cms = $this->get('cms');
            foreach ($info['content_types'] as $item)
            {
                $cms->registerContentType(
                    new ContentType($this->namespace, $item['controller'], $item['title'],
                        isset($item['description']) ? $item['description'] : null)
                );
            }
        }
    }
}

