<?php
/**
 * Скрипт
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

namespace Eresus\UI\HTML;

use Eresus\UI\Widget;

/**
 * Скрипт
 *
 * @since 3.01
 */
class Script extends Widget
{
    /**
     * URL подключаемого скрипта
     *
     * @var string|null
     *
     * @since 3.01
     */
    protected $url = null;

    /**
     * Текст скрипта
     *
     * @var string|null
     *
     * @since 3.01
     */
    protected $contents = null;

    /**
     * Асинхронная загрузка
     *
     * @var bool
     *
     * @since 3.01
     */
    protected $async = false;

    /**
     * Отложенная загрузка
     *
     * @var bool
     *
     * @since 3.01
     */
    protected $defer = false;

    /**
     * Возвращает URL скрипта
     *
     * @return string|null
     *
     * @since 3.01
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Задаёт URL скрипта для подключения
     *
     * При этом любое значение, заданное при помощи {@link setContents()} будет удалено.
     *
     * @param string $url
     *
     * @since 3.01
     */
    public function setUrl($url)
    {
        $this->url = $url;
        $this->contents = null;
    }

    /**
     * Возвращает текст скрипта
     *
     * @return string|null
     *
     * @since 3.01
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Задаёт текст скрипта
     *
     * При этом любое значение, заданное при помощи {@link setUrl()} будет удалено.
     *
     * @param string $code
     *
     * @since 3.01
     */
    public function setContents($code)
    {
        $this->contents = $code;
        $this->url = null;
    }

    /**
     * Возвращает true, скрипт можно загружать асинхронно
     *
     * @return bool
     *
     * @since 3.01
     */
    public function isAsync()
    {
        return $this->async;
    }

    /**
     * Разрешает или запрещает асинхронную загрузку скрипта
     *
     * @param bool $state
     *
     * @since 3.01
     */
    public function setAsync($state)
    {
        $this->async = $state;
    }

    /**
     * Возвращает true, скрипт загрузку скрипта можно отложить
     *
     * @return bool
     *
     * @since 3.01
     */
    public function isDefer()
    {
        return $this->defer;
    }

    /**
     * Разрешает или запрещает отложенную загрузку скрипта
     *
     * @param bool $state
     *
     * @since 3.01
     */
    public function setDefer($state)
    {
        $this->defer = $state;
    }
}

