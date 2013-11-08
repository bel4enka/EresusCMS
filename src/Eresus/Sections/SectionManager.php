<?php
/**
 * Менеджер разделов сайта
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

use Doctrine\ORM\EntityManager;
use Eresus\Entity\RootSection;
use Eresus\Entity\Section;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Менеджер разделов сайта
 *
 * @api
 * @since 3.01
 */
class SectionManager
{
    /**
     * @var ContainerInterface
     *
     * @since 3.01
     */
    private $container;

    /**
     * Корневой псевдо-раздел
     *
     * @var null|RootSection
     *
     * @since 3.01
     */
    private $root = null;

    /**
     * @param ContainerInterface $container
     *
     * @since 3.01
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Возвращает раздел по идентификатору
     *
     * @param int|null $id
     *
     * @return Section|null
     *
     * @since 3.01
     */
    public function get($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Добавляет раздел
     *
     * @param Section $section
     *
     * @since 3.01
     */
    public function add(Section $section)
    {
        // TODO проверки
        $this->getObjectManager()->persist($section);
    }

    /**
     * Удаляет раздел
     *
     * @param Section $section
     *
     * @since 3.01
     */
    public function remove(Section $section)
    {
        /* TODO
        /** @var \Doctrine\ORM\EntityManager $om * /
        $om = $this->container->get('doctrine')->getManager();

        $section = $om->find('Eresus\Entity\Section', $id);
        if (is_null($section))
        {
            // TODO
        }

        /** @var \Eresus\Plugins\PluginManager $plugins * /
        $plugins = $this->container->get('plugins');
        $plugin = $plugins->get($section->getType());
        if (!is_null($plugin))
        {
            // TODO Удаление контента $plugin->onSectionDelete($id);
        }

        foreach ($section->getChildren() as $child)
        {
            $this->deleteBranch($child->getId());
        }
        $om->remove($section);
         */
        $this->getObjectManager()->remove($section);
    }

    /**
     * Передвигает раздел ближе к концу списка разделов
     *
     * @param Section $section
     *
     * @since 3.01
     */
    public function moveFarther(Section $section)
    {
        // TODO
    }

    /**
     * Передвигает раздел ближе к началу списка разделов
     *
     * @param Section $section
     *
     * @since 3.01
     */
    public function moveCloser(Section $section)
    {
        // TODO
    }

    /**
     * Перемещает раздел в другой раздел
     *
     * @param Section $section
     * @param Section $newParent
     *
     * @since 3.01
     */
    public function move(Section $section, Section $newParent)
    {
        // TODO
    }

    /**
     * Возвращает корневой псевдо-раздел
     *
     * @return RootSection
     *
     * @since 3.01
     */
    public function getRootSection()
    {
        if (is_null($this->root))
        {
            $this->root = new RootSection($this->getRepository());
        }
        return $this->root;
    }

    /**
     * Возвращает менеджер объектов
     *
     * @return \Doctrine\ORM\EntityManager
     *
     * @since 3.01
     */
    private function getObjectManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    /**
     * Возвращает хранилище записей
     *
     * @return \Doctrine\ORM\EntityRepository
     *
     * @since 3.01
     */
    private function getRepository()
    {
        return $this->getObjectManager()->getRepository('Eresus\Entity\Section');
    }
}

