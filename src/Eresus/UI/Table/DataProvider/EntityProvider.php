<?php
/**
 * Поставщик данных для таблиц, возвращающий объекты ORM
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

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Eresus\UI\Table\Column;

/**
 * Поставщик данных для таблиц, возвращающий объекты ORM
 *
 * @since 3.01
 */
class EntityProvider implements DataProviderInterface
{
    /**
     * Имя класса сущностей
     * @var EntityRepository
     * @since 3.01
     */
    protected $repository;

    /**
     * @param EntityRepository $repository
     * @since 3.01
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Возвращает список столбцов
     *
     * @return Column[]
     *
     * @since 3.01
     */
    public function getColumns()
    {
        $columns = array();
        $methods = get_class_methods($this->repository->getClassName());
        foreach ($methods as $method)
        {
            if (preg_match('/^(get|is)([A-Z].*)$/', $method, $m))
            {
                $column = new Column($m[2]);
                $column->setGetter($method);
                $columns []= $column;
            }
        }
        return $columns;
    }

    /**
     * Возвращает объекты для строк таблицы
     *
     * @return Collection
     *
     * @since 3.01
     */
    public function getItems()
    {
        return $this->repository->findAll();
    }
}

