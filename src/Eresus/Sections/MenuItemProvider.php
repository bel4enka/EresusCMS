<?php
/**
 * Поставщик пунктов меню на основе разделов сайта
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

namespace Eresus\Sections;

use Eresus\Entity\Section;
use Eresus\UI\Menu\ItemProviderInterface;
use Eresus\UI\Menu\Menu;
use Eresus\UI\Menu\MenuItem;

/**
 * Поставщик пунктов меню на основе разделов сайта
 *
 * @api
 * @since 3.01
 */
class MenuItemProvider implements ItemProviderInterface
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
     * Имя шаблона
     *
     * @var null|string
     *
     * @since 3.01
     */
    private $templateName = null;

    /**
     * Конструктор
     *
     * @param SectionManager  $sectionManager
     * @param Section         $root
     *
     * @since 3.01
     */
    public function __construct(SectionManager $sectionManager, Section $root = null)
    {
        $this->sectionManager = $sectionManager;
        $this->root = $root;
    }

    /**
     * Возвращает пункты меню
     *
     * @param Menu    $target
     * @param Section $root
     *
     * @since 3.01
     */
    public function populate(Menu $target, Section $root = null)
    {
        $sections = is_null($root)
            ? $this->getRoot()->getChildren()
            : $root->getChildren();
        foreach ($sections as $section)
        {
            $item = $this->createMenuItem($section);
            if ($section->getChildren()->count() > 0)
            {
                $subMenu = $target->createSubMenu();
                $this->populate($subMenu, $section);
                $item->setSubMenu($subMenu);
            }
            $target->add($item);
        }
    }

    /**
     * Задаёт шаблон для пунктов меню
     *
     * @param string $filename
     *
     * @since 3.01
     */
    public function setTemplateName($filename)
    {
        $this->templateName = $filename;
    }

    /**
     * Возвращает менеджер разделов
     *
     * @return SectionManager
     *
     * @since 3.10
     */
    private function getSectionManager()
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
    private function getRoot()
    {
        if (is_null($this->root))
        {
            $this->root = $this->getSectionManager()->getRootSection();
        }
        return $this->root;
    }

    /**
     * Создаёт пункт меню
     *
     * @param Section $section
     *
     * @return MenuItem
     *
     * @since 3.01
     */
    private function createMenuItem(Section $section)
    {
        $item = new MenuItem($section->getCaption(),
            'admin.php?mod=content&id=' . $section->getId());
        if (!is_null($this->templateName))
        {
            $item->setTemplateName($this->templateName);
        }
        return $item;
    }
}

