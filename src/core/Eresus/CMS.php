<?php
/**
 * ${product.title}
 *
 * Главный модуль
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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Eresus\CmsBundle\HTTP\Request;
use Eresus\CmsBundle\Extensions\Connector;
use Eresus\CmsBundle\AdminUI;
use Eresus\CmsBundle\ClientUI;

/**
 * Класс приложения Eresus CMS
 *
 * @package Eresus
 */
class Eresus_CMS
{
    /**
     * Создаваемая страница
     * @var \Eresus\CmsBundle\WebPage
     * @since 3.00
     */
    protected $page;

    /**
     * Старое ядро
     * @var Eresus
     * @since 4.00
     */
    protected static $legacyKernel;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Основной метод приложения
     *
     * @return Response
     */
    public function main()
    {
        $this->container->get('extensions');

        //self::$legacyKernel = new Eresus($this->container);
        $this->initConf();

        $i18n = Eresus_I18n::getInstance();
        TemplateSettings::setGlobalValue('i18n', $i18n);
        $response = Eresus_CMS::getLegacyKernel()->init();
        if ($response)
        {
            return $response;
        }
        TemplateSettings::setGlobalValue('Eresus', Eresus_CMS::getLegacyKernel());

        $this->initWeb();

        $output = '';
        /** @var Request $request */
        $request = $this->container->get('request');

        switch (true)
        {
            case strpos($request->getLocalUrl(), '/ext-3rd') === 0:
                ob_start();
                $this->call3rdPartyExtension();
                $output = ob_get_clean();
                $response = new Response($output);
                break;
            case strpos($request->getLocalUrl(), '/admin') === 0:
                $response = $this->runWebAdminUi();
                break;
            default:
                $response = $this->runWebClientUi();
        }

        return $response;
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
    public function fatalError($error = null, $exit = true)
    {
        include __DIR__ . '/../fatal.html.php';
        die;
    }

    /**
     * Возвращает корневую папку приложения
     *
     * @return string
     */
    public function getFsRoot()
    {
        return __DIR__ . '/../..';
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
        return self::$legacyKernel;
    }

    /**
     * Возвращает экземпляр класса Eresus_ClientUI или Eresus_AdminUI
     *
     * Метод нужен до отказа от переменной $page
     *
     * @return \Eresus\CmsBundle\WebPage
     *
     * @since 3.00
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Инициализация Web
     */
    protected function initWeb()
    {
        eresus_log(__METHOD__, LOG_DEBUG, '()');

        Core::setValue('core.template.templateDir', $this->getFsRoot());
        Core::setValue('core.template.compileDir', $this->getFsRoot() . '/var/cache/templates');

        //$this->response = new HttpResponse();

        /** @var Request $request */
        $request = $this->container->get('request');
        TemplateSettings::setGlobalValue('siteRoot',
            $request->getScheme() . '://' . $request->getHost() . $request->getBasePath());

        /** @var Twig_Environment $twigEnv */
        $twigEnv = $this->container->get('twig');
        $twigEnv->addExtension(new Eresus_Twig_Extension());

        /** @var Twig_Loader_Filesystem $loader */
        $loader = $this->container->get('twig.loader');
        $loader->addPath($this->getFsRoot());

        //$this->initRoutes();
    }

    /**
     * Запуск КИ
     * @return Response
     * @deprecated Это временная функция
     */
    protected function runWebClientUi()
    {
        eresus_log(__METHOD__, LOG_DEBUG, 'This method is temporary.');

        $this->page = new ClientUI();
        $this->page->setContainer($this->container);
        $this->page->init();
        return $this->page->render();
    }

    /**
     * Запуск АИ
     * @return Response
     * @deprecated Это временная функция
     */
    protected function runWebAdminUi()
    {
        $this->page = new AdminUI();
        $this->page->setContainer($this->container);
        return $this->page->render();
    }

    /**
     * Инициализация конфигурации
     */
    protected function initConf()
    {
        eresus_log(__METHOD__, LOG_DEBUG, '()');

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

    /**
     * Обрабатывает запрос к стороннему расширению
     *
     * Вызов производится через коннектор этого расширения
     *
     * @return void
     */
    protected function call3rdPartyExtension()
    {
        /** @var Request $request */
        $request = $this->container->get('request');
        $extension = substr($request->getLocalUrl(), 9);
        $extension = substr($extension, 0, strpos($extension, '/'));

        $filename = $this->getFsRoot().'/ext-3rd/'.$extension.'/eresus-connector.php';
        if ($extension && is_file($filename))
        {
            /** @noinspection PhpIncludeInspection */
            include_once $filename;
            $className = $extension.'Connector';
            /** @var Connector $connector */
            $connector = new $className;
            $connector->proxy();
        }
        else
        {
            header('404 Not Found', true, 404);
            echo '404 Not Found';
        }
    }
}

