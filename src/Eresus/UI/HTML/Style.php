<?php
/**
 * Стили
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
 * Стили
 *
 * @since 3.01
 */
class Style extends Widget
{
    /**
     * Содержимое
     *
     * @var string|null
     *
     * @since 3.01
     */
    protected $contents = null;

    /**
     * Тип носителя
     *
     * @var string|null
     *
     * @since 3.01
     */
    protected $media = null;

    /**
     * Возвращает содержимое
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
     * Задаёт содержимое
     *
     * @param string $css
     *
     * @return Style
     *
     * @since 3.01
     */
    public function setContents($css)
    {
        $this->contents = $css;
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
}

