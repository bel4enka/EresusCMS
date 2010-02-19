<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Помощник для компонентных тестов
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
 * @package Tests
 *
 * $Id$
 */


define('ERESUS_TEST_MODE', true);

if (!defined('yes'))
	define('yes', true);

if (!defined('no'))
	define('no', false);

define('TEST_DIR_ROOT', realpath(dirname(__FILE__) . '/../..'));
set_include_path(get_include_path() . PATH_SEPARATOR . TEST_DIR_ROOT);

require_once 'PHPUnit/Extensions/OutputTestCase.php';

/* Настраиваем фильтрацию Code Covarage */
PHPUnit_Util_Filter::addDirectoryToWhitelist(TEST_DIR_ROOT . '/core');
PHPUnit_Util_Filter::removeDirectoryFromWhitelist(TEST_DIR_ROOT . '/core/framework');
PHPUnit_Util_Filter::addFileToFilter(TEST_DIR_ROOT . '/core/errors.html.php');
PHPUnit_Util_Filter::addFileToFilter(TEST_DIR_ROOT . '/core/gziph.php');


$GLOBALS['TESTCONF'] = parse_ini_file(TEST_DIR_ROOT . '/tests/component/tests.conf', true);

ini_set('error_log', TEST_DIR_ROOT . '/tests/component/tests.log');
define('ERESUS_LOG_LEVEL' , LOG_DEBUG);

#@include_once 'core/framework/core/3rdparty/ezcomponents/Base/src/ezc_bootstrap.php';
require_once 'core/framework/core/eresus-core.php';

Core::testMode(true);

if (isset($GLOBALS['TESTCONF']['DB']['dsn']) && $GLOBALS['TESTCONF']['DB']['dsn']) {

	DB::lazyConnection($GLOBALS['TESTCONF']['DB']['dsn']);

}
