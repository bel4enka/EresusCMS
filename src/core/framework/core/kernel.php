<?php

/**
 * @deprecated с 3.01 используйте {@link Eresus_Kernel::log()}
 */
function eresus_log()
{
    call_user_func_array(array('Eresus_Kernel', 'log'), func_get_args());
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
