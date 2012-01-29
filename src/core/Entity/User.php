<?php
/**
 * ${product.title}
 *
 * Пользователь
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
 * Модель пользователя
 *
 * @property int     $id             идентификатор
 * @property string  $username       имя входа
 * @property string  $password       при чтении возвразает хеш, при записи хеширует значение
 * @property boolean $active         признак активности учётной записи
 * @property string  $lastVisit      время последнего удачного входа в систему
 * @property int     $lastLoginTime  время последней попытки входа в систему
 * @property int     $loginErrors    количество неудачных попыток входа
 * @property int     $access         уровень доступа
 * @property string  $accessStr      уровень доступа (строковое представление)
 * @property string  $fullname       полное имя
 * @property string  $mail           адрес e-mail
 * @property array   $profile        дополнительные данные профиля
 *
 * @package	Eresus
 *
 * @since 2.17
 */
class Eresus_Entity_User extends Eresus_DB_Record
{
	/**
	 * PRCE-шаблон для свойства "username"
	 *
	 * Этот шаблон должен быть совместим с JavaScript и  HTML5
	 *
	 * @var string
	 * @since 2.17
	 */
	const USERNAME_PATTERN = '^[a-z0-9_\-\.\@]+$';

	/**
	 * PRCE-фильтр для свойства "username"
	 *
	 * @var string
	 */
	const USERNAME_FILTER = '/[^a-z0-9_\-\.\@]/';

	/**
	 * Названия уровней доступа
	 *
	 * @var array
	 * @since 2.17
	 */
	static private $map = array(
		1 => 'Главный администратор',
		2 => 'Администратор',
		3 => 'Редактор',
		4 => 'Пользователь'
	);

	/**
	 * Описание таблицы
	 *
	 * @since 2.17
	 * @see Doctrine_Record_Abstract::setTableDefinition()
	 */
	public function setTableDefinition()
	{
		$this->setTableName('users');
		$this->hasColumns(array(
			'id' => array(
			'type' => 'integer',
			'length' => 4,
			'fixed' => false,
			'unsigned' => true,
			'primary' => true,
			'autoincrement' => true,
		),
		'username' => array(
			'type' => 'string',
			'length' => 255,
			'default' => '',
			'notnull' => true,
		),
		'password' => array(
			'type' => 'string',
			'length' => 32,
			'default' => '',
			'notnull' => true,
		),
		'active' => array(
			'type' => 'boolean',
			'default' => true,
			'notnull' => true,
		),
		'lastVisit' => array(
			'type' => 'timestamp',
			'notnull' => false,
		),
		'lastLoginTime' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => false,
		),
		'loginErrors' => array(
			'type' => 'integer',
			'length' => 4,
			'default' => 0,
			'notnull' => true,
		),
		'access' => array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => true,
			'default' => 5,
			'notnull' => true,
		),
		'fullname' => array(
			'type' => 'string',
			'length' => 255,
			'default' => '',
			'notnull' => true,
		),
		'mail' => array(
			'type' => 'string',
			'length' => 255,
			'default' => '',
			'notnull' => true,
		),
		'profile' => array(
			'type' => 'array',
		)));

	}
	//-----------------------------------------------------------------------------

	/**
	 * Подготовка модели к работе
	 *
	 * @since 2.17
	 * @see Doctrine_Record::setUp()
	 */
	public function setUp()
	{
		$this->hasMutator('username', 'usernameMutator');
		$this->hasMutator('password', 'passwordMutator');
		$this->hasAccessor('accessStr', 'accessStrAccessor');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Мутатор имени пользователя
	 *
	 * Очищает имя от символов, запрещённых фильтром USERNAME_FILTER.
	 *
	 * @param string $value
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	public function usernameMutator($value)
	{
		$value = preg_replace(self::USERNAME_FILTER, '', $value);
		$this->_set('username', $value);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает хеш пароля
	 *
	 * @param string $password  пароль
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public static function passwordHash($password)
	{
		return md5(md5($password));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Мутатор пароля
	 *
	 * @param string $value  пароль
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	public function passwordMutator($value)
	{
		$this->_set('password', self::passwordHash($value));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет правильность пароля
	 *
	 * @param string $password  пароль
	 *
	 * @return bool  true если пароль верен и false в противном случае
	 *
	 * @since 2.17
	 */
	public function isPasswordValid($password)
	{
		return $this->password == self::passwordHash($password);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Аксессор строкового значения уровня доступа
	 *
	 * @param bool   $load
	 * @param string $property  имя свойства (всегда должно быть «accessStr»)
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function accessStrAccessor($load, $property)
	{
		assert('$property == "accessStr"');

		if (isset(self::$map[$this->access]))
		{
			return i18n(self::$map[$this->access]);
		}

		return i18n('неизвестно');
	}
	//-----------------------------------------------------------------------------
}