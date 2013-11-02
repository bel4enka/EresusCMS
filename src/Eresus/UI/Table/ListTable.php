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

use Doctrine\Common\Collections\Collection;
use Eresus\Kernel;
use Eresus\Templating\Template;
use Eresus\Templating\TemplateManager;
use Eresus\UI\Table\DataProvider\DataProviderInterface;

/**
 * Таблица-список
 *
 * @api
 * @since 3.01
 */
class ListTable
{
    /**
     * Поставщик данных
     * @var DataProviderInterface
     * @since 3.01
     */
    protected $provider;

    /**
     * Столбцы таблицы
     * @var null|Column[]
     * @since 3.01
     */
    private $columns = null;

    /**
     * Строки данных
     * @var null|Collection
     * @since 3.01
     */
    private $data = null;

    /**
     * @param DataProviderInterface $dataProvider
     * @since 3.01
     */
    public function __construct(DataProviderInterface $dataProvider)
    {
        $this->provider = $dataProvider;
    }

    public function addColumn($caption)
    {
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
     * @return array
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
     * Возвращает разметку
     *
     * @return string  HTML
     *
     * @since 3.01
     */
    public function getHtml()
    {
        $tmpl = $this->getTemplate();
        return $tmpl->compile(array('table' => $this));
    }

    /**
     * Возвращает разметку
     *
     * @return string  HTML
     *
     * @since 3.01
     */
    public function __toString()
    {
        try
        {
            return $this->getHtml();
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        }
    }

    /**
     * Возвращает шаблон
     *
     * @return Template
     *
     * @since 3.01
     */
    protected function getTemplate()
    {
        /** @var Kernel $kernel */
        $kernel = $GLOBALS['kernel']; // TODO Переделать!
        /** @var TemplateManager $templates */
        $templates = $kernel->getContainer()->get('templates');
        return $templates->getAdminTemplate('UI/Table/ListTable.html');
    }
}

