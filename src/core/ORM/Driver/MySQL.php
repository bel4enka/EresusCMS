<?php
/**
 * Драйвер MySQL
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
 * Драйвер MySQL
 *
 * @package Eresus
 * @subpackage ORM
 * @since 3.01
 */
class Eresus_ORM_Driver_MySQL extends Eresus_ORM_Driver_Abstract
{
    /**
     * Создаёт таблицу
     *
     * @param Eresus_ORM_Table $table
     *
     * @return void
     *
     * @since 3.01
     */
    public function createTable(Eresus_ORM_Table $table)
    {
        if ($table->isAlias())
        {
            return;
        }
        $db = Eresus_DB::getHandler();
        $tableName = $db->options->tableNamePrefix . $table->getName();

        $sql = array();
        foreach ($table->getColumns() as $name => $attrs)
        {
            $sql []= $name . ' ' . $this->getFieldDefinition($attrs);
        }
        $sql []= 'PRIMARY KEY (' . $table->getPrimaryKey() . ')';
        foreach ($table->getIndexes() as $name => $params)
        {
            $sql []= 'KEY ' . $name . ' (' . implode(', ', $params['fields']) . ')';
        }
        $sql = "CREATE TABLE $tableName (" . implode(', ', $sql) .
            ') ENGINE InnoDB DEFAULT CHARSET=utf8';
        $db->exec($sql);
    }

    /**
     * Удаляет таблицу
     *
     * @param Eresus_ORM_Table $table
     *
     * @return void
     *
     * @since 3.01
     */
    public function dropTable(Eresus_ORM_Table $table)
    {
        $db = Eresus_DB::getHandler();
        $tableName = $db->options->tableNamePrefix . $table->getName();
        $sql = "DROP TABLE $tableName";
        $db->exec($sql);
    }

