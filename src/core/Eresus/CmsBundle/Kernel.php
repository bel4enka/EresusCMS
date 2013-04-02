<?php
/**
 * Ядро CMS
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

namespace Eresus\CmsBundle;

use ErrorException;
use Composer\Autoload\ClassLoader;
use Symfony\Component\HttpKernel\Kernel as ParentKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

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
class Kernel extends ParentKernel
{
    /**
     * Размер резервного буфера для отлова ошибок переполнения памяти (в Кб)
     *
     * @var int
     */
    const MEMORY_OVERFLOW_BUFFER_SIZE = 64;

    /**
     * @var ClassLoader
     * @since 4.00
     */
    private $classLoader;

    /**
     * Резервный буфер для отлова ошибок переполнения памяти
     *
     * @var string
     * @since 4.00
     */
    private $memoryOverflowBuffer = '';

    /**
     * Конструктор ядра
     *
     * @param string $environment  окружение (prod, test, dev)
     * @param bool   $debug        включить или нет отладку
     *
     * @since 4.00
     */
    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);

        /*
         * Установка имени файла журнала
         * ВАЖНО! Путь должен существовать быть доступен для записи скриптам PHP.
         */
        ini_set('error_log', __DIR__ . '/../../../var/logs/eresus.log');

        /**
         * Уровень детализации журнала
         */
        define('ERESUS_LOG_LEVEL', $debug ? LOG_DEBUG : LOG_ERR);

        /**
         * Подключение Eresus Core
         */
        require __DIR__ . '/../../framework/core/eresus-core.php';

        // Устанавливаем кодировку по умолчанию для операций mb_*
        mb_internal_encoding('utf-8');

        /* Предотвращает появление ошибок, связанных с неустановленной временной зоной */
        @$timezone = date_default_timezone_get();
        date_default_timezone_set($timezone);

        $this->initExceptionHandling();
    }

    /**
     * Устанавливает автозагрузчик классов
     *
     * @param ClassLoader $loader
     *
     * @return void
     *
     * @since 4.00
     */
    public function setClassLoader(ClassLoader $loader)
    {
        $this->classLoader = $loader;
    }

    /**
     * Возвращает автозагрузчик классов
     *
     * @return ClassLoader
     * @since 4.00
     */
    public function getClassLoader()
    {
        return $this->classLoader;
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
     */
    private function initExceptionHandling()
    {
        /* Резервируем буфер на случай переполнения памяти */
        $this->memoryOverflowBuffer =
            str_repeat('x', self::MEMORY_OVERFLOW_BUFFER_SIZE * 1024);

        /* Меняем значения php.ini */
        ini_set('html_errors', 0); // Немного косметики

        set_error_handler(array($this, 'errorHandler'));
        //Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Error handler installed');

        //set_exception_handler('Eresus_Kernel::handleException');
        //Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Exception handler installed');

        /*
         * В PHP нет стандартных методов для перехвата некоторых типов ошибок (например E_PARSE или
         * E_ERROR), однако способ всё же есть — зарегистрировать функцию через ob_start.
         * Но только не в режиме CLI.
         */
        // @codeCoverageIgnoreStart
        if (PHP_SAPI != 'cli')
        {
            if (ob_start(array($this, 'fatalErrorHandler'), 4096))
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
    public function fatalErrorHandler($output)
    {
        // Освобождает резервный буфер
        unset($this->memoryOverflowBuffer);
        if (preg_match('/(parse|fatal) error:.*in .* on line/Ui', $output, $m))
        {
            $GLOBALS['ERESUS_CORE_FATAL_ERROR_HANDLER'] = true;
            if ($this->getEnvironment() == 'prod')
            {
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
                $message .= "\nSee application log for more info.\n";
            }
            else
            {
                $message = $output;
            }

            //Eresus_Logger::log(__FUNCTION__, $priority, trim($output));
            if (PHP_SAPI != 'cli')
            //@codeCoverageIgnoreStart
            {
                header('Internal Server Error', true, 500);
                header('Content-type: text/plain', true);
            }
            //@codeCoverageIgnoreEnd

            return $message;
        }
        $this->memoryOverflowBuffer =
            str_repeat('x', self::MEMORY_OVERFLOW_BUFFER_SIZE * 1024);

        // возвращаем false для вывода буфера
        return false;
    }

    /**
     * Возвращает используемые пакеты
     *
     * @return array
     *
     * @since 4.00
     */
    public function registerBundles()
    {
        $bundles = array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \JMS\AopBundle\JMSAopBundle(),
            new \JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new \JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new \Eresus\CommonBundle\CommonBundle(),
            new \Eresus\ORMBundle\ORMBundle(),
            new \Eresus\CmsBundle\CmsBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test')))
        {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    /**
     * Регистрирует пакет во время работы приложения
     *
     * @param Bundle $bundle
     */
    public function registerBundle(Bundle $bundle)
    {
        $this->bundleMap[$bundle->getName()] = array($bundle);
        $bundle->boot();

        /*
         * Регистрируем классы сущностей модуля
         */
        /** @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine */
        $doctrine = $this->getContainer()->get('doctrine');
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $doctrine->getManager();
        /** @var \Doctrine\ORM\Mapping\Driver\DriverChain $chain */
        $chain = $em->getConfiguration()->getMetadataDriverImpl();
        $driver = new AnnotationDriver(new AnnotationReader(), $bundle->getPath() . '/Entity');
        $chain->addDriver($driver, $bundle->getNamespace());
    }

    /**
     * Возвращает настройки контейнера служб
     *
     * @param LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/core/Eresus/CmsBundle/Resources/config/config_'
            . $this->getEnvironment() . '.yml');
    }

    /**
     * Возвращает корневую папку приложения
     *
     * @return string
     */
    public function getRootDir()
    {
        if (null === $this->rootDir)
        {
            $this->rootDir = str_replace('\\', '/', realpath(__DIR__ . '/../../..'));
        }

        return $this->rootDir;
    }

    /**
     * Возвращает папку журналов
     *
     * @return string
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

