<?php
/**
 * Пункт меню
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
 */

namespace Eresus\UI\Menu;

use Eresus\UI\Widget;

/**
 * Пункт меню
 *
 * @api
 * @since 3.01
 */
class MenuItem extends Widget
{
    /**
     * Текст пункта
     *
     * @var string
     *
     * @since 3.01
     */
    protected $caption;

    /**
     * URL
     *
     * @var string
     *
     * @since 3.01
     */
    protected $url;

    /**
     * @param string $caption
     * @param string $url
     */
    public function __construct($caption, $url)
    {
        $this->caption = $caption;
        $this->url = $url;
    }

    /**
     * Возвращает текст пункта меню
     *
     * @return string
     *
     * @since 3.01
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Возвращает URL
     *
     * @return string
     *
     * @since 3.01
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Возвращает подменю
     *
     * @return null|Menu
     *
     * @since 3.01
     */
    public function getSubMenu()
    {
        return null;
    }
}

