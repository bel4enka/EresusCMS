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
 */
class User extends Doctrine_Record
{
	/**
	 * (non-PHPdoc)
	 * @see Doctrine_Record_Abstract::setTableDefinition()
	 */
	public function setTableDefinition()
	{
		$this->setTableName('users');
		$this->hasColumn('id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'fixed' => false,
			'unsigned' => true,
			'primary' => true,
			'autoincrement' => true,
			));
		$this->hasColumn('login', 'string', 16, array(
			'type' => 'string',
			'length' => 16,
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'default' => '',
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('hash', 'string', 32, array(
			'type' => 'string',
			'length' => 32,
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'default' => '',
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('active', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'fixed' => false,
			'unsigned' => true,
			'primary' => false,
			'default' => '1',
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('lastVisit', 'timestamp', null, array(
			'type' => 'timestamp',
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
			));
		$this->hasColumn('lastLoginTime', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'fixed' => false,
			'unsigned' => true,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
			));
		$this->hasColumn('loginErrors', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'fixed' => false,
			'unsigned' => true,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
			));
		$this->hasColumn('access', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'fixed' => false,
			'unsigned' => true,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
			));
		$this->hasColumn('name', 'string', 64, array(
			'type' => 'string',
			'length' => 64,
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
			));
		$this->hasColumn('mail', 'string', 64, array(
			'type' => 'string',
			'length' => 64,
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
			));
		$this->hasColumn('profile', 'string', null, array(
			'type' => 'string',
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
			));
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Doctrine_Record::setUp()
	 */
	public function setUp()
	{
		parent::setUp();

	}
	//-----------------------------------------------------------------------------
}