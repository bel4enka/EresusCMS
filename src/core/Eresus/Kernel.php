<?php
/**
 * ${product.title}
 *
 * Ядро
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

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\DependencyInjection\Container;

/**
 * Ядро CMS
 *
 * Основные функции ядра
 * 1. запуск {@link Eresus_CMS основного класса приложения};
 * 2. перехват ошибок и исключений;
 * 3. {@link autoload() автозагрузка классов};
 * 4. получение основных сведений о системе.
 *
 * @package Eresus
 * @since 3.00
 */
class Eresus_Kernel extends Kernel
{
    /**
     * Резервный буфер для отлова ошибок переполнения памяти (в Кб)
     *
     * @var int
     */
    const MEMORY_OVERFLOW_BUFFER_SIZE = 64;

    /**
     * Для тестирования
     *
     * @var bool
     * @ignore
     */
    private static $override_isCLI = null;

    /**
     * Конструктор ядра
     *
     * @param string $environment  окружение (prod, test, dev)
     * @param bool   $debug        включить или нет отладку
     *
     * @since 3.01
     */
    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);

        /*
         * Установка имени файла журнала
         * ВАЖНО! Путь должен существовать быть доступен для записи скриптам PHP.
         */
        ini_set('error_log', ERESUS_PATH . '/var/logs/eresus.log');

        /**
         * Уровень детализации журнала
         */
        define('ERESUS_LOG_LEVEL', $debug ? LOG_DEBUG : LOG_ERR);

        /**
         * Подключение Eresus Core
         */
        require ERESUS_PATH . '/core/framework/core/eresus-core.php';

        // Устанавливаем кодировку по умолчанию для операций mb_*
        mb_internal_encoding('utf-8');

        /* Предотвращает появление ошибок, связанных с неустановленной временной зоной */
        @$timezone = date_default_timezone_get();
        date_default_timezone_set($timezone);

        self::initExceptionHandling();
    }

    /**
     * Инициализирует обработчики ошибок
     *
     * Этот метод:
     * 1. резервирует в памяти буфер, освобождаемый для обработки ошибок нехватки памяти;
     * 2. отключает HTML-оформление стандартных сообщений об ошибках;
     * 3. регистрирует {@link errorHandler()};
     * 4. регистрирует {@link fatalErrorHandler()}.
     *
     * @return void
     *
     * @since 3.00
     * @uses Eresus_Logger::log()
     */
    private static function initExceptionHandling()
    {
        /* Резервируем буфер на случай переполнения памяти */
        $GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER'] =
            str_repeat('x', self::MEMORY_OVERFLOW_BUFFER_SIZE * 1024);

        /* Меняем значения php.ini */
        ini_set('html_errors', 0); // Немного косметики

        set_error_handler(array('Eresus_Kernel', 'errorHandler'));
        //Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Error handler installed');

        //set_exception_handler('Eresus_Kernel::handleException');
        //Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Exception handler installed');

        /*
         * В PHP нет стандартных методов для перехвата некоторых типов ошибок (например E_PARSE или
         * E_ERROR), однако способ всё же есть — зарегистрировать функцию через ob_start.
         * Но только не в режиме CLI.
         */
        // @codeCoverageIgnoreStart
        if (! self::isCLI())
        {
            if (ob_start(array('Eresus_Kernel', 'fatalErrorHandler'), 4096))
            {
                //Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Fatal error handler installed');
            }
            else
            {
                /*Eresus_Logger::log(
                    LOG_NOTICE, __METHOD__,
                    'Fatal error handler not installed! Fatal error will be not handled!'
                );*/
            }
        }
        // @codeCoverageIgnoreEnd
    }
    //-----------------------------------------------------------------------------

    /**
     * Обработчик ошибок
     *
     * Обработчик ошибок, устанавливаемый через {@link set_error_handler() set_error_handler()} в
     * методе {@link initExceptionHandling()}. Все ошибки важнее E_NOTICE превращаются в исключения
     * {@link http://php.net/ErrorException ErrorException}.
     *
     * @param int    $errno    тип ошибки
     * @param string $errstr   описание ошибки
     * @param string $errfile  имя файла, в котором произошла ошибка
     * @param int    $errline  строка, где произошла ошибка
     *
     * @throws ErrorException
     *
     * @return bool
     *
     * @since 3.00
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        /* Нулевое значение 'error_reporting' означает что был использован оператор "@" */
        if (error_reporting() == 0)
        {
            return true;
        }

        /*
         *  Примечание: На самом деле этот метод обрабатывает только E_WARNING, E_NOTICE, E_USER_ERROR,
         *  E_USER_WARNING, E_USER_NOTICE и E_STRICT
         */

        /* Определяем серьёзность ошибки */
        switch ($errno)
        {
            case E_STRICT:
            case E_NOTICE:
            case E_USER_NOTICE:
                $level = LOG_NOTICE;
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $level = LOG_WARNING;
                break;
            default:
                $level = LOG_ERR;
        }

        if ($level < LOG_NOTICE)
        {
            throw new ErrorException($errstr, $errno, $level, $errfile, $errline);
        }
        /*else
        {
            $logMessage = sprintf(
                "%s in %s:%s",
                $errstr,
                $errfile,
                $errline
            );
            Eresus_Logger::log(__FUNCTION__, $level, $logMessage);
        }*/

        return true;
    }

    /**
     * Обработчик фатальных ошибок
     *
     * Этот обработчик пытается перехватывать сообщения о фатальных ошибках, недоступных при
     * использовании {@link set_error_handler() set_error_handler()}. Это делается через обработчик
     * {@link ob_start() ob_start()}, устанавливаемый в методе {@link initExceptionHandling()}.
     *
     * <i>Замечание по производительности</i>: этот метод освобождает в начале и выделяет в конце
     * своей работы буфер в памяти для отлова ошибок переполнения памяти. Эти операции затормаживают
     * вывод примерно на 1-2%.
     *
     * @param string $output  содержимое буфера вывода
     *
     * @return string|bool
     *
     * @since 3.00
     * @uses Eresus_Logger::log
     */
    public static function fatalErrorHandler($output)
    {
        // Освобождает резервный буфер
        unset($GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER']);
        if (preg_match('/(parse|fatal) error:.*in .* on line/Ui', $output, $m))
        {
            $GLOBALS['ERESUS_CORE_FATAL_ERROR_HANDLER'] = true;
            switch (strtolower($m[1]))
            {
                case 'fatal':
                    $message = 'FATAL ERROR';
                    break;
                case 'parse':
                    $message = 'PARSE ERROR';
                    break;
                default:
                    $message = 'ERROR:';
            }

            //Eresus_Logger::log(__FUNCTION__, $priority, trim($output));
            if (!self::isCLI())
            //@codeCoverageIgnoreStart
            {
                header('Internal Server Error', true, 500);
                header('Content-type: text/plain', true);
            }
            //@codeCoverageIgnoreEnd

            return $message . "\nSee application log for more info.\n";
        }
        $GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER'] =
            str_repeat('x', self::MEMORY_OVERFLOW_BUFFER_SIZE * 1024);

        // возвращаем false для вывода буфера
        return false;
    }

    /**
     * Возвращает true если PHP запущен на UNIX-подобной ОС
     *
     * @return bool
     *
     * @since 3.00
     */
    public static function isUnixLike()
    {
        return DIRECTORY_SEPARATOR == '/';
    }

    /**
     * Возвращает true если PHP запущен на Microsoft® Windows™
     *
     * @return bool
     *
     * @since 3.00
     */
    public static function isWindows()
    {
        return strncasecmp(PHP_OS, 'WIN', 3) == 0;
    }

    /**
     * Возвращает true если PHP запущен на MacOS
     *
     * @return bool
     *
     * @since 3.00
     */
    public static function isMac()
    {
        return strncasecmp(PHP_OS, 'MAC', 3) == 0;
    }

    /**
     * Возвращает true, если используется
     * {@link http://php.net/manual/en/features.commandline.php CLI}
     * {@link http://php.net/manual/en/function.php-sapi-name.php SAPI}
     *
     * @return bool
     *
     * @since 3.00
     */
    public static function isCli()
    {
        //@codeCoverageIgnoreStart
        if (self::$override_isCLI !== null)
        {
            return self::$override_isCLI;
        }
        //@codeCoverageIgnoreEnd

        return PHP_SAPI == 'cli';
    }

    /**
     * Возвращает true, если используется CGI
     * {@link http://php.net/manual/en/function.php-sapi-name.php SAPI}
     *
     * @return bool
     *
     * @since 3.00
     */
    public static function isCgi()
    {
        return strncasecmp(PHP_SAPI, 'CGI', 3) == 0;
    }

    /**
     * Возвращает true, если используется
     * {@link http://php.net/manual/en/function.php-sapi-name.php SAPI} модуля веб-сервера
     *
     * @return bool
     *
     * @since 3.00
     */
    public static function isModule()
    {
        return !self::isCGI() && isset($_SERVER['GATEWAY_INTERFACE']);
    }

    /**
     * Проверяет, объявлен ли указанный класс или интерфейс
     *
     * Этот метод не инициирует автозагрузку.
     *
     * @param string $name  имя класса или интерфейса
     * @return bool true если класс или интерфейс $name объявлен
     *
     * @since 3.00
     */
    public static function classExists($name)
    {
        return class_exists($name, false) || interface_exists($name, false);
    }

    /**
     * Создаёт экземпляр приложения и выполняет его
     *
     * Класс приложения должен содержать публичный метод main(), который будет вызван после создания
     * экземпляра класса.
     *
     * @param string $class  имя класса приложения
     *
     * @throws LogicException если класс $class не найден или не содержит метода «main()»
     *
     * @return int  код завершения (0 — успешное завершение)
     *
     * @since 3.00
     * @see $app, app()
     * @uses Eresus_Logger::log()
     */
    public static function exec($class)
    {
        if (!class_exists($class))
        {
            throw new LogicException('Application class "' . $class . '" does not exists');
        }

        /** @var Eresus_CMS $app */
        $app = new $class();

        if (!method_exists($class, 'main'))
        {
            $app = null;
            throw new LogicException('Method "main()" does not exists in "' . $class . '"');
        }

        self::sc()->set('app', $app);

        try
        {
            //Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'executing %s', $class);
            $exitCode = $app->main();
            //Eresus_Logger::log(__METHOD__, LOG_DEBUG, '%s done with code: %d', $class, $exitCode);
        }
        catch (Eresus_SuccessException $e)
        {
            $exitCode = 0;
        }
        catch (Exception $e)
        {
            //self::handleException($e);
            $exitCode = $e->getCode() ? $e->getCode() : 0xFFFF;
        }
        return $exitCode;
    }

    /**
     * Возвращает контейнер служб
     *
     * @return Container
     *
     * @since 3.01
     */
    public static function sc()
    {
        /** @var Eresus_Kernel $kernel */
        $kernel = $GLOBALS['kernel'];
        return $kernel->container;
    }

    /**
     * Возвращает службу
     *
     * Доступные службы:
     *
     * - app — текущее приложение, обычно {@link Eresus_CMS}
     *
     * @param string $id  идентификатор службы
     *
     * @return object
     *
     * @since 3.01
     */
    public static function get($id)
    {
        return self::sc()->get($id);
    }

    /**
     * Возвращает выполняемое приложение или null, если приложение не запущено
     *
     * Пример: получение корневой директории приложения.
     *
     * <code>
     * $appRootDir = Eresus_Kernel::app()->getRootDir();
     * </code>
     *
     * @return Eresus_CMS  выполняемое приложение
     *
     * @see $app, exec()
     * @since 3.00
     */
    public static function app()
    {
        return self::sc()->get('app');
    }

    /**
     * Возвращает используемые пакеты
     *
     * @return array
     *
     * @since 3.01
     */
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new Eresus\CmsBundle\EresusCmsBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test')))
        {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    /**
     * Возвращает настройки контейнера служб
     *
     * @param Symfony\Component\Config\Loader\LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/cfg/config_' . $this->getEnvironment() . '.yml');
    }

    /**
     * Возвращает корневую папку приложения (app/)
     *
     * @return string
     */
    public function getRootDir()
    {
        if (null === $this->rootDir)
        {
            $this->rootDir = str_replace('\\', '/', realpath(__DIR__ . '/../..'));
        }

        return $this->rootDir;
    }

    /**
     * Возвращает папку журналов
     *
     * @return string
     *
     * @api
     */
    public function getLogDir()
    {
        return $this->getRootDir() . '/var/logs';
    }

    /**
     * Возвращает папку кэша
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->getRootDir() . '/var/cache/' . $this->environment;
    }
}

