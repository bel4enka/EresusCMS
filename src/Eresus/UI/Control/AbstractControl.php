<?php
/**
 * Абстрактный элемент управления
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

use Eresus\UI\Control\UrlBuilder\UrlBuilderInterface;
use Eresus\UI\Widget;

/**
 * Абстрактный элемент управления
 *
 * @api
 * @since 3.01
 */
abstract class AbstractControl extends Widget
{
    /**
     * Построитель адресов для ЭУ
     * @var UrlBuilderInterface
     * @since 3.01
     */
    protected $urlBuilder = null;

    /**
     * Кэш для {@link getActionName()}
     * @var null|string
     * @since 3.01
     */
    private $actionName = null;

    /**
     * Кэш для {@link getAltText()}
     * @var null|string
     * @since 3.01
     */
    private $altText = null;

    /**
     * Свойство для временного хранения идентификатора методом getHtml
     *
     * @var null|int|string
     *
     * @since 3.01
     */
    private $tmpId = null;

    /**
     * Задаёт построитель адресов по умолчанию для элементов управления, использующихся в таблице
     *
     * @param UrlBuilderInterface $urlBuilder
     *
     * @since 3.01
     */
    public function setControlUrlBuilder(UrlBuilderInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Возвращает имя действия
     *
     * Имя действия — строка, идентифицирующая это действие в запросах
     *
     * @return string
     *
     * @since 3.01
     */
    public function getActionName()
    {
        if (is_null($this->actionName))
        {
            $class = get_class($this);
            $pos = strrpos($class, '\\');
            if (false !== $pos)
            {
                $class = substr($class, $pos + 1);
            }
            $word = 'Control';
            $pos = -1 * strlen($word);
            if (substr($class, $pos) == $word)
            {
                $class = substr($class, 0, $pos);
            }
            $this->actionName = strtolower($class);
        }
        return $this->actionName;
    }

    /**
     * Возвращает тип ЭУ
     *
     * @return null|string
     *
     * @since 3.01
     */
    public function getType()
    {
        return $this->getActionName();
    }

    /**
     * Возвращает URL действия
     *
     * @param int|null $id  идентификатор объекта воздействия
     *
     * @return string
     *
     * @since 3.01
     */
    public function getActionUrl($id = null)
    {
        $id = $id ?: $this->tmpId;
        if (is_null($this->urlBuilder))
        {
            return '#?action=' . $this->getActionName() . ($id ? '&id=' . $id : '');
        }

        return $this->urlBuilder->getUrlFor($this->getActionName(), $id);
    }

    /**
     * Возвращает URL значка относительно корневого URL темы оформления
     *
     * @return string|null
     *
     * @since 3.01
     */
    public function getIconUrl()
    {
        return null;
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
        if (is_null($this->altText))
        {
            $class = get_class($this);
            $pos = strrpos($class, '\\');
            if (false !== $pos)
            {
                $class = substr($class, $pos + 1);
            }
            $word = 'Control';
            $pos = -1 * strlen($word);
            if (substr($class, $pos) == $word)
            {
                $class = substr($class, 0, $pos);
            }
            $this->altText = $class;
        }
        return $this->altText;
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
        return '';
    }

    /**
     * Возвращает разметку
     *
     * @param null|int|string $id  идентификатор объекта воздействия
     *
     * @return string  HTML
     *
     * @since 3.01
     */
    public function getHtml($id = null)
    {
        $this->tmpId = $id;
        $html = parent::getHtml();
        $this->tmpId = null;
        return $html;
    }

    /**
     * Возвращает имя файла шаблона
     *
     * @return string
     *
     * @since 3.01
     */
    protected function getTemplateName()
    {
        return 'UI/Control/Control.html';
    }
}

