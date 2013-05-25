<?php
/**
 * Шаблоны плагина
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

/**
 * Шаблоны плагина
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_Plugin_Templates
{
    /**
     * Плагин
     * @var Eresus_Plugin
     * @since 3.01
     */
    private $plugin;

    /**
     * @param Eresus_Plugin $plugin
     * @since 3.01
     */
    public function __construct(Eresus_Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Возвращает шаблон для административного интерфейса
     *
     * @param string $filename  имя файла шаблона относительно папки административных шаблонов
     *
     * @return Template
     *
     * @since 3.01
     */
    public function admin($filename)
    {
        return new Template();
    }

    /**
     * Возвращает шаблон для административного интерфейса
     *
     * @param string $filename  имя файла шаблона относительно папки клиентских шаблонов
     *
     * @return Template
     *
     * @since 3.01
     */
    public function client($filename)
    {
        return new Template();
    }
}

