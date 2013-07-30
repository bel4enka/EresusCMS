<?php
/**
 * ORM
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
 * ORM
 *
 * @package Eresus
 * @subpackage ORM
 * @since 3.01
 */
class Eresus_ORM
{
    /**
     * Драйвер СУБД
     * @var Eresus_ORM_Driver_Abstract
     * @since 3.01
     */
    private static $driver = null;

    /**
     * Кэш таблиц
     *
     * @var array
     * @since 3.01
     */
    private static $tables = array();

    /**
     * Типы полей
     *
     * @var array
     * @since 3.01
     */
    private static $filedTypes = array('boolean', 'date', 'float', 'integer', 'string', 'time',
        'timestamp', 'entity');

    /**
     * Задаёт используемый драйвер СУБД
     *
     * @param Eresus_ORM_Driver_Abstract $driver
     *
     * @since 3.01
     */
    public static function setDriver(Eresus_ORM_Driver_Abstract $driver)
    {
        self::$driver = $driver;
    }

    /**
     * Возвращает используемый драйвер СУБД
     *
     * @return Eresus_ORM_Driver_Abstract
     *
     * @since 3.01
     */
    public static function getDriver()
    {
        if (null === self::$driver)
        {
            self::$driver = new Eresus_ORM_Driver_MySQL();
        }
        return self::$driver;
    }

    /**
     * Возвращает объект таблицы для указанной сущности
     *
     * @param Eresus_ORM_EntityOwnerInterface $owner       объект, которому принадлежит сущность
     * @param string                          $entityName  имя сущности (имя класса без префикса и
     *                                                     слова «Entity»)
     *
     * @return Eresus_ORM_Table
     *
     * @since 3.01
     */
    public static function getTable(Eresus_ORM_EntityOwnerInterface $owner, $entityName)
    {
        $className = $owner->getOrmClassPrefix() . '_Entity_Table_' . $entityName;
        if (!isset(self::$tables[$className]))
        {
            self::$tables[$className] = new $className(self::$driver, $owner);
        }
        return self::$tables[$className];
    }

    /**
     * Возвращает возможные типы полей
     *
     * @return array
     *
     * @since 3.01
     */
    public static function fieldTypes()
    {
        return self::$filedTypes;
    }
}

