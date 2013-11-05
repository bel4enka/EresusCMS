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

use Eresus\UI\Widget;
use Eresus\UI\Control\UrlBuilder\UrlBuilderAwareInterface;
use Eresus\UI\Table\DataProvider\DataProviderInterface;
use Eresus\UI\Control\UrlBuilder\UrlBuilderInterface;
use Eresus\Templating\TemplateManager;
use Doctrine\Common\Collections\Collection;

/**
 * Таблица-список
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
     * Построитель адресов для ЭУ
     * @var UrlBuilderInterface
     * @since 3.01
     */
    protected $urlBuilder = null;

    /**
     * Столбцы таблицы
     * @var null|AbstractColumn[]
     * @since 3.01
     */
    private $columns = array();

    /**
     * Строки данных
     * @var null|Collection
     * @since 3.01
     */
    private $data = null;

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
     * Задаёт построитель адресов по умолчанию для элементов управления, использующихся в таблице
     *
     * @param UrlBuilderInterface $urlBuilder
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
            $items = $this->provider->getItems();
            $this->data = $items;
        }
        return $this->data;
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
    }
}

