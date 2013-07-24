<?php
/**
 * Контроллер АИ типа раздела «URL»
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
 * Контроллер АИ типа раздела «URL»
 *
 * @package Eresus
 */
class Eresus_CMS_Controller_Client_UrlContent
    implements Eresus_CMS_Controller_Client_ContentInterface
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
     * @return Eresus_HTTP_Response
     * @since 3.01
     */
    public function getHtml()
    {
        $tmpl = new Eresus_Template();
        $tmpl->setSource($this->page->content);
        $html = $tmpl->compile();
        $response = new Eresus_HTTP_Redirect($html);
        return $response;
    }
}

