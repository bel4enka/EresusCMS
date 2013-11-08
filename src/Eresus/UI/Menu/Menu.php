<?php
/**
 * Меню
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
 * Меню
 *
 * @since 3.01
 */
class Menu extends Widget
{
    /**
     * Пункты меню
     *
     * @var MenuItem[]
     *
     * @since 3.01
     */
    protected $items = array();

    /**
     * Добавляет пункт к меню
     *
     * @param MenuItem $item
     *
     * @return Menu
     *
     * @since 3.01
     */
    public function add(MenuItem $item)
    {
        $item->setTemplateManager($this->getTemplateManager());
        $this->items []= $item;
        return $this;
    }

    /**
     * Возвращает пункты меню
     *
     * @return MenuItem[]
     *
     * @since 3.01
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Возвращает уровень вложенности этого меню
     *
     * @return int
     *
     * @since 3.01
     */
    public function getLevel()
    {
        return 1;
    }
}

