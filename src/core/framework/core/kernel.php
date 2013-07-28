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
     * Internal registry
     *
     * @see getValue, setValue, unsetValue
     *
     * @var array
     */
    static private $registry = array();

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
