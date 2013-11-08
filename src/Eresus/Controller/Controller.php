<?php
/**
 * Абстрактный контроллер
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

namespace Eresus\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Абстрактный контроллер CMS
 *
 * @api
 * @since 3.01
 */
abstract class Controller
{
    /**
     * Контейнер служб
     *
     * @var ContainerInterface
     * @since 3.01
     */
    protected $container;

    /**
     * Конструктор контроллера
     *
     * @param ContainerInterface $container
     *
     * @since 3.01
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Обрабатывает полученный запрос и возвращает ответ
     *
     * @param Request $request
     *
     * @return string|Response
     *
     * @since 3.01
     */
    abstract public function process(Request $request);

    /**
     * Возвращает службу из контейнера
     *
     * @param string $id
     *
     * @return object
     *
     * @since 3.01
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Отправляет извещение о событии
     *
     * @param string $eventName  имя события
     * @param Event  $event      опциональное описание события
     *
     * @since 3.01
     */
    protected function dispatchEvent($eventName, Event $event = null)
    {
        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */
        $dispatcher = $this->container->get('events');
        $dispatcher->dispatch($eventName, $event);
    }
}

