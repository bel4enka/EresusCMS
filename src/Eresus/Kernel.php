<?php
/**
 * Ядро
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

namespace Eresus;

use Bedoved;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Eresus_Kernel;

/**
 * Ядро
 *
 * Ядро обеспечивает начальную инициализацию CMS, создание объекта сайта и передачу ему управления.
 *
 * @since 3.01
 */
class Kernel
{
    /**
     * Режим отладки
     *
     * @var bool
     *
     * @since 3.01
     */
    private $debug = false;

    /**
     * @var Bedoved
     * @since 3.01
     */
    private $bedoved;

    /**
     * Папка приложения
     *
     * @var string
     *
     * @since 3.01
     */
    private $appDir;

    /**
     * Контейнер служб
     * @var ContainerBuilder
     * @since 3.01
     */
    private $container;

    /**
     * Описание сайта
     * @var Site
     * @since 3.01
     */
    private $site;

    /**
     * Инициализирует ядро
     *
     * @param string $appDir  папка приложения
     *
     * @since 3.01
     */
    public function __construct($appDir)
    {
        $this->appDir = $appDir;
        //TODO session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
        session_name('sid');
        session_start();
    }

    /**
     * Включает или отключает режим отладки
     *
     * @param bool $state
     *
     * @since 3.01
     */
    public function setDebug($state)
    {
        $this->debug = $state;
    }

    /**
     * Запускает CMS
     *
     * @since 3.01
     */
    public function start()
    {
        $request = Request::createFromGlobals();

        $this->initErrorHandling();
        $this->initContainer($request);
        $this->initLegacyKernel();
        $this->initEventListeners();
        $this->initConf();

        /** TODO Обратная совместимость @deprecated с 3.01 */
        $GLOBALS['Eresus']->init();
        Eresus_Kernel::$app = $this;

        $this->run($request);
    }

    /**
     * Возвращает контейнер
     *
     * @return ContainerBuilder
     *
     * @since 3.01
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Возвращает папку приложения
     *
     * @return string
     *
     * @since 3.01
     */
    public function getAppDir()
    {
        return $this->appDir;
    }

    /**
     * Возвращает путь к папке кэша
     *
     * @return string
     * @since 3.01
     */
    public function getCacheDir()
    {
        return $this->getAppDir() . '/var/cache';
    }

    /**
     * Возвращает экземпляр класса Eresus
     *
     * Метод нужен до отказа от класса Eresus
     *
     * @return \Eresus
     *
     * @since 3.00
     */
    public static function getLegacyKernel()
    {
        return $GLOBALS['Eresus'];
    }

    /**
     * Завершение обработки запроса
     *
     * @since 3.01
     */
    public function onShutdown()
    {
        /** @var \Doctrine\ORM\EntityManager $om */
        $om = $this->container->get('doctrine')->getManager();
        $om->flush();
    }

    /**
     * Инициализация обработки ошибок
     *
     * @since 3.01
     */
    private function initErrorHandling()
    {
        $this->bedoved = new Bedoved($this->debug);
        $this->bedoved
            ->enableErrorConversion()
            ->enableExceptionHandling()
            ->enableFatalErrorHandling();
    }

