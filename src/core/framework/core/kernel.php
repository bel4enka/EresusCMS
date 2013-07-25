<?php

/**
 * @deprecated с 3.01 используйте {@link Eresus_Kernel::log()}
 */
function eresus_log()
{
    call_user_func_array(array('Eresus_Kernel', 'log'), func_get_args());
}

/**
 * Main Eresus Core class
 *
 * All methods of this class are static
 *
 * @package Core
 */
class Core
{
    /**
     * Indicates initialization state:
     *  0 - Not inited
     *  1 - Init in progress
     *  2 - Init complete
     *
     * @var int  Initialization state
     */
    static private $initState = 0;

    /**
     * Internal registry
     *
     * @see getValue, setValue, unsetValue
     *
     * @var array
     */
    static private $registry = array();

    /**
     * Init Eresus Core
     */
    static public function init()
    {
        if (self::$initState)
            return;

        self::$initState = 1;

        /*
         * eZ Components
         */
        $currentDir = dirname(__FILE__);
        set_include_path($currentDir . '/3rdparty/ezcomponents' . PATH_SEPARATOR .
        get_include_path());
        include_once 'Base/src/base.php';
        spl_autoload_register(array('ezcBase', 'autoload'));

        self::$initState = 2;
    }

    /**
     * Checks if class or interface exists
     *
     * This method not triggering autoload
     *
     * @param string $name  Class or interface name
     * @return bool
     */
    static public function classExists($name)
    {
        return class_exists($name, false) || interface_exists($name, false);
    }

    /**
     * Writes exception description to log
     *
     * @param Exception $e
     */
    static public function logException($e)
    {
        $previous = $e->getPrevious();
        $trace = $e->getTraceAsString();

        $logMessage = sprintf(
            "%s in %s at %s\n%s\nBacktrace:\n%s\n",
            get_class($e),
            $e->getFile(),
            $e->getLine(),
            $e->getMessage(),
            $trace
        );
        Eresus_Kernel::log('Core', LOG_ERR, $logMessage);

        if ($previous)
        {
            self::logException($previous, 'Previous exception:');
        }
    }

    /**
     * Set value in internal registry
     *
     * @param string $key    Value name
     * @param mixed  $value  Value
     *
     * @see getValue, unsetValue
     * @link http://martinfowler.com/eaaCatalog/registry.html Registry pattern
     */
    static public function setValue($key, $value)
    {
        self::$registry[$key] = $value;
    }
    //-----------------------------------------------------------------------------

    /**
     * Get value from internal registry
     *
     * @param string $key                 Value name
     * @param mixed  $default [optional]  Optional default if value not set
     * @return mixed  Value or $default or null
     *
     * @see setValue, unsetValue
     */
    static public function getValue($key, $default = null)
    {
        if (isset(self::$registry[$key]))
            return self::$registry[$key];
        else
            return $default;
    }
    //-----------------------------------------------------------------------------

    /**
     * Unset value in internal registry
     *
     * @param string $key
     *
     * @see getValue, unsetValue
     */
    static public function unsetValue($key)
    {
        unset(self::$registry[$key]);
    }

}


/*****************************************************************************
 *
 *   Functions
 *
 *****************************************************************************/

/**
 * Recursive slash stripping
 *
 * @param string|array $source
 * @return string|array
 *
 * @author Ghost
 */
function ecStripSlashes($source)
{
    if (is_array($source)) {

        foreach ($source as $key => $value) {
            $source[$key] = ecStripSlashes($source[$key]);
        }

    } else {

        $source = stripslashes($source);
    }

    return $source;
}
