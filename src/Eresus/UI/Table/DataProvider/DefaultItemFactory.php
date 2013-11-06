<?php
/**
 * Фабрика по умолчанию, производящая элементы для таблиц
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

namespace Eresus\UI\Table\DataProvider;

use Eresus\Content\ElementInterface;
use Eresus\Exceptions\InvalidArgumentTypeException;

/**
 * Фабрика по умолчанию, производящая элементы для таблиц
 *
 * Это псевдо-фабрика, возвращающая полученные на входе объекты без изменений. Единственное, что
 * она делает — проверяет класс объекта и вбрасывает исключение, если он не поддерживает
 * интерфейс {@link Eresus\Content\ElementInterface}.
 *
 * @since 3.01
 */
class DefaultItemFactory implements ItemFactoryInterface
{
    /**
     * Возвращает элемент для строки таблицы
     *
     * @param mixed $source  исходные данные для строки таблицы
     *
     * @throws InvalidArgumentTypeException
     *
     * @return ElementInterface
     *
     * @since 3.01
     */
    public function create($source)
    {
        if (!($source instanceof ElementInterface))
        {
            throw InvalidArgumentTypeException::factory(__METHOD__, 1,
                'Eresus\Content\ElementInterface', $source);
        }
        return $source;
    }
}

