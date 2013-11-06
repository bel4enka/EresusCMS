<?php
/**
 * Переключатель страниц
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

namespace Eresus\UI;

use Eresus\Templating\TemplateManager;
use Eresus\UI\Control\UrlBuilder\UrlBuilderAwareInterface;
use Eresus\UI\Control\UrlBuilder\UrlBuilderInterface;
use Iterator;
use Countable;

/**
 * Переключатель страниц
 *
 * @api
 * @since 3.01
 */
class Pagination extends Widget implements Iterator, Countable, UrlBuilderAwareInterface
{
    /**
     * Построитель адресов для ЭУ
     *
     * @var UrlBuilderInterface
     *
     * @since 3.01
     */
    protected $urlBuilder = null;

    /**
     * Общее количество страниц
     *
     * @var int
     * @since 3.01
     */
    private $total;

    /**
     * Номер текущей страницы
     *
     * @var int
     * @since 3.01
     */
    private $current;

    /**
     * Размер переключателя в количестве выводимых страниц
     *
     * @var int
     * @since 3.01
     */
    private $size = 10;

    /**
     * Номер итерации
     *
     * Часть реализации интерфейса Iterator
     *
     * @var int
     * @since 3.01
     */
    private $iteration = 0;

    /**
     * Элементы нумерации
     *
     * @var array
     * @since 3.01
     */
    private $items = array();

    /**
     * Конструктор виджета
     *
     * Принимаемые параметры (кроме $templateManager) можно указать и позднее, при помощи
     * соответствующих методов setXXX.
     *
     * @param TemplateManager     $templateManager
     * @param int                 $total            общее количество страниц
     * @param int                 $current          номер текущей страницы. По умолчанию 1.
     * @param UrlBuilderInterface $urlBuilder       построитель адресов
     *
     * @since 3.01
     */
    public function __construct(TemplateManager $templateManager, $total = null, $current = 1,
                                UrlBuilderInterface $urlBuilder = null)
    {
        parent::__construct($templateManager);
        $this->setTotal($total);
        $this->setCurrent($current);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Устанавливает общее количество страниц
     *
     * @param int $value
     *
     * @return Pagination
     *
     * @since 3.01
     */
    public function setTotal($value)
    {
        $this->total = $value;
        return $this;
    }

    /**
     * Возвращает общее количество страниц
     *
     * @return int
     *
     * @since 3.01
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Устанавливает номер текущей страницы
     *
     * @param int $value
     *
     * @return Pagination
     *
     * @since 3.01
     */
    public function setCurrent($value)
    {
        $this->current = $value;
        return $this;
    }

    /**
     * Возвращает номер текущей страницы
     *
     * @return int
     *
     * @since 3.01
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Задаёт построитель адресов
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
     * Устанавливает максимальное количество отображаемых страниц
     *
     * @param int $value
     *
     * @return Pagination
     *
     * @since 3.01
     */
    public function setSize($value)
    {
        $this->size = $value;
        return $this;
    }

    /**
     * Возвращает максимальное количество отображаемых страниц
     *
     * @return int
     *
     * @since 3.01
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Возвращает текущий элемент списка страниц
     *
     * @return array
     *
     * @since 3.01
     * @see Iterator::current()
     * @internal
     */
    public function current()
    {
        return $this->items[$this->iteration - 1];
    }

    /**
     * Возвращает номер итерации
     *
     * @return int
     *
     * @since 3.01
     * @see Iterator::key()
     * @internal
     */
    public function key()
    {
        return $this->iteration;
    }

    /**
     * Перемещает внутренний указатель на следующий элемент
     *
     * @return void
     *
     * @since 3.01
     * @see Iterator::next()
     * @internal
     */
    public function next()
    {
        $this->iteration++;
    }

    /**
     * Подготавливает объект к первой итерации
     *
     * @return void
     *
     * @since 3.01
     * @see Iterator::rewind()
     * @internal
     */
    public function rewind()
    {
        $this->items = array();

        /*
         * Если страниц больше чем задано показывать за один раз, то будем показывать только часть
         * страниц, наиболее близких к текущей.
         */
        if ($this->total > $this->size)
        {
            // Начинаем показ с текущей, минус половину видимых
            $first = (int) floor($this->current - $this->size / 2);
            if ($first < 1)
            {
                // Страниц меньше 1-й не существует
                $first = 1;
            }

            $last = $first + $this->size - 1;
            if ($last > $this->total)
            {
                // Но если это больше чем страниц всего, вносим исправления
                $last = $this->total;
                $first = $last - $this->size + 1;
            }
        }
        else
        {
            $first = 1;
            $last = $this->total;
        }


        if ($first > 1)
        {
            $pageNumber = $this->current - $this->size;
            if ($pageNumber < 1)
            {
                $pageNumber = 1;
            }
            $this->items []= $this->itemFactory($pageNumber, '&larr;');
        }

        for ($pageNumber = $first; $pageNumber <= $last; $pageNumber++)
        {
            $item = $this->itemFactory($pageNumber);
            $item['current'] = $pageNumber == $this->current;
            $this->items []= $item;
        }

        if ($last < $this->total)
        {
            $pageNumber = $this->current + $this->size;
            if ($pageNumber > $this->total)
            {
                $pageNumber = $this->total;
            }
            $this->items []= $this->itemFactory($pageNumber, '&rarr;');
        }

        $this->iteration = 1;
    }

    /**
     * Возвращает true, если внутренний указатель не вышел за пределы списка элементов
     *
     * @return bool
     *
     * @since 3.01
     * @see Iterator::valid()
     * @internal
     */
    public function valid()
    {
        return $this->iteration - 1 < count($this->items);
    }

    /**
     * Возвращает количество элементов переключателя
     *
     * @return int
     *
     * @since 3.01
     * @see Countable::count()
     * @internal
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Создаёт элемент нумерации
     *
     * @param int    $pageNumber  номер страницы
     * @param string $label       метка страницы
     *
     * @throws \LogicException
     *
     * @return array
     *
     * @since 3.01
     */
    private function itemFactory($pageNumber, $label = null)
    {
        if (is_null($this->urlBuilder))
        {
            throw new \LogicException(sprintf('URL builder not set for %s', get_class($this)));
        }
        return array(
            'url' => $this->urlBuilder->getUrl(array('page' => $pageNumber)),
            'label' => $label ? $label : $pageNumber,
            'current' => false
        );
    }
}

