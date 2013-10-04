<?php
/**
 * Абстрактная таблица БД
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
 *
 * @package Eresus
 * @subpackage ORM
 */

/**
 * Абстрактная таблица БД
 *
 * @package Eresus
 * @subpackage ORM
 * @since 3.01
 */
abstract class Eresus_ORM_Table
{
    /**
     * Владелец
     *
     * @var Eresus_ORM_EntityOwnerInterface
     * @since 3.01
     */
    protected $owner;

    /**
     * Карта соответствия типов ORM типам PDO
     *
     * @var array
     * @since 3.01
     */
    protected $orm2pdoTypeMap = array(
        'boolean' => PDO::PARAM_BOOL,
        'integer' => PDO::PARAM_INT,
        'entity' => PDO::PARAM_INT,
        'float' => null,
        'string' => PDO::PARAM_STR,
        'timestamp' => PDO::PARAM_STR,
        'time' => PDO::PARAM_STR,
        'date' => PDO::PARAM_STR,
    );

    /**
     * Драйвер СУБД
     * @var Eresus_ORM_Driver_Abstract
     * @since 3.01
     */
    private $driver;

    /**
     * Имя таблицы
     *
     * @var string
     * @since 3.01
     */
    private $tableName = null;

    /**
     * Признак того, что таблица является псевдонимом другой
     *
     * В этом случае в {@link tableName} хранится имя основной таблицы
     *
     * @var bool
     * @since 3.01
     */
    private $isAlias = false;

    /**
     * Описание столбцов
     *
     * @var array
     * @since 3.01
     */
    private $columns = array();

    /**
     * Имя класса сущности
     *
     * @var string
     * @since 3.01
     */
    private $entityClass = null;

    /**
     * Имя поля основного ключа
     *
     * @var string
     * @since 3.01
     */
    private $primaryKey = 'id';

    /**
     * Порядок сортировки
     *
     * Массив пар «поле=>ezcQuerySelect::ASC/DESC»
     *
     * @var array
     * @since 3.01
     */
    private $ordering = array();

    /**
     * Индексы
     *
     * @var array
     * @since 3.01
     */
    private $indexes = array();

    /**
     * Реестр объектов
     *
     * @var Eresus_ORM_Entity[]
     *
     * @since 3.01
     */
    private $registry = array();

    /**
     * Конструктор
     *
     * @param Eresus_ORM_Driver_Abstract      $driver
     * @param Eresus_ORM_EntityOwnerInterface $owner
     *
     * @return Eresus_ORM_Table
     *
     * @since 3.01
     */
    public function __construct(Eresus_ORM_Driver_Abstract $driver,
        Eresus_ORM_EntityOwnerInterface $owner)
    {
        $this->driver = $driver;
        $this->owner = $owner;
        $this->setTableDefinition();
    }

    /**
     * Возвращает имя таблицы
     *
     * @return string
     *
     * @since 3.01
     */
    public function getName()
    {
        return $this->tableName;
    }

    /**
     * Возвращает имя класса сущности
     *
     * @return string
     *
     * @since 3.01
     */
    public function getEntityClass()
    {
        if (is_null($this->entityClass))
        {
            $thisClass = get_class($this);
            $this->entityClass = str_replace('_Table_', '_', $thisClass);
        }
        return $this->entityClass;
    }

    /**
     * Возвращает поля таблицы
     *
     * @return array
     *
     * @since 3.01
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Возвращает имя основного ключа
     *
     * @return string
     *
     * @since 3.01
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Возвращает сортировку
     *
     * @return array
     *
     * @since 3.01
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * Возвращает индексы
     *
     * @return array
     *
     * @since 3.01
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * Возвращает true если этот класс является псевдонимом другой таблицы
     *
     * @return bool
     *
     * @since 3.01
     */
    public function isAlias()
    {
        return $this->isAlias;
    }

    /**
     * Помещает сущность в таблицу
     *
     * @param Eresus_ORM_Entity $entity
     *
     * @return void
     *
     * @since 3.01
     */
    public function persist(Eresus_ORM_Entity $entity)
    {
        $q = Eresus_DB::getHandler()->createInsertQuery();
        $q->insertInto($this->getName());
        $this->bindValuesToQuery($entity, $q);
        $entity->beforeSave($q);
        $q->execute();
        $columns = $this->getColumns();
        if (@$columns[$this->getPrimaryKey()]['autoincrement'])
        {
            $entity->{$this->getPrimaryKey()} = Eresus_DB::getHandler()->lastInsertId();
        }
        $entity->afterSave();
    }

