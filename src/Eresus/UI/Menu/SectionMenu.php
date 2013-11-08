<?php
/**
 * Меню разделов сайта
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

use Eresus\Entity\Section;
use Eresus\Sections\SectionManager;
use Eresus\Templating\TemplateManager;

/**
 * Меню разделов сайта
 *
 * @since 3.01
 */
class SectionMenu extends Menu
{
    /**
     * Корневой раздел
     *
     * @var null|Section
     *
     * @since 3.01
     */
    private $root = null;

    /**
     * Менеджер разделов
     *
     * @var SectionManager
     *
     * @since 3.01
     */
    private $sectionManager;

    /**
     * Конструктор виджета
     *
     * @param TemplateManager $templateManager
     * @param SectionManager  $sectionManager
     *
     * @since 3.01
     */
    public function __construct(TemplateManager $templateManager, SectionManager $sectionManager)
    {
        parent::__construct($templateManager);
        $this->sectionManager = $sectionManager;
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
        $sections = $this->getRoot()->getChildren();
        $items = array();
        foreach ($sections as $section)
        {
            $items []= new MenuItem($section->getCaption(), '#');
        }
        return $items;
    }

    /**
     * Возвращает менеджер разделов
     *
     * @return SectionManager
     *
     * @since 3.10
     */
    protected function getSectionManager()
    {
        return $this->sectionManager;
    }

    /**
     * Возвращает корневой раздел
     *
     * @return Section|null
     *
     * @since 3.10
     */
    protected function getRoot()
    {
        if (is_null($this->root))
        {
            $this->root = $this->sectionManager->getRootSection();
        }
        return $this->root;
    }
}

