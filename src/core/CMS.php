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
     * 2since 3.01
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */ $name = 'Eresus';
    /**
     * Версия CMS
     * @var string
     * @since 3.01
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */ $version = '${product.version}';

    /**
     * HTTP-запрос
     *
     * @var HttpRequest
     */
    protected $request;

    /**
     * Описание сайта
     * @var Eresus_Site
     * @since 3.01
     */
    private $site;

    /**
     * Объект создаваемой страницы
     * @var WebPage
     * @since 3.00
     */
    protected $page;

    /**
     * Основной метод приложения
     *
     * @return int  Код завершения для консольных вызовов
     *
     * @see EresusApplication#main()
     */
    public function main()
    {
        eresus_log(__METHOD__, LOG_DEBUG, '()');

        try
        {
            /* Подключение таблицы автозагрузки классов */
            EresusClassAutoloader::add('core/cms.autoload.php');

            /* Общая инициализация */
            $this->checkEnvironment();
            $this->createFileStructure();

            eresus_log(__METHOD__, LOG_DEBUG, 'Init legacy kernel');

            /* Подключение старого ядра */
            include_once 'kernel-legacy.php';

            /**
             * @global Eresus Eresus
             */
            $GLOBALS['Eresus'] = new Eresus;

            TemplateSettings::setGlobalValue('cms', $this);

            $this->initConf();
            if (Eresus_CMS::getLegacyKernel()->conf['debug']['enable'])
            {
                // Обратная совместимость TODO убрать
                define('ERESUS_CMS_DEBUG', true);
            }

            $i18n = I18n::getInstance();
            TemplateSettings::setGlobalValue('i18n', $i18n);
            //$this->initDB();
            //$this->initSession();
            Eresus_CMS::getLegacyKernel()->init();
            TemplateSettings::setGlobalValue('Eresus', Eresus_CMS::getLegacyKernel());

            if (Eresus_Kernel::isCLI())
            {
                return $this->runCLI();
            }
            else
            {
                $this->runWeb();
            }
        }
        catch (Exception $e)
        {
            Core::logException($e);
            ob_end_clean();
            $this->fatalError($e, false);
        }
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
     */
    public function fatalError(/** @noinspection PhpUnusedParameterInspection */
        $error = null, $exit = true)
    {
        include dirname(__FILE__) . '/fatal.html.php';
        die;
    }
    //-----------------------------------------------------------------------------

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
    //-----------------------------------------------------------------------------

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
        eresus_log(__METHOD__, LOG_DEBUG, '()');

        $this->initWeb();

        $output = '';

        switch (true)
        {
            case substr($this->request->getLocal(), 0, 8) == '/ext-3rd':
                $this->call3rdPartyExtension();
                break;

            case substr($this->request->getLocal(), 0, 6) == '/admin':
                $output = $this->runWebAdminUI();
                break;

            default:
                $output = $this->runWebClientUI();
                break;
        }

        echo $output;
    }
    //-----------------------------------------------------------------------------

    /**
     * Инициализация Web
     */
    protected function initWeb()
    {
        eresus_log(__METHOD__, LOG_DEBUG, '()');

        Core::setValue('core.template.templateDir', $this->getFsRoot());
        Core::setValue('core.template.compileDir', $this->getFsRoot() . '/var/cache/templates');

        $this->request = HTTP::request();
        //$this->response = new HttpResponse();
        $this->detectWebRoot();
        //$this->initRoutes();
        $this->initSite();
    }

    /**
     * Запуск КИ
     * @return string
     * @deprecated Это временная функция
     */
    protected function runWebClientUI()
    {
        eresus_log(__METHOD__, LOG_DEBUG, 'This method is temporary.');

        include 'client.php';

        $GLOBALS['page'] = $this->page = new TClientUI();
        TemplateSettings::setGlobalValue('page', $this->page);
        $this->page->init();
        /*return */$this->page->render();
    }

    /**
     * Запуск АИ
     * @return string
     * @deprecated Это временная функция
     */
    protected function runWebAdminUI()
    {
        eresus_log(__METHOD__, LOG_DEBUG, 'This method is temporary.');

        include 'admin.php';

        $GLOBALS['page'] = $this->page = new TAdminUI();
        TemplateSettings::setGlobalValue('page', $this->page);
        /*return */$this->page->render();
    }
    //-----------------------------------------------------------------------------

    /**
     * Определение корневого веб-адреса сайта
     *
     * Метод определяет корневой адрес сайта и устанавливает соответствующим
     * образом localRoot объекта EresusCMS::request
     */
    protected function detectWebRoot()
    {
        $webServer = WebServer::getInstance();
        $DOCUMENT_ROOT = $webServer->getDocumentRoot();
        $SUFFIX = $this->getFsRoot();
        $SUFFIX = substr($SUFFIX, strlen($DOCUMENT_ROOT));
        $this->request->setLocalRoot($SUFFIX);
        eresus_log(__METHOD__, LOG_DEBUG, 'detected root: %s', $SUFFIX);

        // TODO Удалить где-нибудь в 3.03-04
        TemplateSettings::setGlobalValue('siteRoot',
            $this->request->getScheme() . '://' .
                $this->request->getHost() .
                $this->request->getLocalRoot()
        );

    }

    /**
     * Выполнение в режиме CLI
     *
     * @return int
     */
    protected function runCLI()
    {
        eresus_log(__METHOD__, LOG_DEBUG, '()');

        $this->initCLI();
        return 0;
    }
    //-----------------------------------------------------------------------------

    /**
     * Инициализация CLI
     */
    protected function initCLI()
    {
        eresus_log(__METHOD__, LOG_DEBUG, '()');
    }
    //-----------------------------------------------------------------------------

    /**
     * Инициализация конфигурации
     */
    protected function initConf()
    {
        eresus_log(__METHOD__, LOG_DEBUG, '()');

        /*
         * Переменную $Eresus приходится делать глобальной, чтобы файл конфигурации
         * мог записывать в неё свои значения.
         * TODO Избавиться от глобальной переменной
         */
        /** @noinspection PhpUnusedLocalVariableInspection */
        global $Eresus;

        $filename = $this->getFsRoot() . '/cfg/main.php';
        if (file_exists($filename))
        {
            /** @noinspection PhpIncludeInspection */
            include $filename;
            // TODO: Сделать проверку успешного подключения файла
        }
        else
        {
            $this->fatalError("Main config file '$filename' not found!");
        }
    }
    //-----------------------------------------------------------------------------

    /**
     * Инициализация БД
     */
    protected function initDB()
    {
        eresus_log(__METHOD__, LOG_DEBUG, '()');
        /*
        global $Eresus;

        // FIXME Использование устаревших настроек
        $dsn = ($Eresus->conf['db']['engine'] ? $Eresus->conf['db']['engine'] : 'mysql') .
            '://' . $Eresus->conf['db']['user'] .
            ':' . $Eresus->conf['db']['password'] .
            '@' . ($Eresus->conf['db']['host'] ? $Eresus->conf['db']['host'] : 'localhost') .
            '/' . $Eresus->conf['db']['name'];

        DBSettings::setDSN($dsn);*/
    }
    //-----------------------------------------------------------------------------

    /**
     * Инициализация сессии
     */
    protected function initSession()
    {
        eresus_log(__METHOD__, LOG_DEBUG, '()');

        /*global $Eresus;

        session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
        session_name('sid');
        session_start();

        # Обратная совместимость
        $Eresus->session = &$_SESSION['session'];
        #if (!isset($Eresus->session['msg']))
            $Eresus->session['msg'] = array('error' => array(), 'information' => array());
        #$Eresus->user = &$_SESSION['user'];
        $GLOBALS['session'] = &$_SESSION['session'];
        $GLOBALS['user'] = &$_SESSION['user'];*/
    }
    //-----------------------------------------------------------------------------

    /**
     * Обрабатывает запрос к стороннему расширению
     *
     * Вызов производится через коннектор этого расширения
     *
     * @return void
     */
    protected function call3rdPartyExtension()
    {
        $extension = substr($this->request->getLocal(), 9);
        $extension = substr($extension, 0, strpos($extension, '/'));

        $filename = $this->getFsRoot().'/ext-3rd/'.$extension.'/eresus-connector.php';
        if ($extension && is_file($filename))
        {
            /** @noinspection PhpIncludeInspection */
            include_once $filename;
            $className = $extension.'Connector';
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



/**
 * Компонент АИ
 *
 * @package Eresus
 */
class EresusAdminComponent
{

}

