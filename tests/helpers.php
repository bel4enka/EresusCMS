<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Вспомогательный файл для модульных тестов
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
 * @subpackage Tests
 *
 * $Id$
 */

/* Устанавливаем путь для подключения тестируемых файлов */
if ( !defined('ERESUS_TEST_ROOT') )
	define('ERESUS_TEST_ROOT', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'));

set_include_path(get_include_path() . PATH_SEPARATOR . ERESUS_TEST_ROOT);

/* Включаем в Eresus режим тестирования и режит отладки */
ini_set('error_log', 'debug.log');
define('ERESUS_LOG_LEVEL' , LOG_DEBUG);
$GLOBALS['ERESUS_CORE_TESTMODE'] = array();

if ( !defined('ERESUS_ROOT') )
	define('ERESUS_ROOT', ERESUS_TEST_ROOT . DIRECTORY_SEPARATOR . 'core'  . DIRECTORY_SEPARATOR . 'framework');

set_include_path(get_include_path() . PATH_SEPARATOR . ERESUS_ROOT);

/* Настраиваем фильтрацию Code Covarage */
PHPUnit_Util_Filter::addDirectoryToFilter(ERESUS_TEST_ROOT . DIRECTORY_SEPARATOR . 'tests');
PHPUnit_Util_Filter::addDirectoryToFilter(ERESUS_ROOT);

/**
 * Подключение Eresus Core
 */
require 'EresusFramework.php';
Core::testMode(true);

set_include_path(get_include_path() . PATH_SEPARATOR . ERESUS_TEST_ROOT . DIRECTORY_SEPARATOR . 'core');

#TODO: Временная необходимость
include_once 'lang/ru.php';

/* Удаляем локальные переменные */