    /**
     * Преобразует значение поля ORM в значение PDO
     *
     * @param mixed  $ormValue      значение поля
     * @param string $ormFieldType  тип поля
     *
     * @throws Eresus_Exception_InvalidArgumentType
     *
     * @return mixed
     *
     * @since 3.01
     */
    public function pdoFieldValue($ormValue, $ormFieldType)
    {
        if (is_null($ormValue))
        {
            return null;
        }

        switch ($ormFieldType)
        {
            case 'boolean':
                $ormValue = intval($ormValue);
                break;
            case 'date':
                if (!($ormValue instanceof DateTime))
                {
                    throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1, 'DateTime',
                        $ormValue);
                }
                /* @var DateTime $ormValue */
                $ormValue = $ormValue->format('Y-m-d');
                break;
            case 'datetime':
                if (!($ormValue instanceof DateTime))
                {
                    throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1, 'DateTime',
                        $ormValue);
                }
                /* @var DateTime $ormValue */
                $ormValue = $ormValue->format('Y-m-d H:i:s');
                break;
            case 'entity':
                if (!($ormValue instanceof Eresus_ORM_Entity))
                {
                    throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1,
                        'Eresus_ORM_Entity', $ormValue);
                }
                $ormValue = $ormValue->getPrimaryKey();
                break;
            case 'time':
                if (!($ormValue instanceof DateTime))
                {
                    throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1, 'DateTime',
                        $ormValue);
                }
                /* @var DateTime $ormValue */
                $ormValue = $ormValue->format('H:i:s');
                break;
            case 'timestamp':
                if ($ormValue instanceof DateTime)
                {
                    /* @var DateTime $ormValue */
                    $ormValue = $ormValue->getTimestamp();
                }
                else
                {
                    $ormValue = intval($ormValue);
                }
                break;
        }
        return $ormValue;
    }

    /**
     * Возвращает объявление поля
     *
     * @param array $attrs  атрибуты поля
     *
     * @throws InvalidArgumentException  в случае если в $attrs нет нужных элементов или их значения
     *                                   неверны
     *
     * @return string
     *
     * @since 3.01
     */
    private function getFieldDefinition(array $attrs)
    {
        if (!array_key_exists('type', $attrs))
        {
            throw new InvalidArgumentException('No type specified for field');
        }

        if (!in_array($attrs['type'], Eresus_ORM::fieldTypes()))
        {
            throw new InvalidArgumentException('Invalid type "' . $attrs['type'] . '"');
        }

        $method = 'getDefinitionFor' . $attrs['type'];
        $sql = $this->$method($attrs);

        return $sql;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * Возвращает SQL-объявление поля типа boolean
     *
     * @param array $attrs  атрибуты поля
     *
     * @return string  SQL
     *
     * @since 3.01
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getDefinitionForBoolean(array $attrs)
    {
        $sql = 'BOOL';
        $sql .= $this->getDefinitionForDefault($attrs);
        return $sql;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * Возвращает SQL-объявление поля типа date
     *
     * @param array $attrs  атрибуты поля
     *
     * @return string  SQL
     *
     * @since 3.01
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getDefinitionForDate(array $attrs)
    {
        $sql = 'DATE';
        $sql .= $this->getDefinitionForDefault($attrs);
        return $sql;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * Возвращает SQL-объявление поля типа datetime
     *
     * @param array $attrs  атрибуты поля
     *
     * @return string  SQL
     *
     * @since 3.01
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getDefinitionForDatetime(array $attrs)
    {
        $sql = 'DATETIME';
        $sql .= $this->getDefinitionForDefault($attrs);
        return $sql;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * Возвращает SQL-объявление поля типа entity
     *
     * @return string  SQL
     *
     * @since 3.01
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getDefinitionForEntity()
    {
        return $this->getDefinitionForInteger(array('type' => 'integer', 'unsigned' => true));
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * Возвращает SQL-объявление поля типа float
     *
     * @param array $attrs  атрибуты поля
     *
     * @return string  SQL
     *
     * @since 3.01
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getDefinitionForFloat(array $attrs)
    {
        if (isset($attrs['length']) && 2147483647 == $attrs['length'])
        {
            $sql = 'DOUBLE';
        }
        else
        {
            $sql = 'FLOAT';
        }
        $sql .= $this->getDefinitionForDefault($attrs);
        return $sql;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * Возвращает SQL-объявление поля типа integer
     *
     * @param array $attrs  атрибуты поля
     *
     * @return string  SQL
     *
     * @since 3.01
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getDefinitionForInteger(array $attrs)
    {
        $sql = 'INT';
        $length = isset($attrs['length']) ? $attrs['length'] : 10;
        $sql .= '(' . $length . ')';
        if (@$attrs['unsigned'])
        {
            $sql .= ' UNSIGNED';
        }
        if (@$attrs['autoincrement'])
        {
            $sql .= ' AUTO_INCREMENT';
        }
        $sql .= $this->getDefinitionForDefault($attrs);
        return $sql;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * Возвращает SQL-объявление поля типа string
     *
     * @param array $attrs  атрибуты поля
     *
     * @return string  SQL
     *
     * @since 3.01
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getDefinitionForString(array $attrs)
    {
        if (isset($attrs['length']) && 255 >= $attrs['length'])
        {
            $sql = 'VARCHAR(' . $attrs['length'] . ')';
        }
        elseif (!isset($attrs['length']) || 65535 >= $attrs['length'])
        {
            $sql = 'TEXT';
        }
        else
        {
            $sql = 'LONGTEXT';
        }
        $sql .= $this->getDefinitionForDefault($attrs);
        return $sql;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * Возвращает SQL-объявление поля типа time
     *
     * @param array $attrs  атрибуты поля
     *
     * @return string  SQL
     *
     * @since 3.01
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getDefinitionForTime(array $attrs)
    {
        $sql = 'TIME';
        $sql .= $this->getDefinitionForDefault($attrs);
        return $sql;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * Возвращает SQL-объявление поля типа timestamp
     *
     * @param array $attrs  атрибуты поля
     *
     * @return string  SQL
     *
     * @since 3.01
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getDefinitionForTimestamp(array $attrs)
    {
        $sql = 'INT(10) UNSIGNED';
        $attrs['unsigned'] = true;
        $sql .= $this->getDefinitionForDefault($attrs);
        return $sql;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * Возвращает SQL-объявление значения по умолчанию для поля
     *
     * @param array $attrs  атрибуты поля
     *
     * @return string  SQL
     *
     * @since 3.01
     */
    private function getDefinitionForDefault(array $attrs)
    {
        $sql = '';
        if (array_key_exists('default', $attrs))
        {
            $sql .= ' DEFAULT ';
            if (is_null($attrs['default']))
            {
                $sql .= 'NULL';
            }
            elseif (in_array($attrs['type'], array('date', 'string', 'time', 'timestamp')))
            {
                $sql .= '\'' . $attrs['default'] . '\'';
            }
            elseif (in_array($attrs['type'], array('boolean')))
            {
                $sql .= $attrs['default'] ? '1' : '0';
            }
            else
            {
                $sql .= $attrs['default'];
            }
        }
        return $sql;
    }
}

