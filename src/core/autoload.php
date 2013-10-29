<?php
/**
 * Автозагрузчик классов
 *
 * Работает только для классов «Eresus_*». Из имени класса удаляется префикс «Eresus_», все
 * символы в имени класса «_» заменяются на разделитель директорий, добавляется суффикс «.php».
 *
 * Таким образом класс «Eresus_HTTP_Request» будет искаться в файле «core/HTTP/Request.php».
 *
 * Устанавливается через {@link spl_autoload_register() spl_autoload_register()} при подключении
 * этого файла.
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
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

spl_autoload_register(
    /**
     *
     * @param string $class
     *
     * @throws LogicException если класс не найден
     *
     * @since 3.00
     */
    function ($class)
    {
        static $map = array(
            'AdminList' => 'lib/admin/lists.php',
            'EresusAccounts' => 'lib/accounts.php',
            'EresusForm' => 'EresusForm.php',
            'EresusCollection' => 'classes/helpers/EresusCollection.php',
            'Form' => 'lib/forms.php',
            'I18n' => 'i18n.php',
            'MySQL' => 'lib/mysql.php',
            'mysql' => 'lib/mysql.php',
            'PaginationHelper' => 'classes/helpers/PaginationHelper.php',
            'Sections' => 'lib/sections.php',
            'TAdminUI' => 'admin.php',
            'TClientUI' => 'client.php',
            'TContentPlugin' => 'classes/backward/TContentPlugin.php',
            'Templates' => 'lib/templates.php',
            'TListContentPlugin' => 'classes/backward/TListContentPlugin.php',
            'TSettings' => 'settings.php',
            'WebServer' => 'classes/WebServer.php',
            'WebPage' => 'classes/WebPage.php',
            /* DB */
            'DBSettings' => 'framework/core/DB/DB.php',
            'DBRuntimeException' => 'framework/core/DB/DB.php',

            /* Template */
            'Template' => 'framework/core/Template/Template.php',
            'TemplateFile' => 'framework/core/Template/Template.php',
            'TemplateSettings' => 'framework/core/Template/Template.php',

            /* WWW */
            'HTTP' => 'framework/core/WWW/HTTP/HTTP.php',
            'HttpHeader' => 'framework/core/WWW/HTTP/HttpHeaders.php',
            'HttpHeaders' => 'framework/core/WWW/HTTP/HttpHeaders.php',
            'HttpMessage' => 'framework/core/WWW/HTTP/HttpMessage.php',
            'HttpResponse' => 'framework/core/WWW/HTTP/HttpResponse.php',
        );

        static $bcClasses = array('DB', 'HttpRequest', 'Plugin', 'Plugins', 'Template');

        if (stripos($class, 'Eresus_') === 0)
        /*
         * Классы Eresus
         */
        {
            $fileName = __DIR__ . '/' . str_replace('_', '/', substr($class, 7)) . '.php';

            if (file_exists($fileName))
            {
                /** @noinspection PhpIncludeInspection */
                include $fileName;
            }
        }
        elseif (in_array($class, $bcClasses))
        /*
         * Классы для обратной совместимости
         */
        {
            include_once __DIR__ . '/backward.php';
        }
        elseif (array_key_exists($class, $map))
            /*
             * Старые классы
             */
        {
            /** @noinspection PhpIncludeInspection */
            include_once __DIR__ . '/' . $map[$class];
        }
    }
);

/**
 * Автозагрузчик Composer
 */
include_once __DIR__ . '/vendor/autoload.php';

/**
 * Подключаем Dwoo
 */
include_once __DIR__ . '/framework/core/3rdparty/dwoo/dwooAutoload.php';

/*
 * eZ Components
 */
set_include_path(__DIR__ . '/framework/core/3rdparty/ezcomponents' . PATH_SEPARATOR
    . get_include_path());
include_once 'Base/src/base.php';
spl_autoload_register(array('ezcBase', 'autoload'));

