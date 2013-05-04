<?php
/**
 * Тип контента
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

/**
 * Тип контента
 *
 * Описывает тип контента и предоставляет доступ к его контроллерам.
 *
 * @since 4.00
 */
class ContentType
{
    /**
     * Контейнер служб
     * @var ContainerInterface
     * @since 4.00
     */
    private $container;

    /**
     * Пространство имён, где содержатся контроллеры этого типа
     *
     * @var string
     * @since 4.00
     */
    private $namespace;

    /**
     * Имя контроллеров этого типа
     *
     * @var string
     * @since 4.00
     */
    private $controller;

    /**
     * Название
     *
     * @var string|string[]
     * @since 4.00
     */
    private $title;

    /**
     * Описание
     *
     * @var string|string[]
     * @since 4.00
     */
    private $description = null;

    /**
     * Контроллер для АИ
     * @var \Symfony\Bundle\FrameworkBundle\Controller\Controller
     * @since 4.00
     */
    private $adminController = null;

    /**
     * Контроллер для КИ
     * @var \Symfony\Bundle\FrameworkBundle\Controller\Controller
     * @since 4.00
     */
    private $clientController = null;

    /**
     * Конструктор
     *
     * @param ContainerInterface $container
     * @param string             $namespace
     * @param string             $controller
     * @param string|string[]    $title
     * @param string|string[]    $description
     * @since 4.00
     */
    public function __construct(ContainerInterface $container, $namespace, $controller, $title,
        $description = null)
    {
        $this->container = $container;
        $this->namespace = $namespace;
        $this->controller = $controller;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * Возвращает уникальный идентификатор типа контента
     *
     * @return string
     * @since 4.00
     */
    public function getId()
    {
        return str_replace('\\', '.', $this->namespace) . '.' . $this->controller;
    }

    /**
     * Возвращает название
     *
     * @return string|string[]
     * @since 4.00
     */
    public function getTitle()
    {
        return is_array($this->title)
            ? $this->title['ru'] // TODO исправить на локаль из настроек
            : $this->title;
    }

    /**
     * Возвращает описание
     *
     * @return string|string[]
     * @since 4.00
     */
    public function getDescription()
    {
        return is_array($this->description)
            ? $this->description['ru'] // TODO исправить на локаль из настроек
            : $this->description;
    }

    /**
     * Возвращает контроллер для АИ или false, если такого контроллера нет
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Controller|bool
     */
    public function getAdminController()
    {
        if (null === $this->adminController)
        {
            $className = $this->createControllerClassName('Admin');
            $this->adminController = new $className;
            $this->adminController->setContainer($this->container);
        }
        return $this->adminController;
    }

    /**
     * Возвращает контроллер для КИ или false, если такого контроллера нет
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Controller|bool
     */
    public function getClientController()
    {
        if (null === $this->clientController)
        {
            $className = $this->createControllerClassName('Client');
            $this->clientController = new $className;
            $this->clientController->setContainer($this->container);
        }
        return $this->clientController;
    }

    /**
     * Создаёт имя класса контроллера для указанного интерфейса
     *
     * @param string $ui  интерфейс: Admin или Client
     *
     * @return string
     *
     * @since 4.00
     */
    private function createControllerClassName($ui)
    {
        return "{$this->namespace}\\Controller\\{$this->controller}Content{$ui}Controller";
    }
}

