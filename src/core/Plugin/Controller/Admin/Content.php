<?php
/**
 * Контроллер контента АИ
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
 * Контроллер контента АИ
 *
 * @package Eresus
 * @since 3.01
 */
abstract class Eresus_Plugin_Controller_Admin_Content extends Eresus_Plugin_Controller_Admin
    implements Eresus_CMS_Controller_Admin_ContentInterface
{
    /**
     * Возвращает разметку области контента
     *
     * Метод вызывает{@link getAction()} чтобы определить запрошенное пользователем действие. К
     * полученному результату добавляется префикс «action», затем в классе ищется метод с
     * получившимся названием. Если такого метода нет, вбрасывается исключение. Если метод есть,
     * он вызывается, а его результат возвращается.
     *
     * @throws Eresus_CMS_Exception_NotFound
     *
     * @return string
     * @since 3.01
     */
    public function getHtml()
    {
        $action = 'action' . $this->getAction();
        if (!method_exists($this, $action))
        {
            throw new Eresus_CMS_Exception_NotFound;
        }

        return $this->{$action}();
    }

    /**
     * Возвращает запрошенное пользователем действие
     *
     * Действие определяется на основе аргумента «action» из запроса HTTP. Если аргумент не указан,
     * возвращается действие «index».
     *
     * @return string
     * @since 3.01
     */
    protected function getAction()
    {
        return arg('action') ?: 'index';
    }
}

