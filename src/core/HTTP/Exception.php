<?php
/**
 * Исключительная ситуация при работе по HTTP
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
 * @subpackage HTTP
 */

/**
 * Исключительная ситуация при работе по HTTP
 *
 * @package Eresus
 * @subpackage HTTP
 *
 * @since 3.01
 */
abstract class Eresus_HTTP_Exception extends RuntimeException
{
    /**
     * Создаёт исключение
     *
     * @param string    $message   текст сообщения
     * @param int       $code      не используется
     * @param Exception $previous  предыдущее исключение
     *
     * @since 3.01
     */
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $this->getStatusCode(), $previous);
    }

    /**
     * Метод должен возвращать код состояния HTTP, соответствующий исключению
     *
     * @return int
     * @since 3.01
     */
    abstract protected function getStatusCode();
}

