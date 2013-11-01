<?php
/**
 * Исключительная ситуация «Запрошенный адрес не найден»
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

namespace Eresus\Exceptions;

use Eresus_HTTP_Exception;
use Eresus_HTTP_Exception_NotFound;

/**
 * Исключительная ситуация «Запрошенный адрес не найден»
 *
 * @api
 * @since 3.01
 */
class NotFoundException extends UserLevelException
{
    /**
     * Создаёт исключение HTTP соответствующее этому исключению
     *
     * @return Eresus_HTTP_Exception
     *
     * @since 3.01
     */
    protected function createHttpException()
    {
        return new Eresus_HTTP_Exception_NotFound($this->getMessage());
    }
}

