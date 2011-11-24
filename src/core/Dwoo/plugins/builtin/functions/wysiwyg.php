<?php
/**
 * Вывод визуального редактора
 *
 * @package Eresus
 */

/**
 * Вывод визуального редактора
 *
 * @since 2.16
 */
function Dwoo_Plugin_wysiwyg(Dwoo $dwoo, $name, $value = '', $height = 200)
{
	$wysiwyg = $GLOBALS['Eresus']->extensions->load('forms', 'html');
	$field = array(
		'name' => $name,
		'value' => $value,
		'height' => $height,
	);

	return $wysiwyg->getWYSIWYG($field);
}
