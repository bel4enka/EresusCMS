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

use Eresus\Exceptions\InvalidArgumentTypeException;
use Eresus\UI\Control\AbstractControl;
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
     * @var AbstractControl[]
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
     * Имя метода получения идентификатора
     * @var null|string
     * @since 3.01
     */
    private $getter = null;

    /**
     * Имя ключа идентификатора
     * @var null|string
     * @since 3.01
     */
    private $key = null;

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
     * @param AbstractControl $control
     *
     * @since 3.01
     */
    public function add(AbstractControl $control)
    {
        $this->propagateUrlBuilder($control);
        $this->controls []= $control;
    }

    /**
     * Задаёт имя метода получения идентификатора объекта
     *
     * @param string $methodName
     *
     * @return ControlsColumn
     *
     * @since 3.01
     */
    public function setGetter($methodName)
    {
        $this->getter = $methodName;
        return $this;
    }

    /**
     * Задаёт имя ключа идентификатора объекта
     *
     * @param string $key
     *
     * @return ControlsColumn
     *
     * @since 3.01
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
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
     * @param object|array $row
     *
     * @throws InvalidArgumentTypeException
     * @throws \LogicException
     *
     * @return string
     *
     * @since 3.01
     */
    public function getData($row)
    {
        if (!(is_object($row) || is_array($row)))
        {
            throw InvalidArgumentTypeException::factory(__METHOD__, 1, 'object or array', $row);
        }

        if (is_object($row))
        {
            if (is_null($this->getter))
            {
                throw new \LogicException('Getter for control column not defined');
            }
            $id = $row->{$this->getter}();
        }
        else
        {
            if (is_null($this->key))
            {
                throw new \LogicException('Key for controls column not defined');
            }
            $id = $row[$this->key];
        }

        $html = '';
        foreach ($this->controls as $control)
        {
            $html .= $control->getHtml($id);
        }
        return $html;
    }

    /**
     * @param AbstractControl $control
     */
    private function propagateUrlBuilder(AbstractControl $control)
    {
        if (!is_null($this->urlBuilder))
        {
            $control->setControlUrlBuilder($this->urlBuilder);
        }
    }
}

