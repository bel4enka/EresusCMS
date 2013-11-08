<?php
/**
 * Связь с другим ресурсом
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
 * Связь с другим ресурсом
 *
 * @since 3.01
 */
class Link extends Widget
{
    /**
     * URL
     *
     * @var string|null
     *
     * @since 3.01
     */
    protected $href = null;

    /**
     * Тип носителя
     *
     * @var string|null
     *
     * @since 3.01
     */
    protected $media = null;

    /**
     * Тип данных
     *
     * @var string|null
     *
     * @since 3.01
     */
    protected $type = null;

    /**
     * Типы связи
     *
     * @var string[]
     *
     * @since 3.01
     */
    protected $rel = array();

    /**
     * Возвращает URL
     *
     * @return string|null
     *
     * @since 3.01
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Задаёт URL
     *
     * @param string $url
     *
     * @return Link
     *
     * @since 3.01
     */
    public function setHref($url)
    {
        $this->href = $url;
        return $this;
    }

    /**
     * Возвращает тип носителя
     *
     * @return string|null
     *
     * @since 3.01
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Задаёт тип носителя
     *
     * @param string $media
     *
     * @return Link
     *
     * @since 3.01
     */
    public function setMedia($media)
    {
        $this->media = $media;
        return $this;
    }

    /**
     * Возвращает тип данных
     *
     * @return string|null
     *
     * @since 3.01
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Задаёт тип данных
     *
     * @param string $type
     *
     * @return Link
     *
     * @since 3.01
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Возвращает типы связи
     *
     * @return string[]
     *
     * @since 3.01
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * Задаёт типы связи
     *
     * @param string ... типы связей (stylesheet, alternate…)
     *
     * @return Link
     *
     * @since 3.01
     */
    public function setRel()
    {
        $this->rel = func_get_args();
        return $this;
    }
}

