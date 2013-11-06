<?php
/**
 * Столбец таблицы с элементами управления
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

namespace Eresus\UI\Table;

use Eresus\Content\ElementInterface;
use Eresus\Exceptions\InvalidArgumentTypeException;
use Eresus\UI\Control\ElementControl;
use Eresus\UI\Control\UrlBuilder\UrlBuilderAwareInterface;
use Eresus\UI\Control\UrlBuilder\UrlBuilderInterface;

/**
 * Столбец таблицы с элементами управления
 *
 * @api
 * @since 3.01
 */
class ControlsColumn extends AbstractColumn implements UrlBuilderAwareInterface
{
    /**
     * Элементы управления
     *
     * @var ElementControl[]
     * @since 3.01
     */
    protected $controls = array();

    /**
     * Построитель адресов для ЭУ
     * @var UrlBuilderInterface
     * @since 3.01
     */
    protected $urlBuilder = null;

    /**
     * Создаёт и возвращает новый столбец
     *
     * По сути, этот метод — синтаксический сахар, позволяющий удобнее использовать текучий (fluid)
     * интерфейс:
     *
     * <code>
     * $list->addColumn(ControlsColumn::create(…)->…);
     * </code>
     *
     * @param ... элементы управления (см. {@link add()})
     *
     * @return ControlsColumn
     *
     * @since 3.01
     */
    public static function create()
    {
        $column = new self();
        $controls = func_get_args();
        foreach ($controls as $control)
        {
            $column->add($control);
        }
        return $column;
    }

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
        foreach ($this->controls as $control)
        {
            $this->propagateUrlBuilder($control);
        }
    }

    /**
     * Добавляет элемент управления
     *
     * @param ElementControl $control
     *
     * @since 3.01
     */
    public function add(ElementControl $control)
    {
        $this->propagateUrlBuilder($control);
        $this->controls []= $control;
    }

    /**
     * Возвращает подпись столбца
     *
     * @return string
     *
     * @since 3.01
     */
    public function getCaption()
    {
        return '';
    }

    /**
     * Возвращает тип ячейки
     *
     * Тип ячейки — это произвольная комбинация символов, которая будет добавлена как суффикс
     * класса CSS "table__cell_type_…" к тегу td.
     *
     * @return null|string
     *
     * @since 3.01
     */
    public function getType()
    {
        return 'controls';
    }

    /**
     * Возвращает данные этого столбца из переданной строки
     *
     * @param ElementInterface $element
     *
     * @throws InvalidArgumentTypeException
     *
     * @return string
     *
     * @since 3.01
     */
    public function getData($element)
    {
        if (!($element instanceof ElementInterface))
        {
            throw InvalidArgumentTypeException::factory(__METHOD__, 1,
                'Eresus\Content\ElementInterface', $element);
        }
        $html = array();
        foreach ($this->controls as $control)
        {
            $control->setElement($element);
            $html []= $control->getHtml();
        }
        $html = implode(' ', $html);
        return $html;
    }

    /**
     * @param ElementControl $control
     */
    private function propagateUrlBuilder(ElementControl $control)
    {
        if (!is_null($this->urlBuilder))
        {
            $control->setControlUrlBuilder($this->urlBuilder);
        }
    }
}

