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

use Eresus\Templating\TemplateManager;
use Eresus\UI\Control\UrlBuilder\UrlBuilderInterface;
use Eresus\UI\Widget;

/**
 * Абстрактный элемент управления
 *
 * Элемент управления — это виджет, при активации которого пользователем, должно быть выполнено
 * определённое действие. Активация, как правило, производится щелчком мыши по элементу, либо
 * иным аналогичным способом.
 *
 * @api
 * @since 3.01
 */
class Control extends Widget
{
    /**
     * Стиль «ссылка»
     */
    const STYLE_LINK = 'link';

    /**
     * Стиль «значок»
     */
    const STYLE_ICON = 'icon';

    /**
     * Стиль «кнопка»
     */
    const STYLE_BUTTON = 'button';

    /**
     * Построитель адресов для ЭУ
     * @var UrlBuilderInterface
     * @since 3.01
     */
    protected $urlBuilder = null;

    /**
     * URL действия
     *
     * @var null|string
     *
     * @since 3.01
     */
    private $actionUrl = null;

    /**
     * Кэш для {@link getActionName()}
     * @var null|string
     * @since 3.01
     */
    private $actionName = null;

    /**
     * Стиль ЭУ
     *
     * @var string
     *
     * @since 3.01
     */
    private $style = self::STYLE_LINK;

    /**
     * Кэш для {@link getAltText()}
     * @var null|string
     * @since 3.01
     */
    private $label = null;

    /**
     * Конструктор виджета
     *
     * @param TemplateManager $templateManager
     *
     * @since 3.01
     */
    public function __construct(TemplateManager $templateManager)
    {
        parent::__construct($templateManager);
        $this->setTemplateName('UI/Control/Control.html');
    }

    /**
     * Задаёт построитель адресов по умолчанию для элементов управления, использующихся в таблице
     *
     * @param UrlBuilderInterface $urlBuilder
     *
     * @since 3.01
     */
    public function setUrlBuilder(UrlBuilderInterface $urlBuilder)
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
     * Задаёт стиль ЭУ
     *
     * @param string $style
     *
     * @return Control
     *
     * @since 3.01
     */
    public function setStyle($style)
    {
        assert('is_string($style)');
        $this->style = $style;
        return $this;
    }

    /**
     * Возвращает стиль ЭУ
     *
     * @return string
     *
     * @since 3.01
     */
    public function getStyle()
    {
        return $this->style;
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
     * @return string
     *
     * @since 3.01
     */
    public function getActionUrl()
    {
        if (!is_null($this->actionUrl))
        {
            return $this->actionUrl;
        }
        if (!is_null($this->urlBuilder))
        {
            return $this->urlBuilder->getActionUrl($this->getActionName());
        }
        return $this->getActionName();
    }

    /**
     * Задаёт URL действия
     *
     * @param string $url
     *
     * @return Control
     *
     * @since 3.01
     */
    public function setActionUrl($url)
    {
        $this->actionUrl = $url;
        return $this;
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
     * Возвращает подпись
     *
     * @return string
     *
     * @since 3.01
     */
    public function getLabel()
    {
        if (is_null($this->label))
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
            $this->label = $class;
        }
        return $this->label;
    }

    /**
     * Задаёт подпись ЭУ
     *
     * @param string $label
     *
     * @return Control
     *
     * @since 3.01
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
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
     * Возвращает обработчик активации ЭУ на стороне клиента
     *
     * @return string|null
     *
     * @since 3.01
     */
    public function getClientHandler()
    {
        return null;
    }
}

