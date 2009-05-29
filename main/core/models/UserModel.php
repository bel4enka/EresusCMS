<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модель пользователя
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-${build.year}, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 * @package EresusCMS
 *
 * $Id$
 */


/**
 * Модель пользователя
 *
 * Модель описывает пользователя сайта.
 *
 * @package EresusCMS
 */
class UserModel extends GenericModel {

	/**
	 * Имя таблицы пользователей
	 *
	 * @var string
	 */
	protected $dbTable = 'users';

	/**
	 * Экземпляр модели текущего пользователя
	 *
	 * @var UserModel
	 * @see getCurrent
	 */
	private static $current;

	/**
	 * Получение модели текущего пользователя
	 *
	 * Метод реализует паттерн "Одиночка" для получения
	 * экземпляра модели пользователя, работающего в данный
	 * момент с сайтом.
	 *
	 * @return UserModel
	 * @see $current
	 */
	public static function getCurrent()
	{
		if (!self::$current) {

			self::$current = new UserModel();

		}

		return self::$current;
	}
	//-----------------------------------------------------------------------------
}
