<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Запускающий скрипт
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-${build.year}, Eresus Project, http://eresus.ru/
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
 * @subpackage Main
 *
 * $Id$
 */

/*
 * Установка имени файла журнала
 */
ini_set('error_log', 'cms.log');

/**
 * Уровень детализации журнала
 */
define('ERESUS_LOG_LEVEL' , LOG_WARNING);


/**
 * Путь к Eresus Framework
 */
if ( !defined('ERESUS_FRAMEWORK_ROOT') ) {

	define('ERESUS_FRAMEWORK_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'framework');

}

set_include_path(get_include_path() . PATH_SEPARATOR . ERESUS_FRAMEWORK_ROOT);


/**
 * Подключение Eresus Framework
 */
include_once 'EresusFramework.php';

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . DIRECTORY_SEPARATOR . 'core');
/**
 * Подключение CMS
 */
include_once 'EresusCMS.php';

/*
 * Запуск CMS
 */
Core::exec('EresusCMS');
