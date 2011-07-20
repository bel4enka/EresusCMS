<?php
/**
 * ${product.title}
 *
 * Набор методов для работы с ORM
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
 *
 * $Id$
 */

/**
 * Интерфейс к статическим методам {@link http://www.doctrine-project.org/projects/orm/1.2/docs/en
 * Doctrine ORM}
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_DB_ORM
{
	/**
	 * Возвращает объект таблицы заданного компонента
	 *
	 * Псевдоним для {@link Doctrine_Core::getTable()}.
	 *
	 * <b>Пример</b>
	 *
	 * <code>
	 * $user = Eresus_DB_ORM::getTable('Eresus_Model_User')->find(1);
	 * </code>
	 *
	 * @param string $componentName  имя компонента
	 *
	 * @return Doctrine_Table  объект таблицы
	 *
	 * @since 2.16
	 */
	public static function getTable($componentName)
	{
		return Doctrine_Core::getTable($componentName);
	}
	//-----------------------------------------------------------------------------
}
