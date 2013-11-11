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
 * Меню — это виджет, как правило навигационный, состоящий из пунктов, каждый из которых позволяет
 * выполнить некоторое действие (обычно — переход на URL). Также каждый пункт может иметь
 * собственное подменю, что позволяет создавать древовидные структуры.
 *
 * Пункты могут быть добавлены в меню вручную (см. {@link add()}), либо предоставлены поставщиком
 * пунктов меню — объектом класса, поддерживающего интерфейс {@link ItemProviderInterface}
 * (см. {@link setItemProvider()}).
 *
 * @api
 * @since 3.01
 */
class Menu extends Widget
{
    /**
     * Пункты меню
     *
     * @var MenuItem[]|null
     *
     * @since 3.01
     */
    protected $items = array();

    /**
     * Были ли получены пункты от поставщика
     *
     * @var bool
     *
     * @since 3.01
     */
    protected $populated = false;

    /**
     * Уровень вложенности
     *
     * @var int
     *
     * @since 3.01
     */
    private $level = 1;

    /**
     * Поставщик пунктов меню
     *
     * @var ItemProviderInterface|null
     *
     * @since 3.01
     */
    private $itemProvider = null;

    /**
     * Задаёт поставщика пунктов для меню
     *
     * @param ItemProviderInterface $provider
     *
     * @return Menu
     *
     * @since 3.01
     */
    public function setItemProvider(ItemProviderInterface $provider)
    {
        $this->itemProvider = $provider;
        $this->populated = false;
        return $this;
    }

    /**
     * Возвращает поставщика пунктов для меню
     *
     * @return ItemProviderInterface|null
     *
     * @since 3.01
     */
    protected function getItemProvider()
    {
        return $this->itemProvider;
    }

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
        if (!is_null($this->getTemplateManager()))
        {
            $item->setTemplateManager($this->getTemplateManager());
        }
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
        if (!is_null($this->getItemProvider()) && !$this->populated)
        {
            $this->getItemProvider()->populate($this);
        }
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
        return $this->level;
    }

    /**
     * Задаёт уровень вложенности
     *
     * @param int $level
     *
     * @since 3.01
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Создаёт и возвращает пустое подменю
     *
     * Подменю устанавливается уровень вложенности на единицу больше, чем создающего меню.
     *
     * @return Menu
     *
     * @since 3.01
     */
    public function createSubMenu()
    {
        $class = get_class($this);
        /** @var Menu $menu */
        $menu = new $class($this->getTemplateManager());
        $menu->setLevel($this->getLevel() + 1);
        return $menu;
    }
}

