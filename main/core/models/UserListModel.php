<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модель списка пользователей
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
 * Модель списка пользователя
 *
 * @property int      $filterId           Фильтр по идентификатору
 * @property string   $filterUsername     Фильтр по имени пользователя
 * @property string   $filterPassword     Фильтр по паролю
 * @property bool     $filterActive       Фильтр по активности
 * @property datetime $filterLastVisit    Фильтр по времени последнего визита
 * @property int      $filterLoginErrors  Фильтр по количеству ошибок ввода
 * @property int      $filterAccess       Фильтр по уровню доступа
 * @property string   $filterFullname     Фильтр по полному имени
 * @property string   $filterMail         Фильтр по адресу E-mail
 *
 * @see UserModel
 *
 * @package EresusCMS
 */
class UserListModel extends GenericListModel {

	/**
	 * Имя таблицы пользователей
	 *
	 * @var string
	 */
	protected $dbTable = 'users';

	/**
	 * Установка фильтра по имени пользователя
	 * @param mixed $value
	 */
	protected function setFilterUsername($value)
	{
		$this->filter['login'] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Получение фильтра по имени пользователя
	 * @return mixed
	 */
	protected function getFilterUsername()
	{
		return ecArrayValue($this->filter, 'login');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Установка фильтра по паролю
	 * @param mixed $value
	 */
	protected function setFilterPassword($value)
	{
		$this->filter['hash'] = UserModel::hash($value);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Получение фильтра по паролю
	 *
	 * Внимание! При чтениии можно только узнать - был ли установлен фильтр или
	 * нет, но нельзя узнать само значение фильтра.
	 *
	 * @return bool
	 */
	protected function getFilterPassword()
	{
		return isset($this->filter['hash']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Установка фильтра по полному имени пользователя
	 * @param mixed $value
	 */
	protected function setFilterFullname($value)
	{
		$this->filter['name'] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Получение фильтра по полному имени пользователя
	 * @return mixed
	 */
	protected function getFilterFullname()
	{
		return ecArrayValue($this->filter, 'name');
	}
	//-----------------------------------------------------------------------------

}