    /**
     * Обновляет сущность в таблице
     *
     * @param Eresus_ORM_Entity $entity
     *
     * @return void
     *
     * @since 3.01
     */
    public function update(Eresus_ORM_Entity $entity)
    {
        $pKey = $this->getPrimaryKey();
        $columns = $this->getColumns();
        $q = Eresus_DB::getHandler()->createUpdateQuery();
        $q->update($this->getName())->
            where($q->expr->eq($pKey,
                $q->bindValue($entity->$pKey, null, $this->pdoFieldType($columns[$pKey]['type']))
            ));
        $this->bindValuesToQuery($entity, $q);
        $entity->beforeSave($q);
        $q->execute();
        $entity->afterSave();
    }

    /**
     * Удаляет сущность из таблицы
     *
     * @param Eresus_ORM_Entity $entity
     *
     * @return void
     *
     * @@since 3.01
     */
    public function delete(Eresus_ORM_Entity $entity)
    {
        $pKey = $this->getPrimaryKey();
        $columns = $this->getColumns();
        $q = Eresus_DB::getHandler()->createDeleteQuery();
        $q->deleteFrom($this->getName())->
            where($q->expr->eq($pKey,
                $q->bindValue($entity->$pKey, null, $this->pdoFieldType($columns[$pKey]['type']))
            ));
        $entity->beforeDelete($q);
        $q->execute();
        $entity->afterDelete();
    }

    /**
     * Возвращает количество записей
     *
     * @param ezcQuerySelect $query
     *
     * @return int
     *
     * @since 3.01
     */
    public function count(ezcQuerySelect $query = null)
    {
        $query = $query ?: $this->createCountQuery();
        $raw = $query->fetch();
        if ($raw)
        {
            return $raw['record_count'];
        }
        return 0;
    }

    /**
     * Возвращает все записи
     *
     * @param int $limit   максимум элементов, который следует вернуть
     * @param int $offset  сколько элементов пропустить
     *
     * @return array
     *
     * @since 3.01
     */
    public function findAll($limit = null, $offset = 0)
    {
        $q = $this->createSelectQuery();
        $items = $this->loadFromQuery($q, $limit, $offset);
        return $items;
    }

    /**
     * Возвращает все записи, удовлетворяющие фильтру
     *
     * @param array $filter  набор пар «поле => значение»
     *
     * @throws LogicException
     *
     * @return Eresus_ORM_Entity[]
     *
     * @since 3.01
     */
    public function findAllBy(array $filter)
    {
        $q = $this->createSelectQuery();
        $where = array();
        $columns = $this->getColumns();
        foreach ($filter as $field => $value)
        {
            if (!array_key_exists($field, $columns))
            {
                throw new LogicException(sprintf('Unknown column "%s" in filter', $field));
            }
            $where []= $q->expr->eq($field, $q->bindValue($value, ":$field",
                $this->pdoFieldType($columns[$field]['type'])));
        }
        $q->where($q->expr->lAnd($where));
        return $this->loadFromQuery($q);
    }

    /**
     * Возвращает сущность по основному ключу
     *
     * @param mixed $id
     *
     * @return Eresus_ORM_Entity
     *
     * @since 3.01
     */
    public function find($id)
    {
        if (array_key_exists($id, $this->registry))
        {
            return $this->registry[$id];
        }

        $pKey = $this->getPrimaryKey();
        $columns = $this->getColumns();
        $q = $this->createSelectQuery();
        $q->where($q->expr->eq($pKey,
            $q->bindValue($id, null, $this->pdoFieldType($columns[$pKey]['type']))));
        $entity = $this->loadOneFromQuery($q);

        if (null !== $entity)
        {
            $this->registry[$id] = $entity;
        }
        return $entity;
    }

    /**
     * Возвращает заготовку запроса SELECT
     *
     * @param bool $fill  заполнить запрос начальными данными
     *
     * @return ezcQuerySelect
     *
     * @since 3.01
     */
    public function createSelectQuery($fill = true)
    {
        $q = Eresus_DB::getHandler()->createSelectQuery();
        $q->from($this->getName());
        if ($fill)
        {
            $q->select('*');
            $columns = $this->getColumns();
            if (count($this->ordering) > 0)
            {
                foreach ($this->ordering as $orderBy)
                {
                    $q->orderBy($orderBy[0], $orderBy[1]);
                }
            }
            elseif (isset($columns['position']))
            {
                $q->orderBy('position');
            }
        }
        return $q;
    }

    /**
     * Возвращает заготовку запроса SELECT для подсчёта количества записей
     *
     * @return ezcQuerySelect
     *
     * @since 3.01
     */
    public function createCountQuery()
    {
        $q = Eresus_DB::getHandler()->createSelectQuery();
        $q->select($q->alias($q->expr->count('*'), 'record_count'));
        $q->from($this->getName());
        $q->limit(1);
        return $q;
    }

