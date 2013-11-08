<?php
/**
 * Подключение библиотеки JavaScript
 *
 * @package Eresus
 */

/**
 * Подключает библиотеку JavaScript
 *
 * @see Eresus\Templating\Page::linkJsLib()
 * @since 2.16
 */
function Dwoo_Plugin_jslib(Dwoo $dwoo, $name)
{
    $args = func_get_args();
    array_shift($args);
    call_user_func_array(array($GLOBALS['page'], 'linkJsLib'), $args);
    return '';
}
