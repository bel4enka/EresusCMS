<?php
/**
 * Абстрактная сущность
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
 * Абстрактная сущность
 *
 * @package Eresus
 * @subpackage ORM
 * @since 3.01
 */
abstract class Eresus_ORM_Entity
{
    /**
     * Модуль
     *
     * @var Eresus_Plugin|TContentPlugin
     */
    protected $plugin;

    /**
     * Атрибуты
     *
     * @var array
     */
    private $attrs = array();

    /**
     * Кэш геттеров
     *
     * @var array
     */
    private $gettersCache = array();

    /**
     * Конструктор
     *
     * @param Eresus_Plugin|TContentPlugin $plugin  модуль
     * @param array          $attrs   исходные значения полей
     *
     * @return Eresus_ORM_Entity
     *
     * @since 3.01
     */
    public function __construct($plugin, array $attrs = array())
    {
        $this->plugin = $plugin;
        $this->attrs = $attrs;
    }

    /**
     * "Магический" метод для доступа к свойствам объекта
     *
     * Если есть метод, имя которого состоит из префикса "get" и имени свойства, вызывает этот
     * метод для получения значения. В противном случае вызывает {@link getProperty}.
     *
     * @param string $key  Имя поля
     *
     * @return mixed  Значение поля
     *
     * @uses getProperty
     * @since 3.01
     */
    public function __get($key)
    {
        $getter = 'get' . $key;
        if (method_exists($this, $getter))
        {
            if (!isset($this->gettersCache[$key]))
            {
                $this->gettersCache[$key] = $this->$getter();
            }
            return $this->gettersCache[$key];
        }

        return $this->getProperty($key);
    }

    /**
     * "Магический" метод для установки свойств объекта
     *
     * Если есть метод, имя которого состоит из префикса "set" и имени свойства, вызывает этот
     * метод для установки значения. В противном случае вызывает {@link setProperty()}.
     *
     * @param string $key    Имя поля
     * @param mixed  $value  Значение поля
     *
     * @return void
     *
     * @uses setProperty()
     * @since 3.01
     */
    public function __set($key, $value)
    {
        $setter = 'set' . $key;
        if (method_exists($this, $setter))
        {
            $this->$setter($value);
        }
        else
        {
            $this->setProperty($key, $value);
        }
    }

    /**
     * Возвращает таблицу этой сущности
     *
     * @return Eresus_ORM_Table
     *
     * @since 3.01
     */
    public function getTable()
    {
        $entityName = get_class($this);
        $entityName = substr($entityName, strrpos($entityName, '_') + 1);
        return Eresus_ORM::getTable($this->plugin, $entityName);
    }

    /**
     * Возвращает значение основного ключа для этого объекта
     *
     * @return mixed
     *
     * @since 3.01
     */
    public function getPrimaryKey()
    {
        return $this->{$this->getTable()->getPrimaryKey()};
    }

    /**
     * Устанавливает значение свойства
     *
     * Метод не инициирует вызов сеттеров, но обрабатывает значение фильтрами
     *
     * @param string $key    Имя свойства
     * @param mixed  $value  Значение
     *
     * @return void
     *
     * @since 3.01
     */
    public function setProperty($key, $value)
    {
        $columns = $this->getTable()->getColumns();
        if (array_key_exists($key, $columns))
        {
            $column = $columns[$key];
            switch (@$column['type'])
            {
                case 'entity':
                    if (is_object($value))
                    {
                        $primaryKey = $this->getTable()->getPrimaryKey();
                        $value = $value->{$primaryKey};
                    }
            }
        }
        $this->attrs[$key] = $value;
    }

    /**
     * Возвращает значение свойства
     *
     * Читает значение непосредственно из массива свойств, не инициируя вызов геттеров
     *
     * @param string $key  имя свойства
     *
     * @return mixed  значение свойства
     *
     * @since 3.01
     */
    public function getProperty($key)
    {
        if (isset($this->attrs[$key]))
        {
            $value = $this->attrs[$key];
            $table = $this->getTable();
            $columns = $table->getColumns();
            if (array_key_exists($key, $columns))
            {
                $column = $columns[$key];
                switch (@$column['type'])
                {
                    case 'entity':
                        $table = $this->getTableByEntityClass(@$column['class']);
                        $value = $table->find($value);
                }
            }
            return $value;
        }

        return null;
    }

    //@codeCoverageIgnoreStart
    /**
     * Вызывается перед изменением в БД
     *
     * @param ezcQuery $query  запрос, который будет выполнен для сохранения записи
     *
     * @return void
     *
     * @since 3.01
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(ezcQuery $query)
    {
    }
    //@codeCoverageIgnoreEnd

    //@codeCoverageIgnoreStart
    /**
     * Вызывается после записи изменений в БД
     *
     * @return void
     *
     * @since 3.01
     */
    public function afterSave()
    {
    }
    //@codeCoverageIgnoreEnd

    //@codeCoverageIgnoreStart
    /**
     * Вызывается перед удалением записи из БД
     *
     * @param ezcQuery $query  запрос, который будет выполнен для удаления записи
     *
     * @return void
     *
     * @since 3.01
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDelete(ezcQuery $query)
    {
    }
    //@codeCoverageIgnoreEnd

    //@codeCoverageIgnoreStart
    /**
     * Вызывается после удаления записи из БД
     *
     * @return void
     *
     * @since 3.01
     */
    public function afterDelete()
    {
    }
    //@codeCoverageIgnoreEnd

    /**
     * Возвращает таблицу по имени класса сущности
     *
     * @param string $entityClass
     *
     * @throws InvalidArgumentException
     *
     * @return ORM_Table
     *
     * @since 3.01
     */
    protected function getTableByEntityClass($entityClass)
    {
        if ('' === strval($entityClass))
        {
            throw new InvalidArgumentException('$entityClass can not be blank');
        }
        $entityPluginName = substr($entityClass, 0, strpos($entityClass, '_'));
        $entityPluginName = strtolower($entityPluginName);
        $plugin = Eresus_Plugin_Registry::getInstance()->load($entityPluginName);
        $entityName = substr($entityClass, strrpos($entityClass, '_') + 1);
        $table = Eresus_ORM::getTable($plugin, $entityName);
        return $table;
    }
}