    /**
     * Возвращает массив сущностей на основе запроса
     *
     * @param ezcQuerySelect $query
     * @param int            $limit   максимум элементов, который следует вернуть
     * @param int            $offset  сколько элементов пропустить
     *
     * @return array
     *
     * @since 3.01
     */
    public function loadFromQuery(ezcQuerySelect $query, $limit = null, $offset = 0)
    {
        if ($limit)
        {
            $query->limit($limit, $offset);
        }
        $raw = $query->fetchAll();
        $items = array();
        if ($raw)
        {
            foreach ($raw as $attrs)
            {
                $items []= $this->entityFactory($attrs);
            }
        }
        return $items;
    }

    /**
     * Возвращает сущность на основе запроса
     *
     * @param ezcQuerySelect $query
     *
     * @return Eresus_ORM_Entity
     *
     * @since 3.01
     */
    public function loadOneFromQuery(ezcQuerySelect $query)
    {
        $query->limit(1);
        $attrs = $query->fetch();
        if ($attrs)
        {
            return $this->entityFactory($attrs);
        }
        return null;
    }

    //@codeCoverageIgnoreStart
    /**
     * Метод должен устанавливать свойства таблицы БД
     *
     * В этом методе необходимо сделать следующее:
     *
     * 1. Задать имя таблицы вызовом {@link setTableName()}
     * 2. Определить столбцы вызовом {@link hasColumns()}
     * 3. Создать необходимые индексы вызовом {@link index()}
     *
     * @return void
     *
     * @since 3.01
     */
    abstract protected function setTableDefinition();
    //@codeCoverageIgnoreEnd

    /**
     * Устанавливает имя таблицы
     *
     * @param string $name
     *
     * @throws LogicException  если перед setTableName() был вызван {@link isAlias()} или
     *                         setTableName() вызван повторно
     * @return void
     *
     * @since 3.01
     */
    protected function setTableName($name)
    {
        if (null !== $this->tableName)
        {
            if ($this->isAlias)
            {
                throw new LogicException(
                    'Method setTableName() can not be executed after isAlias()');
            }
            else
            {
                throw new LogicException(
                    'Method setTableName() can not be executed more then once');
            }
        }
        $this->tableName = $name;
    }

    /**
     * Объявляет таблицу псевдонимом другой таблицы
     *
     * Если вы используете этот метод в {@link setTableDefinition()}, то методы
     * {@link setTableName()} и {@link index()} использовать нельзя.
     *
     * @param string $tableName  имя основной таблицы (это имя именно таблицы БД, а не её класса)
     *
     * @throws LogicException  если перед isAlias() был вызван {@link setTableName()} или isAlias()
     *                         вызван повторно
     *
     * @since 3.01
     */
    protected function isAliasFor($tableName)
    {
        if (null !== $this->tableName)
        {
            if ($this->isAlias)
            {
                throw new LogicException(
                    'Method isAlias() can not be executed more then once');
            }
            else
            {
                throw new LogicException(
                    'Method isAlias() can not be executed after setTableName()');
            }
        }
        $this->tableName = $tableName;
        $this->isAlias = true;
    }

    /**
     * Устанавливает описания столбцов
     *
     * Аргумент $columns должен быть ассоциативным массивом, где каждый элемент соответствует
     * одному столбцу таблицы, при этом ключ элемента должен задавать имя столбца, а тело элемента —
     * описание этого столбца. В свою очередь, тело элемента должно быть ассоциативным массивом, в
     * котором можно использовать следующие ключи:
     *
     * - type (string) — тип поля (см. ниже).
     * - autoincrement (bool) — Автоинкрементное. Допустим только у первого столбца.
     * - length (int) — размер столбца.
     * - unsigned (bool) — беззнаковое число.
     *
     * Первый столбец всегда назначается основным ключом.
     *
     * Возможные типы полей:
     *
     * - boolean
     * - date
     * - datetime
     * - float
     * - integer
     * - string
     * - time
     * - timestamp — хранится как целое число
     *
     * @param array $columns
     *
     * @throws InvalidArgumentException
     *
     * @return void
     *
     * @since 3.01
     */
    protected function hasColumns(array $columns)
    {
        foreach ($columns as $name => $column)
        {
            if (!is_string($name) || !preg_match('/^[a-z_]+$/i', $name))
            {
                throw new InvalidArgumentException(sprintf(
                    'Column name must be a non empty string consisted of "a-z" or "_", got "%s"',
                    $name));
            }
            if (!array_key_exists('type', $column))
            {
                throw new InvalidArgumentException(
                    sprintf('No "type" element in "%s" definition', $name));
            }
        }
        $this->columns = $columns;
        reset($columns);
        $this->primaryKey = key($columns);
    }

