<?php
/**
 * ЭУ «Вкл/выкл»
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

namespace Eresus\UI\Control;
use Eresus\Content\SwitchableElementInterface;

/**
 * ЭУ «Вкл/выкл»
 *
 * @api
 * @since 3.01
 */
class ToggleControl extends ElementControl
{
    /**
     * Возвращает URL значка
     *
     * @return string
     *
     * @since 3.01
     */
    public function getIconUrl()
    {
        return 'item-' . ($this->isEnabled() ? '' : 'in') . 'active.png';
    }

    /**
     * Возвращает альтернативный текст
     *
     * @return string
     *
     * @since 3.01
     */
    public function getAltText()
    {
        return $this->isEnabled() ? '&#9745;' : '&#9744;';
    }

    /**
     * Возвращает текст подсказки
     *
     * @return string
     *
     * @since 3.01
     */
    public function getHint()
    {
        return $this->isEnabled() ? _('Отключить') : _('Включить');
    }

    /**
     * Возвращает true, если объект включен
     *
     * @return bool
     *
     * @since 3.01
     */
    private function isEnabled()
    {
        $e = $this->getElement();
        $enabled = (!($e instanceof SwitchableElementInterface)) || $e->isEnabled();
        return $enabled;
    }

    /**
     * Возвращает разметку
     *
     * @return string  HTML
     *
     * @since 3.01
     */
    public function getHtml()
    {
        if (!($this->getElement() instanceof SwitchableElementInterface))
        {
            return '';
        }
        return parent::getHtml();
    }
}

