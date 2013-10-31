<?php
/**
 * Событие «Отправка ответа»
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
 *
 * @package Eresus
 */

namespace Eresus\Events;

use Symfony\Component\EventDispatcher\Event;
use Eresus_HTTP_Response;

/**
 * Событие «Отправка ответа»
 *
 * @since 3.01
 * @package Eresus
 */
class ResponseEvent extends Event
{
    /**
     * Ответ
     *
     * @var Eresus_HTTP_Response
     *
     * @since 3.01
     */
    private $response;

    /**
     * @param Eresus_HTTP_Response $response
     *
     * @since 3.01
     */
    public function __construct(Eresus_HTTP_Response $response)
    {
        $this->response = $response;
    }

    /**
     * Возвращает ответ
     *
     * @return Eresus_HTTP_Response
     *
     * @since 3.01
     */
    public function getResponse()
    {
        return $this->response;
    }
}