    /**
     * Определяет индекс
     *
     * @param string $name    имя индекса
     * @param array  $params  свойства индекса
     *
     * @return void
     *
     * @since 3.01
     */
    protected function index($name, array $params)
    {
        $this->indexes[$name] = $params;
    }

    /**
     * Задаёт порядок сортировки по умолчанию
     *
     * Если порядок сортировки не задан, но есть поле «position» сортировка будет осуществляться по
     * нему по умолчанию.
     *
     * Примеры:
     * <code>
     * // По порядковому номеру
     * $this->setOrdering('position');
     * // По дате, новые в начале
     * $this->setOrdering('date', ezcQuerySelect::DESC);
     * // Сначала по названию, потом по дате
     * $this->setOrdering('title', 'ASC', 'date', 'DESC');
     * </code>
     *
     * @return void
     *
     * @since 3.01
     */
    protected function setOrdering()
    {
        $args = func_get_args();
        if (count($args) % 2 != 0)
        {
            $args []= ezcQuerySelect::ASC;
        }
        $this->ordering = array();
        while (count($args))
        {
            $field = array_shift($args);
            $dir = array_shift($args);
            $this->ordering []= array($field, $dir);
        }
    }

    /**
     * Возвращает тип поля PDO на основе типа поля ORM
     *
     * @param string $ormFieldType
     *
     * @throws Eresus_Exception_InvalidArgumentType  если $ormFieldType не строка
     * @throws InvalidArgumentException  если указан неизвестный тип
     *
     * @return int|null
     *
     * @since 3.01
     */
    protected function pdoFieldType($ormFieldType)
    {
        if (!is_string($ormFieldType))
        {
            throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1, 'string',
                $ormFieldType);
        }

        if (!array_key_exists($ormFieldType, $this->orm2pdoTypeMap))
        {
            throw new InvalidArgumentException('Unknown field type: ' . $ormFieldType);
        }
        return $this->orm2pdoTypeMap[$ormFieldType];
    }

    /**
     * Возвращает значение поля, пригодное для использования с PDO
     *
     * @param mixed  $ormValue      значение поля
     * @param string $ormFieldType  см. {@link hasColumns()}
     *
     * @throws Eresus_Exception_InvalidArgumentType
     *
     * @return mixed
     *
     * @since 3.01
     */
    protected function pdoFieldValue($ormValue, $ormFieldType)
    {
        if (!is_string($ormFieldType))
        {
            throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 2, 'string',
                $ormFieldType);
        }

        return $this->driver->pdoFieldValue($ormValue, $ormFieldType);
    }

    /**
     * Превращает значение PDO в значение ORM
     *
     * @param mixed  $value  значение поля
     * @param array  $attrs  атрибуты поля, см. {@link hasColumns()}
     *
     * @throws Eresus_Exception_InvalidArgumentType
     *
     * @return mixed
     *
     * @since 3.01
     */
    protected function convertPdoValue($value, array $attrs)
    {
        switch ($attrs['type'])
        {
            case 'date':
            case 'time':
            case 'datetime':
                $value = new DateTime(strval($value));
                break;
            case 'timestamp':
                $value = new DateTime('@' . strval($value));
                break;
        }
        return $value;
    }

    /**
     * Фабрика сущностей
     *
     * @param array $values
     *
     * @throws InvalidArgumentException
     *
     * @return Eresus_ORM_Entity
     *
     * @since 3.01
     */
    protected function entityFactory(array $values)
    {
        if (!array_key_exists($this->getPrimaryKey(), $values))
        {
            throw new InvalidArgumentException('Primary key value not found in $values argument.');
        }

        $id = $values[$this->getPrimaryKey()];
        if (array_key_exists($id, $this->registry))
        {
            return $this->registry[$id];
        }

        $entityClass = $this->getEntityClass();
        foreach ($this->getColumns() as $name => $attrs)
        {
            $values[$name] = $this->convertPdoValue($values[$name], $attrs);
        }
        $entity = new $entityClass($this->owner, $values);
        $this->registry[$id] = $entity;
        return $entity;
    }

    /**
     * Привязывает значения свойств объекта к запросу
     *
     * @param Eresus_ORM_Entity $entity  объект
     * @param ezcQuery   $query   запрос
     *
     * @since 3.01
     */
    protected function bindValuesToQuery(Eresus_ORM_Entity $entity, ezcQuery $query)
    {
        /** @var ezcQueryInsert|ezcQueryUpdate $query */
        foreach ($this->getColumns() as $name => $attrs)
        {
            $type = $this->pdoFieldType($attrs['type']);
            $value = $this->pdoFieldValue($entity->getProperty($name), $attrs['type']);
            $query->set($name, $query->bindValue($value, ":$name", $type));
        }
    }
}

