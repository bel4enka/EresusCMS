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
 * @property string $login
 * @property string $hash
 * @property int	  $active
 * @property string $lastVisit
 * @property int	  $lastLoginTime  время последней попытки входа в систему
 * @property int	  $loginErrors
 * @property int	  $access
 * @property string $name
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
		'login' => array(
			'type' => 'string',
			'length' => 16,
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'default' => '',
			'notnull' => true,
			'autoincrement' => false,
		),
		'hash' => array(
			'type' => 'string',
			'length' => 32,
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'default' => '',
			'notnull' => true,
			'autoincrement' => false,
		),
		'active' => array(
			'type' => 'integer',
			'length' => 1,
			'fixed' => false,
			'unsigned' => true,
			'primary' => false,
			'default' => '1',
			'notnull' => true,
			'autoincrement' => false,
		),
		'lastVisit' => array(
			'type' => 'timestamp',
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		),
		'lastLoginTime' => array(
			'type' => 'integer',
			'length' => 4,
			'fixed' => false,
			'unsigned' => true,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		),
		'loginErrors' => array(
			'type' => 'integer',
			'length' => 4,
			'fixed' => false,
			'unsigned' => true,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		),
		'access' => array(
			'type' => 'integer',
			'length' => 1,
			'fixed' => false,
			'unsigned' => true,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		),
		'name' => array(
			'type' => 'string',
			'length' => 64,
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		),
		'mail' => array(
			'type' => 'string',
			'length' => 64,
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		),
		'profile' => array(
			'type' => 'string',
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		)));

		$this->hasAccessorMutator('profile', 'profileAccessor', 'profileMutator');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Распаковщик профиля
	 *
	 * @param string $value
	 *
	 * @return array
	 *
	 * @since 2.16
	 */
	public function profileAccessor($value)
	{
		return unserialize($value);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Упаковщик профиля
	 *
	 * @param array $value
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function profileMutator($value)
	{
		return serialize($value);
	}
	//-----------------------------------------------------------------------------
}