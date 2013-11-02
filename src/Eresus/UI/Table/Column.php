<?php
/**
 * Столбец таблицы
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

/**
 * Столбец таблицы
 *
 * @api
 * @since 3.01
 */
class Column
{
    const ALIGN_LEFT = 'left';
    const ALIGN_RIGHT = 'right';
    const ALIGN_CENTER = 'center';

    /**
     * @var string
     * @since 3.01
     */
    private $caption;

    /**
     * Имя метода получения данных для столбца
     * @var null|string
     * @since 3.01
     */
    private $getter = null;

    /**
     * Имя ключа данных для столбца
     * @var null|string
     * @since 3.01
     */
    private $key = null;

    /**
     * Карта замены значений
     *
     * @var null|array
     *
     * @since 3.01
     */
    private $valueMap = null;

    /**
     * Обработчик значений
     * @var null|Callable
     */
    private $callback = null;

    /**
     * Выравнивание
     * @var null|string
     * @since 3.01
     */
    private $align = null;

    /**
     * Конструктор столбца
     *
     * @param string $caption  подпись столбца
     *
     * @since 3.01
     */
    public function __construct($caption = '')
    {
        $this->caption = $caption;
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
        return $this->caption;
    }

    /**
     * Задаёт имя метода получения значения для этого столбца
     *
     * @param string $methodName
     *
     * @return $this
     *
     * @since 3.01
     */
    public function setGetter($methodName)
    {
        $this->getter = $methodName;
        return $this;
    }

    /**
     * Задаёт имя ключа значения для этого столбца
     *
     * @param string $key
     *
     * @return $this
     *
     * @since 3.01
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Задаёт карту замены значений
     *
     * Если значение ячейки совпадёт с одним из ключей массива $map, то {@link getData()} для
     * этой ячейки вернёт значение, соответствующее этому ключу.
     *
     * @param array $map  карта замены значений
     *
     * @return $this
     */
    public function setValueMap(array $map)
    {
        $this->valueMap = $map;
        return $this;
    }

    /**
     * Задаёт функцию-обработчик значений
     *
     * Обработчику будет передано значение ячейки после всех остальных трансформаций. Обработчик
     * должен возвращать значение, которое будет выведено в ячейке.
     *
     * @param callable $callback
     *
     * @return $this
     *
     * @since
     */
    public function setCallback($callback)
    {
        assert('is_callable($callback)');
        $this->callback = $callback;
        return $this;
    }

    /**
     * Задаёт выравнивание содержимого в ячейках столбца
     *
     * @param string $align
     *
     * @return $this
     *
     * @since 3.01
     */
    public function setAlign($align)
    {
        $this->align = $align;
        return $this;
    }

    /**
     * Возвращает выравнивание для ячейки
     *
     * @return null|string
     *
     * @since 3.01
     */
    public function getAlign()
    {
        return $this->align;
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
                throw new \LogicException(sprintf('Getter for column "%s" not defined',
                    $this->caption));
            }
            $data = $row->{$this->getter}();
        }
        else
        {
            if (is_null($this->key))
            {
                throw new \LogicException(sprintf('Key for column "%s" not defined',
                    $this->caption));
            }
            $data = $row[$this->key];
        }

        if (!is_null($this->valueMap) && array_key_exists($data, $this->valueMap))
        {
            $data = $this->valueMap[$data];
        }

        if (!is_null($this->callback))
        {
            $data = call_user_func($this->callback, $data);
        }

        return $data;
    }
}

