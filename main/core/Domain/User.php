<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Пользователь
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
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

// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Users', 'doctrine');

/**
 * Модель пользователя
 *
 * @property int	  $id
 * @property string $username
 * @property string $password       при чтении возвразает хеш, при записи хеширует значение
 * @property int	  $active
 * @property string $lastVisit
 * @property int	  $lastLoginTime  время последней попытки входа в систему
 * @property int	  $loginErrors
 * @property int	  $access
 * @property string $fullname
 * @property string $mail
 * @property string $profile
 *
 * @package	EresusCMS
 *
 * @since 2.16
 */
class User extends EresusActiveRecord
{
	/**
	 * PRCE-фильтр для свойства "username"
	 *
	 * @var string
	 */
	const USERNAME_FILTER = '/[^a-z0-9_\-\.\@]/';

	/**
	 * (non-PHPdoc)
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
			'type' => 'integer',
			'length' => 1,
			'default' => 1,
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
			'type' => 'string',
			'default' => '',
			'notnull' => true,
		)));

	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Doctrine_Record::setUp()
	 */
	public function setUp()
	{
		$this->hasMutator('username', 'usernameMutator');
		$this->hasMutator('password', 'passwordMutator');
		$this->hasAccessorMutator('profile', 'unserializeAccessor', 'serializeMutator');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Мутатор имени пользователя
	 *
	 * @param string $value
	 *
	 * @return void
	 *
	 * @since 2.16
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
	 * @param string $password
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public static function passwordHash($password)
	{
		return md5(md5($password));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Мутатор пароля
	 *
	 * @param string $value
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function passwordMutator($value)
	{
		$this->_set('password', self::passwordHash($value));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет правильность пароля
	 *
	 * @param string $password
	 *
	 * @return bool  TRUE если пароль верен и FALSE в противном случае
	 *
	 * @since 2.16
	 */
	public function isPasswordValid($password)
	{
		return $this->password == self::passwordHash($password);
	}
	//-----------------------------------------------------------------------------
}