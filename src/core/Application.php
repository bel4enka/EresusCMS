<?php
/**
 * Абстрактное приложение
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
 * Абстрактное приложение
 *
 * @see main()
 * @see Eresus_Kernel::exec()
 *
 * @package Eresus
 */
abstract class Eresus_Application
{
    /**
     * Корневая папка приложения
     *
     * Устанавливается в {@link initFS()}. Используйте {@link getFsRoot()} для чтения значения.
     *
     * @var string
     * @see getFsRoot(), initFS()
     */
    protected $fsRoot;

    /**
     * Main application function
     *
     * Developer must implement this method in his application.
     *
     * This method will be called by {@link Core::exec()}.
     *
     * <code>
     * class MyApp extends Eresus_Application {
     *
     *   public function main()
     *   {
     *     // Main code of your application goes here:
     *     // 1. You can do some init tasks
     *     // 2. You can do some usefull job ;-)
     *     // 3. At the end you can do some finalizing tasks
     *     return $exitCode;
     *   }
     * }
     * </code>
     *
     * @return int  Exit code
     * @see Core::exec()
     */
    abstract public function main();
    //-----------------------------------------------------------------------------

    /**
     * Constructor
     *
     * 1. Inits FS related parts of application
     * 2. If application has method called 'autoload' registers it through
     *    {@link Core::registerAutoloader}
     *
     * There is no need to call constructor directly. It will be called
     * automaticly from {@link Core::exec()}
     *
     * @return Eresus_Application
     * @see initFS(), Core::exec(), Core::registerAutoloader()
     */
    function __construct()
    {

        $this->initFS();
        if (method_exists($this, 'autoload'))
            Core::registerAutoloader(array($this, 'autoload'));

    }
    //-----------------------------------------------------------------------------

    /**
     * Init FS related parts of application
     *
     * - Sets {@link fsRoot} by calling {@link detectFsRoot}
     *
     * @return void
     *
     * @see fsRoot, detectFsRoot()
     */
    protected function initFS()
    {
        $this->fsRoot = $this->detectFsRoot();
    }
    //-----------------------------------------------------------------------------

    /**
     * Trying to determine application root directory
     *
     * In CLI mode $GLOBALS['argv'][0] used.
     *
     * In other modes $_SERVER['SCRIPT_FILENAME'] used.
     *
     * @return string
     * @see fsRoot, getFsRoot()
     */
    protected function detectFsRoot()
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'start');
        switch (true)
        {
            case Eresus_Kernel::isCLI():
                $path = reset($GLOBALS['argv']);
                Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Using global $argv variable: %s', $path);
                $path = FS::canonicalForm($path);
                $path = FS::dirname($path);
                break;
            default:
                Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Using $_SERVER["SCRIPT_FILENAME"]: %s',
                    $_SERVER['SCRIPT_FILENAME']);

                $path = FS::canonicalForm($_SERVER['SCRIPT_FILENAME']);
                $path = FS::dirname($path);
        }

        $path = FS::normalize($path);
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '"%s"', $path);

        return $path;
    }

    /**
     * Get application root directory
     *
     * @return string
     * @see fsRoot
     */
    public function getFsRoot()
    {
        return $this->fsRoot;
    }
    //-----------------------------------------------------------------------------

}