    /**
     * Создаёт контейнер служб
     *
     * @param Request $request
     *
     * @since 3.01
     */
    private function initContainer(Request $request)
    {
        $this->container = new ContainerBuilder();

        $this->container->setParameter('debug', $this->debug);
        $this->container->setParameter('request', $request);
        $this->container->setParameter('security.session.ttl', 30); // в минутах
        $this->container->setParameter('admin.theme', 'default');

        $this->container->set('container', $this->container);
        $this->container->set('kernel', $this);

        $this->container
            ->register('events', 'Symfony\Component\EventDispatcher\EventDispatcher');

        $this->container
            ->register('doctrine', 'Eresus\ORM\Registry')
            ->addArgument(new Reference('container'));

        $this->container
            ->register('doctrine.driver_chain',
                'Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain');

        $this->container
            ->register('security', 'Eresus\Security\SecurityManager')
            ->addArgument(new Reference('container'));

        $this->container
            ->register('plugins', 'Eresus\Plugins\PluginManager')
            ->addArgument(new Reference('container'));

        $this->container
            ->register('accounts', 'Eresus\Security\AccountManager')
            ->addArgument(new Reference('container'));

        $this->container
            ->register('sections', 'Eresus\Sections\SectionManager')
            ->addArgument(new Reference('container'));

        $this->container
            ->register('templates.dwoo', 'Dwoo')
            ->setPublic(false)
            ->addArgument($this->getCacheDir() . '/templates');

        $this->container
            ->register('i18n', 'I18n')
            ->setFactoryClass('I18n')
            ->setFactoryMethod('getInstance');

        $this->container
            ->register('templates', 'Eresus\Templating\TemplateManager')
            ->addArgument(new Reference('container'))
            ->addArgument(new Reference('templates.dwoo'))
            ->addMethodCall('setGlobal', array('site', '%site%'))
            ->addMethodCall('setGlobal', array('i18n', new Reference('i18n')))
            ->addMethodCall('setGlobal', array('theme', new Reference('admin.theme')));

        $this->container
            ->register('admin.theme', 'Eresus\Templating\AdminTheme')
            ->addArgument(new Reference('container'))
            ->addArgument('%admin.theme%');

        //TODO Удалить после удаления устаревших компонентов
        $GLOBALS['_container'] = $this->container;
    }

    /**
     * Устанавливает обработчики событий
     *
     * @since 3.01
     */
    private function initEventListeners()
    {
        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */
        $dispatcher = $this->container->get('events');
        $dispatcher->addListener('cms.shutdown', array($this, 'onShutdown'));
    }

    /**
     * Подключение старого ядра
     */
    private function initLegacyKernel()
    {
        include_once $this->getAppDir() . '/core/kernel-legacy.php';

        $legacyKernel = new \Eresus($this->container);
        /**
         * @global \Eresus Eresus
         * @todo Обратная совместимость — удалить
         * @deprecated с 3.00 используйте Eresus_Kernel::app()->getLegacyKernel()
         */
        $GLOBALS['Eresus'] = $legacyKernel;
    }

    /**
     * Инициализация конфигурации
     *
     * @throws \RuntimeException
     */
    private function initConf()
    {
        /*
         * Переменную $Eresus приходится делать глобальной, чтобы файл конфигурации
         * мог записывать в неё свои значения.
         * TODO Избавиться от глобальной переменной
         */
        /** @noinspection PhpUnusedLocalVariableInspection */
        global $Eresus;

        $filename = $this->getAppDir() . '/cfg/main.php';
        if (!file_exists($filename))
        {
            throw new \RuntimeException(_("Не найден файл настроек «{$filename}»!"));
        }

        /** @noinspection PhpIncludeInspection */
        include $filename;
        // TODO: Сделать проверку успешного подключения файла

        $this->container->setParameter('debug', $Eresus->conf['debug']['enable']);

        $this->container->setParameter('db.driver', 'pdo_' . $Eresus->conf['db']['engine']);
        $this->container->setParameter('db.host', $Eresus->conf['db']['host']);
        $this->container->setParameter('db.username', $Eresus->conf['db']['user']);
        $this->container->setParameter('db.password', $Eresus->conf['db']['password']);
        $this->container->setParameter('db.dbname', $Eresus->conf['db']['name']);
        $this->container->setParameter('db.prefix', $Eresus->conf['db']['prefix']);
    }

    /**
     * Выполняет приложение
     *
     * @param Request $request
     *
     * @since 3.01
     */
    private function run(Request $request)
    {
        $this->site = new Site($this, $request);
        $this->container->setParameter('site', $this->site);

        $response = $this->site->handleRequest($request);
        $response->send();

        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $evd */
        $evd = $this->container->get('events');
        $evd->dispatch('cms.shutdown');
    }
}

