<?php
/**
 * Абстрактный фронт-контроллер CMS
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Абстрактный фронт-контроллер CMS
 *
 * @since 3.01
 */
abstract class FrontController extends Controller
{
    /**
     * Обрабатываемый запрос
     *
     * @var Request
     * @since 3.01
     */
    private $request;

    /**
     * Создаваемая страница
     * @var \Eresus_CMS_Page
     * @since 3.01
     */
    private $page;

    /**
     * Конструктор контроллера
     *
     * @param ContainerInterface $container
     * @param Request $request
     *
     * @since 3.01
     */
    public function __construct(ContainerInterface $container, Request $request)
    {
        parent::__construct($container);
        $this->request = $request;
        $this->page = $this->createPage();
    }

    /**
     * Выполняет действия контроллера и возвращает ответ
     *
     * @return Response
     * @since 3.01
     */
    abstract public function dispatch();

    /**
     * Возвращает объект Eresus_CMS_Page
     *
     * @return \Eresus_CMS_Page
     * @since 3.01
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Возвращает текущий запрос
     *
     * @return Request
     * @since 3.01
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * Создаёт объект Eresus_CMS_Page
     *
     * @return \Eresus_CMS_Page
     * @since 3.01
     */
    abstract protected function createPage();
}

