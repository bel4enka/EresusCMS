<?php
/**
 * Реестр типов контента
 *
 * @version ${product.version}
 * @copyright 2012, Михаил Красильников <m.krasilnikov@yandex.ru>
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

namespace Eresus\CmsBundle\Content;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Реестр типов контента
 *
 * Реестр хранит объекты класса {@link ContentType} зарегистрированных типов контента.
 *
 * @since 4.00
 */
class ContentTypeRegistry implements ContainerAwareInterface
{
    /**
     * Контейнер служб
     * @var ContainerInterface
     * @since 4.00
     */
    private $container;

    /**
     * Список типов контента
     * @var ContentType[]
     * @since 4.00
     */
    private $registry = array();

    /**
     * Конструктор
     *
     * @param ContainerInterface $container
     *
     * @since 4.00
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @since 4.00
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Регистрирует тип контента
     *
     * @param ContentType $type
     *
     * @since 4.00
     */
    public function register(ContentType $type)
    {
        $this->registry[$type->getId()] = $type;
    }

    /**
     * Возвращает список доступных типов контента
     *
     * @return ContentType[]
     * @since 4.00
     */
    public function getAll()
    {
        // Убеждаемся, что модули зарегистрировали свои типы контента.
        $this->container->get('extensions');
        return $this->registry;
    }

    /**
     * Возвращает тип контента по его идентификатору, если такой тип зарегистрирован
     *
     * @param string $id
     *
     * @return ContentType|null
     */
    public function getByID($id)
    {
        assert('is_string($id)');
        // Убеждаемся, что модули зарегистрировали свои типы контента.
        $this->container->get('extensions');
        return array_key_exists($id, $this->registry)
            ? $this->registry[$id]
            : null;
    }
}

