<?php
/**
 * Абстрактный контроллер контента КИ
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

/**
 * Абстрактный контроллер контента КИ
 *
 * @package Eresus
 */
abstract class Eresus_Client_Controller_Content_Abstract
    implements Eresus_Client_Controller_Content_Interface
{
    /**
     * Обрабатываемая страница
     * @var TClientUI
     * @since 3.01
     */
    private $page;

    /**
     * Задаёт обрабатываемую в данный момент страницу
     *
     * @param TClientUI $page
     *
     * @return void
     *
     * @since 3.01
     */
    public function setPage(TClientUI $page)
    {
        $this->page = $page;
    }

    /**
     * Возвращает разметку области контента
     *
     * @return Eresus_HTTP_Response|string
     * @since 3.01
     */
    abstract public function getHtml();

    /**
     * Возвращает создаваемую страницу
     *
     * @return TClientUI
     * @since 3.01
     */
    protected function getPage()
    {
        return $this->page;
    }
}

