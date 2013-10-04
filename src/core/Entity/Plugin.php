<?php
/**
 * Сведения о модуле расширения
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
 * Сведения о модуле расширения
 *
 * @property       string $name
 * @property       bool   $active
 * @property       array  $settings
 *
 * @package Eresus
 * @subpackage Domain
 */
class Eresus_Entity_Plugin extends Eresus_ORM_Entity
{
    /**
     * Геттер свойства «settings»
     *
     * @return array
     */
    protected function getSettings()
    {
        return unserialize($this->getProperty('settings'));
    }

    /**
     * Сеттер свойства «settings»
     *
     * @param array $settings
     */
    protected function setSettings(array $settings)
    {
        $this->setProperty('settings', serialize($settings));
    }
}

