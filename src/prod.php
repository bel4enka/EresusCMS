<?php
/**
 * Запускающий скрипт для режима веб
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
 */

use Doctrine\Common\Annotations\AnnotationRegistry;
use Eresus\CmsBundle\HTTP\Request;

// Временно включаем вывод ошибок, пока не инициализированы средства журналирования
$displayErrors = ini_set('display_errors', true);
// Временно включаем отслеживание ошибок
ini_set('track_errors', true);

/** @var \Composer\Autoload\ClassLoader $loader */
/** @noinspection PhpIncludeInspection */
$loader = require __DIR__ . '/vendor/autoload.php';

/* intl */
if (!function_exists('intl_get_error_code'))
{
    /** @noinspection PhpIncludeInspection */
    require_once __DIR__
        . '/vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->add(
        '',
        __DIR__ . '/vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs'
    );
}

/** @noinspection PhpParamsInspection */
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

$loader->add('Eresus_', __DIR__);

/* Если произошли какие-то ошибки, прерываем работу приложения */
if (isset($php_errormsg))
{
    die($php_errormsg);
}

// Выключаем отслеживание ошибок, теперь полагаемся на ядро
ini_set('track_errors', false);

$kernel = new Eresus\CmsBundle\Kernel('prod', true);

// Восстанавливаем состояние вывода ошибок
ini_set('track_errors', $displayErrors);

$kernel->setClassLoader($loader);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

