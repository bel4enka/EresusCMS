<?php
/**
 * Обеспечение совместимости со старыми версиями
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
 * @subpackage BC
 */

/**
 * Класс для обратной совместимости
 *
 * @package Eresus
 * @subpackage BC
 * @deprecated с 3.01 используйте {@link Eresus_DB}
 */
class DB extends Eresus_DB
{
}

/**
 * Класс для обратной совместимости
 *
 * @package Eresus
 * @subpackage BC
 * @deprecated с 3.01 используйте {@link Eresus_HTTP_Request}
 */
class HttpRequest extends Eresus_HTTP_Request
{
}

/**
 * Класс для обратной совместимости
 *
 * @package Eresus
 * @subpackage BC
 * @deprecated с 3.01 используйте {@link Eresus_Plugin}
 */
class Plugin extends Eresus_Plugin
{
}

/**
 * Класс для обратной совместимости
 *
 * @package Eresus
 * @subpackage BC
 * @deprecated с 3.01 используйте {@link Eresus_Plugin_Registry}
 */
class Plugins extends Eresus\Plugins\Registry
{
}

/**
 * Класс для обратной совместимости
 *
 * @package Eresus
 * @subpackage BC
 * @deprecated с 3.01 используйте {@link Eresus_Template}
 */
class Template extends Eresus_Template
{
}

