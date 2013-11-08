<?php
/**
 * Мета-тег
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
 * Мета-тег
 *
 * @since 3.01
 */
class Meta extends Widget
{
    /**
     * Имя
     *
     * @var string|null
     *
     * @since 3.01
     */
    protected $name = null;

    /**
     * Заголовок HTTP
     *
     * @var string|null
     *
     * @since 3.01
     */
    protected $header = null;

    /**
     * Содержимое
     *
     * @var string|null
     *
     * @since 3.01
     */
    protected $content = null;

    /**
     * Возвращает имя
     *
     * @return string|null
     *
     * @since 3.01
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Задаёт имя
     *
     * При этом любое значение, заданное при помощи {@link setHeader()} будет удалено.
     *
     * @param string $name
     *
     * @return Meta
     *
     * @since 3.01
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->header = null;
        return $this;
    }

    /**
     * Возвращает имя заголовка HTTP
     *
     * @return string|null
     *
     * @since 3.01
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Задаёт имя заголовка HTTP
     *
     * При этом любое значение, заданное при помощи {@link setName()} будет удалено.
     *
     * @param string $header
     *
     * @return Meta
     *
     * @since 3.01
     */
    public function setHeader($header)
    {
        $this->header = $header;
        $this->name = null;
        return $this;
    }

    /**
     * Возвращает содержимое
     *
     * @return string|null
     *
     * @since 3.01
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Задаёт содержимое
     *
     * @param string $content
     *
     * @return Meta
     *
     * @since 3.01
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
}

