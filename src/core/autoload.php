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
        static $bcClasses = array('HttpRequest', 'Plugin', 'Template');

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
            /*
             * Doctrine при загрузке сущностей ищет необязательный класс с суффиксом «Table».
             * Отсутствие такого класса не является ошибкой. Отсутствие любого другого класса расцениваем
             * как логическую ошибку.
             */
            elseif (substr($class, -5) !== 'Table')
            {
                throw new LogicException('Class "' . $class . '" not found');
            }
        }
        elseif (stripos($class, 'Botobor') === 0)
        /*
         * Классы Botobor
         */
        {
            $fileName = __DIR__ . '/botobor/botobor.php';

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
    }
);

