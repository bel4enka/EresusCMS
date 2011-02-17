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
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
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
 * @package Core
 *
 * $Id$
 */

ini_set('display_errors', true);

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
//include_once 'core/framework/core/eresus-core.php';

if (isset($php_errormsg))
{
	die($php_errormsg);
}
ini_set('track_errors', false);

/*
 * Если есть файл install.php, запускаем инсталлятор, а не CMS
 */
if (is_file('install.php'))
{
	$fileName = 'install.php';
	$appName = 'Installer';
}
else
{
	$fileName = 'core/CMS.php';
	$appName = 'Eresus_CMS';
}


try
{
	/**
	 * Подключение главного приложения
	 */
	include_once $fileName;
}
catch (Exception $e)
{
	die('Can not include file "' . $fileName . '". Is it present and accessible?');
}

/*
 * Запуск приложения
 */
Core::exec($appName);
