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
     * Возвращает объект таблицы для указанной сущности указанного плагина
     *
     * @param Eresus_Plugin|TContentPlugin $plugin      плагин, которому принадлежит сущность
     * @param string                       $entityName  имя сущности (без имени плагина и слова
     *                                                  «Entity»)
     *
     * @return Eresus_ORM_Table
     *
     * @throws Eresus_Exception_InvalidArgumentType
     *
     * @since 3.01
     */
    public static function getTable($plugin, $entityName)
    {
        if (!($plugin instanceof Eresus_Plugin) && !($plugin instanceof TContentPlugin))
        {
            throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1,
                'Eresus_Plugin or TContentPlugin instance', $plugin);
        }
        $className = get_class($plugin);
        if ($plugin instanceof TPlugin)
        {
            // Удаляем букву «T» из начала имени класса
            $className = substr($className, 1);
        }
        $className .= '_Entity_Table_' . $entityName;
        if (!isset(self::$tables[$className]))
        {
            self::$tables[$className] = new $className($plugin);
        }
        return self::$tables[$className];
    }

    /**
     * Возвращает возможные типы полей
     *
     * @return array
     *
     * @since 1.00
     */
    public static function fieldTypes()
    {
        return self::$filedTypes;
    }
}

