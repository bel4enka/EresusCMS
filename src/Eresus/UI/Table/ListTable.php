<?php
/**
 * Таблица-список
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

use Eresus\UI\Pagination;
use Eresus\UI\Widget;
use Eresus\UI\Control\UrlBuilder\UrlBuilderAwareInterface;
use Eresus\UI\Table\DataProvider\DataProviderInterface;
use Eresus\UI\Control\UrlBuilder\UrlBuilderInterface;
use Eresus\Templating\TemplateManager;

/**
 * Таблица-список
 *
 * Список объектов в виде таблицы
 *
 * @api
 * @since 3.01
 */
class ListTable extends Widget implements UrlBuilderAwareInterface
{
    /**
     * Поставщик данных
     * @var DataProviderInterface
     * @since 3.01
     */
    protected $provider;

    /**
     * Размер страницы (кол-во элементов)
     *
     * @var null|int
     *
     * @since 3.01
     */
    protected $pageSize = null;

    /**
     * Построитель адресов для ЭУ
     * @var UrlBuilderInterface
     * @since 3.01
     */
    protected $urlBuilder = null;

    /**
     * Столбцы таблицы
     *
     * @var null|AbstractColumn[]
     *
     * @since 3.01
     */
    private $columns = array();

    /**
     * Строки данных
     *
     * @var null|object[]
     *
     * @since 3.01
     */
    private $data = null;

    /**
     * Переключатель страниц
     *
     * @var null|Pagination
     *
     * @since 3.01
     */
    private $pagination = null;

    /**
     * @param TemplateManager       $templateManager
     * @param DataProviderInterface $dataProvider
     *
     * @since 3.01
     */
    public function __construct(TemplateManager $templateManager,
        DataProviderInterface $dataProvider)
    {
        parent::__construct($templateManager);
        $this->provider = $dataProvider;
    }

    /**
     * Задаёт размер страницы (сколько элементов выводить)
     *
     * @param int|null $size
     *
     * @return ListTable
     *
     * @since 3.01
     */
    public function setPageSize($size)
    {
        $this->pageSize = $size;
        if (is_null($size))
        {
            $this->pagination = null;
        }
        else
        {
            $this->getPagination()->setSize($size);
        }
        return $this;
    }

    /**
     * Задаёт номер текущей страницы
     *
     * Номер страницы можно задать только ПОСЛЕ вызова {@link setPageSize()}. Нумерация страниц
     * начинается с нуля.
     *
     * @param int $number
     *
     * @return ListTable
     *
     * @since 3.01
     */
    public function setCurrentPage($number)
    {
        if (!is_null($this->pagination))
        {
            $this->getPagination()->setCurrent($number);
        }
        return $this;
    }

    /**
     * Задаёт построитель адресов по умолчанию для элементов управления, использующихся в таблице
     *
     * @param UrlBuilderInterface $urlBuilder
     *
     * @return ListTable
     *
     * @since 3.01
     */
    public function setControlUrlBuilder(UrlBuilderInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
        foreach ($this->columns as $column)
        {
            $this->propagateUrlBuilder($column);
        }
        return $this;
    }

    /**
     * Добавляет столбец к таблице
     *
     * @param AbstractColumn $column
     *
     * @since 3.01
     */
    public function addColumn(AbstractColumn $column)
    {
        $this->propagateUrlBuilder($column);
        $this->columns []= $column;
    }

    /**
     * Возвращает true, если у таблицы есть шапка
     *
     * @return bool
     *
     * @since 3.01
     */
    public function hasHead()
    {
        return true;
    }

    /**
     * Возвращает описания столбцов таблицы
     *
     * @return AbstractColumn[]
     *
     * @since 3.01
     */
    public function getColumns()
    {
        if (is_null($this->columns))
        {
            $this->columns = $this->provider->getColumns();
        }
        return $this->columns;
    }

    /**
     * Возвращает строки данных
     *
     * @return array
     *
     * @since 3.01
     */
    public function getDataRows()
    {
        if (is_null($this->data))
        {
            // Вычисляем номер страницы, убеждаемся, что он всегда будет больше нуля
            $page = $this->getPagination()
                ? ($this->getPagination()->getCurrent() > 0
                    ? $this->getPagination()->getCurrent()
                    : 1)
                : 1;
            $this->data = $this->provider->getItems($this->pageSize, ($page - 1) * $this->pageSize);
        }
        return $this->data;
    }

    /**
     * Возвращает переключатель страниц
     *
     * @return Pagination|null
     *
     * @since 3.01
     */
    public function getPagination()
    {
        if (is_null($this->pagination) && !is_null($this->pageSize))
        {
            $this->pagination = new Pagination($this->getTemplateManager(),
                ceil($this->provider->getCount() / $this->pageSize), 1, $this->urlBuilder);
        }
        return $this->pagination;
    }

    /**
     * @param AbstractColumn $column
     */
    private function propagateUrlBuilder(AbstractColumn $column)
    {
        if ($column instanceof UrlBuilderAwareInterface && !is_null($this->urlBuilder))
        {
            $column->setControlUrlBuilder($this->urlBuilder);
        }
        if ($this->getPagination())
        {
            $this->getPagination()->setControlUrlBuilder($this->urlBuilder);
        }
    }
}

