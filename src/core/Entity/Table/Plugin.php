<?php
/**
 * Таблица модулей расширения
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
 * @subpackage Domain
 */

/**
 * Таблица модулей расширения
 *
 * @package Eresus
 * @subpackage Domain
 */
class Eresus_Entity_Table_Plugin extends Eresus_ORM_Table
{
    protected function setTableDefinition()
    {
        $this->setTableName('plugins');
        $this->hasColumns(array(
            'name' => array(
                'type' => 'string',
                'length' => 255,
            ),
            'active' => array(
                'type' => 'boolean',
            ),
            'settings' => array(
                'type' => 'string',
                'length' => 65535,
            ),
        ));
        $this->index('active_idx', array('fields' => array('active')));
    }
}

