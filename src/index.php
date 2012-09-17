<?php
/**
 * ${product.title}
 *
 * Запускающий скрипт для режима Web
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
 * @package Eresus
 *
 * $Id$
 */

use Symfony\Component\HttpFoundation\Request;

// Временно включаем вывод ошибок, пока не инициализированы средства журналирования
ini_set('display_errors', true);

/*
 * Установка имени файла журнала
 * ВАЖНО! Путь должен существовать быть доступен для записи скриптам PHP.
 */
ini_set('error_log', __DIR__ . '/var/log/eresus.log');

/**
 * Уровень детализации журнала
 */
define('ERESUS_LOG_LEVEL', LOG_ERR);

define('ERESUS_APP_ROOT', __DIR__);

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require_once __DIR__ . '/app/autoload.php';
$loader->add('Eresus_', ERESUS_APP_ROOT . '/core');

$kernel = new Eresus_Kernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

