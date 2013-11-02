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
     * @since 3.01
     */
    public function setGetter($methodName)
    {
        $this->getter = $methodName;
    }

    /**
     * Задаёт имя ключа значения для этого столбца
     *
     * @param string $key
     *
     * @since 3.01
     */
    public function setKey($key)
    {
        $this->key = $key;
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
        return $data;
    }
}

