<?php
/**
 * Класс приложения Eresus CMS
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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\Event;

/**
 * Класс приложения Eresus CMS
 *
 * @property-read string $version  версия CMS
 *
 * @package Eresus
 */
class Eresus_CMS extends Eresus_Application
{
    /**
     * Название CMS
     * @var string
     * @since 3.01
     */
    private $name = 'Eresus';

    /**
     * Версия CMS
     * @var string
     * @since 3.01
     */
    private $version = '${product.version}';

    /**
     * Описание сайта
     * @var Eresus_Site
     * @since 3.01
     */
    private $site;

    /**
     * Контейнер служб
     * @var ContainerBuilder
     * @since 3.01
     */
    private $container;

    /**
     * Объект создаваемой страницы
     * @var WebPage
     * @since 3.00
     */
    protected $page;

    /**
     * Инициализация
     *
     * @since 3.01
     */
    public function __construct()
    {
        parent::__construct();

        $this->createContainer();

        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */
        $dispatcher = $this->container->get('events');
        $dispatcher->addListener('cms.shutdown', array($this, 'onShutdown'));
    }

    /**
     * Основной метод приложения
     *
     * @return int  Код завершения для консольных вызовов
     *
     * @see EresusApplication::main()
     */
    public function main()
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'starting...');

        /* Общая инициализация */
        $this->checkEnvironment();
        $this->createFileStructure();

        /* Подключение старого ядра */
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Init legacy kernel');
        include_once 'kernel-legacy.php';

        /**
         * @global Eresus Eresus
         * @todo Обратная совместимость — удалить
         * @deprecated с 3.01 используйте Eresus_Kernel::app()->getLegacyKernel()
         */
        $GLOBALS['Eresus'] = new Eresus($this->container);

        TemplateSettings::setGlobalValue('cms', $this);

        $this->initConf();
        $i18n = I18n::getInstance();
        TemplateSettings::setGlobalValue('i18n', $i18n);
        Eresus_CMS::getLegacyKernel()->init();
        TemplateSettings::setGlobalValue('Eresus', Eresus_CMS::getLegacyKernel());
        $this->runWeb();
        return 0;
    }

    /**
     * Магический метод для обеспечения доступа к свойствам только на чтение
     *
     * @param string $property
     * @return mixed
     * @throws LogicException  если свойства $property нет
     */
    public function __get($property)
    {
        if (property_exists($this, $property))
        {
            return $this->{$property};
        }
        throw new LogicException(sprintf('Trying to access unknown property %s of %s',
            $property, __CLASS__));
    }

    /**
     * Выводит сообщение о фатальной ошибке и прекращает работу приложения
     *
     * @param Exception|string $error  исключение или описание ошибки
     * @param bool             $exit   завершить или нет выполнение приложения
     *
     * @return void
     *
     * @since 2.16
     * @deprecated с 3.01, вбрасывайте исключения
     */
    public function fatalError(/** @noinspection PhpUnusedParameterInspection */
        $error = null, $exit = true)
    {
        include dirname(__FILE__) . '/fatal.html.php';
        die;
    }

    /**
     * Возвращает экземпляр класса Eresus
     *
     * Метод нужен до отказа от класса Eresus
     *
     * @return Eresus
     *
     * @since 3.00
     */
    public static function getLegacyKernel()
    {
        return $GLOBALS['Eresus'];
    }

    /**
     * Возвращает экземпляр класса Eresus_Site
     *
     * @return Eresus_Site
     *
     * @since 3.01
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Возвращает экземпляр класса TClientUI или TAdminUI
     *
     * Метод нужен до отказа от переменной $page
     *
     * @return WebPage
     *
     * @since 3.00
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Возвращает путь к папке кэша
     *
     * @return string
     * @since 3.01
     */
    public function getCacheDir()
    {
        return $this->getFsRoot() . '/var/cache';
    }

    /**
     * Завершение обработки запроса
     *
     * @param Event $event
     *
     * @since 3.01
     */
    public function onShutdown(Event $event)
    {
        /** @var \Doctrine\ORM\EntityManager $om */
        $om = $this->container->get('doctrine')->getManager();
        $om->flush();
    }

    /**
     * Проверка окружения
     *
     * @return void
     */
    protected function checkEnvironment()
    {
        $errors = array();

        /* Проверяем наличие нужных файлов */
        $required = array('cfg/main.php');
        foreach ($required as $filename)
        {
            if (!file_exists($filename))
            {
                $errors []= array('file' => $filename, 'problem' => 'missing');
            }
        }

        /* Проверяем доступность для записи */
        $writable = array(
            'cfg/settings.php',
            'var',
            'data',
            'templates',
            'style'
        );
        foreach ($writable as $filename)
        {
            if (!is_writable($filename))
            {
                $errors []= array('file' => $filename, 'problem' => 'non-writable');
            }
        }

        if ($errors)
        {
            if (!Eresus_Kernel::isCLI())
            {
                require_once 'errors.html.php';
            }
            else
            {
                die("Errors...\n"); // TODO Доделать
            }
        }
    }

    /**
     * Создание файловой структуры
     *
     * @return void
     */
    protected function createFileStructure()
    {
        $dirs = array(
            '/var/log',
            '/var/cache',
            '/var/cache/templates',
        );

        foreach ($dirs as $dir)
        {
            if (!file_exists($this->getFsRoot() . $dir))
            {
                $umask = umask(0000);
                mkdir($this->getFsRoot() . $dir, 0777);
                umask($umask);
            }
            // TODO Сделать проверку на запись в созданные директории
        }
    }

    /**
     * Выполнение в режиме Web
     */
    protected function runWeb()
    {
        $request = new Eresus_CMS_Request(Eresus_HTTP_Request::createFromGlobals());
        $request->setSiteRoot($this->detectWebRoot());

        // TODO Удалить где-нибудь в 3.03-04
        TemplateSettings::setGlobalValue('siteRoot', $request->getSiteRoot());

        $this->initSite();

        if (substr($request->getPath(), 0, 8) == '/ext-3rd')
        {
            $this->call3rdPartyExtension($request);
        }
        else
        {
            if ($request->getDirectory() == '/admin' || $request->getPath() == '/admin.php')
            {
                $controller = new Eresus_Admin_FrontController($this->container, $request);
            }
            else
            {
                $controller = new Eresus_Client_FrontController($this->container, $request);
            }
            $this->page = $controller->getPage();
            /**
             * @global
             * @deprecated с 3.01 используйте Eresus_Kernel::app()->getPage()
             */
            $GLOBALS['page'] = $this->page;
            TemplateSettings::setGlobalValue('page', $this->page);
            $response = $controller->dispatch();
            $response->send();

            /** @var \Symfony\Component\EventDispatcher\EventDispatcher $evd */
            $evd = $this->container->get('events');
            $evd->dispatch('cms.shutdown');
        }
    }

    /**
     * Определяет и возвращает корневой адрес сайта
     *
     * @return string
     */
    protected function detectWebRoot()
    {
        $webServer = WebServer::getInstance();
        $documentRoot = $webServer->getDocumentRoot();
        $suffix = $this->getFsRoot();
        $suffix = substr($suffix, strlen($documentRoot));
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'detected root: %s', $suffix);
        return $suffix;
    }

    /**
     * Инициализация конфигурации
     *
     * @throws RuntimeException
     */
    protected function initConf()
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '()');

        /*
         * Переменную $Eresus приходится делать глобальной, чтобы файл конфигурации
         * мог записывать в неё свои значения.
         * TODO Избавиться от глобальной переменной
         */
        /** @noinspection PhpUnusedLocalVariableInspection */
        global $Eresus;

        $filename = $this->getFsRoot() . '/cfg/main.php';
        if (!file_exists($filename))
        {
            throw new RuntimeException(_("Не найден файл настроек «{$filename}»!"));
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
     * Обрабатывает запрос к стороннему расширению
     *
     * Вызов производится через коннектор этого расширения
     *
     * @param Eresus_CMS_Request $request
     *
     * @return void
     */
    protected function call3rdPartyExtension(Eresus_CMS_Request $request)
    {
        $extension = substr($request->getDirectory(), 9);
        if (($p = strpos($extension, '/')) !== false)
        {
            $extension = substr($extension, 0, $p);
        }

        $filename = $this->getFsRoot() . '/ext-3rd/' . $extension . '/eresus-connector.php';
        if ($extension && is_file($filename))
        {
            /** @noinspection PhpIncludeInspection */
            include_once $filename;
            $className = $extension . 'Connector';
            /** @var EresusExtensionConnector $connector */
            $connector = new $className;
            $connector->proxy();
        }
        else
        {
            header('404 Not Found', true, 404);
            echo '404 Not Found';
        }
    }

    /**
     * Создаёт контейнер служб
     *
     * @since 3.01
     */
    private function createContainer()
    {
        $this->container = new ContainerBuilder();
        $this->container->setParameter('container', $this->container);
        $this->container->setParameter('app', $this);

        $this->container
            ->register('events', 'Symfony\Component\EventDispatcher\EventDispatcher');
        $this->container
            ->register('doctrine', 'Eresus\ORM\Registry')
            ->addArgument('%container%');
        $this->container
            ->register('doctrine.driver_chain',
                'Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain');
        $this->container
            ->register('plugins', 'Eresus\Plugins\PluginManager')
            ->addArgument('%container%');
        $this->container
            ->register('accounts', 'Eresus\Security\AccountManager')
            ->addArgument('%container%');

        //TODO Удалить после удаления устаревших компонентов
        $GLOBALS['_container'] = $this->container;
    }

    /**
     * Инициализирует сайт
     *
     * @return void
     *
     * @since 3.01
     */
    private function initSite()
    {
        $this->site = new Eresus_Site($this->getLegacyKernel());
        $this->site->setTitle(siteTitle);
        $this->site->setDescription(siteDescription);
        $this->site->setKeywords(siteKeywords);
        TemplateSettings::setGlobalValue('site', $this->site);
    }
}

