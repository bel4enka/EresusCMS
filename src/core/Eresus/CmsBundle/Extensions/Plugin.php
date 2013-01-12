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
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Eresus\CmsBundle\ContentType;
use Eresus\CmsBundle\Extensions\Controller\AdminSettings;
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
 * @property-read  string          $path         путь к папке плагина относительно корня сайта
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
     * Путь к папке плагина относительно корня сайта
     *
     * @var string
     * @since 4.00
     */
    private $path;

    /**
     * Типы контента, предоставляемые модулем
     *
     * @var ContentType[]
     * @since 4.00
     */
    private $contentTypes = array();

    /**
     * Пакет плагина
     * @var Bundle
     */
    private $bundle = null;

    /**
     * Контроллер диалога настройки
     * @var AdminSettingsDialog
     * @since 4.00
     */
    private $configController = null;

    /**
     * Создаёт основной объект плагина из указанного пространства имён
     *
     * @param string             $ns          пространство имён плагина
     * @param ContainerInterface $container   контейнер служб
     * @param array              $localConfig локальная конфигурация плагина
     *
     * @throws LogicException
     */
    public function __construct($ns, ContainerInterface $container, array $localConfig = null)
    {
        $this->container = $container;
        $this->namespace = $ns;
        $this->settings = new ArrayCollection;
        $this->path = '/plugins/' . str_replace('\\', '/', $ns);
        $config = $this->loadConfig($localConfig);
        $this->applyConfig($config, $localConfig);
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
     * Возвращает пакет плагина
     *
     * @throws LogicException  если класс «пространство\имён\плагина\Bundle» не найден или он
     *                         унаследован не от Eresus\CmsBundle\Extensions\PluginBundle
     *
     * @return Bundle
     */
    public function getBundle()
    {
        if (null === $this->bundle)
        {
            $class = $this->namespace . '\\Bundle';
            if (!class_exists($class))
            {
                throw new LogicException(sprintf('Plugin bundle class «%s» not found', $class));
            }
            $bundle = new $class;
            if (!($bundle instanceof PluginBundle))
            {
                throw new LogicException(sprintf('Class «%s» must be descendant of «%s»', $class,
                    'Eresus\CmsBundle\Extensions\PluginBundle'));
            }
            $this->bundle = $bundle;
        }
        return $this->bundle;
    }

    /**
     * Возвращает контроллер диалога настройки
     *
     * @throws LogicException
     *
     * @return AdminSettingsDialog
     * @since 4.00
     */
    public function getConfigController()
    {
        if (null === $this->configController)
        {
            $className = $this->namespace . '\Controllers\Admin\AdminSettings';
            if (class_exists($className))
            {
                $controller = new $className($this);
                if (!($controller instanceof AdminSettings))
                {
                    throw new LogicException(sprintf('Class %s" should be descendant of "%s"',
                        get_class($controller), get_class(new AdminSettings($this))));
                }
                $this->configController = $controller;
            }
            else
            {
                $this->configController = new AdminSettings($this);
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
     * Возвращает список типов контента, предоставляемых модулем
     *
     * @return ContentType[]
     * @since 4.00
     */
    public function getContentTypes()
    {
        return $this->contentTypes;
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
     * Загружает конфигурацию плагина
     *
     * @throws Exceptions\LogicException
     *
     * @since 4.00
     */
    private function loadConfig()
    {
        // Путь к файлу описания плагина
        $filename = $this->path . '/plugin.yml';
        if (!file_exists(dirname(Eresus_Kernel::app()->getFsRoot() . $filename)))
        {
            throw new LogicException("Plugin folder not exists: {$this->namespace}");
        }
        if (!file_exists(Eresus_Kernel::app()->getFsRoot() . $filename))
        {
            throw new LogicException("File not exists: $filename");
        }
        $config = Yaml::parse(Eresus_Kernel::app()->getFsRoot() . $filename);
        $this->validateConfig($config);
        return $config;
    }

    /**
     * Проверяет конфигурацию плагина
     *
     * @param array $config  конфигурация, которую надо проверить
     *
     * @throws Exceptions\LogicException
     */
    private function validateConfig(array $config)
    {
        /* Проверяем наличие необходимых полей */
        $required = array('title', 'version', 'require');
        $missed = array_diff($required, array_keys($config));
        if (count($missed))
        {
            throw new LogicException(sprintf('Missing required fields "%s" in plugin.yml of %s',
                implode(', ', $missed), $this->namespace));
        }
    }

    /**
     * Применяет конфигурацию к объекту плагина
     *
     * @param array $config       конфигурация по умолчанию
     * @param array $localConfig  локальная конфигурация
     *
     * @since 4.00
     */
    private function applyConfig(array $config, array $localConfig = null)
    {
        $this->title = $config['title'];
        $this->version = $config['version'];
        $this->requirements = $config['require'];
        $this->description = isset($config['description'])
            ? $config['description']
            : '';

        if (null === $localConfig)
        {
            $localConfig = array('enabled' => false, 'settings' => array());
        }

        $defaultSettings = is_array($config['settings']) ? $config['settings'] : array();
        $localSettings = is_array($localConfig['settings']) ? $localConfig['settings'] : array();

        $this->enabled = $localConfig['enabled'];
        $this->settings = new ArrayCollection(array_replace($defaultSettings, $localSettings));

        /*
         * Определяем типы контента
         */
        $this->contentTypes = array();
        if ($config['content_types'])
        {
            foreach ($config['content_types'] as $item)
            {
                $this->contentTypes []= new ContentType($this->container, $this->namespace,
                    $item['controller'], $item['title'],
                    isset($item['description']) ? $item['description'] : null);
            }
        }
    }
}

