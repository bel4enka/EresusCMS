<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Запускающий скрипт
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 * @package EresusCMS
 *
 * $Id$
 */

/*
 * Установка имени файла журнала
 * ВАЖНО! Путь должен существовать быть доступен для записи скриптам PHP.
 */
ini_set('error_log', dirname(__FILE__) . '/var/log/eresus.log');

/**
 * Уровень детализации журнала
 */
define('ERESUS_LOG_LEVEL' , ${log.level});

ini_set('track_errors', true);
/**
 * Подключение Eresus Core
 */
include_once 'core/framework/core/eresus-core.compiled.php';

if ($php_errormsg)
{
	die($php_errormsg);
}
ini_set('track_errors', false);

try
{
	/**
	 * Подключение Doctrine
	 */
	include_once 'core/Doctrine.php';
	spl_autoload_register(array('Doctrine', 'autoload'));
	spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));

	/**
	 * Подключение главного приложения
	 */
	include_once 'core/main.php';
}
catch (Exception $e)
{
	die($e->getMessage());
}
/*
 * Запуск CMS
 */
Core::exec('EresusCMS');
