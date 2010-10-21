<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Раздел сайта
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
Doctrine_Manager::getInstance()->bindComponent('Section', 'doctrine');

/**
 * Раздел сайта
 *
 * @property int	  $id
 * @property string $name
 * @property int	  $owner
 * @property string $title
 * @property string $caption
 * @property string $description
 * @property string $hint
 * @property string $keywords
 * @property int	  $position
 * @property bool   $active
 * @property int	  $access
 * @property bool   $visible
 * @property string $template
 * @property string $type
 * @property string $content
 * @property string $options
 * @property string $created
 * @property string $updated
 *
 * @package	EresusCMS
 */
class Section extends Doctrine_Record
{
	/**
	 * (non-PHPdoc)
	 * @see Doctrine_Record_Abstract::setTableDefinition()
	 */
	public function setTableDefinition()
	{
		$this->setTableName('pages');

		$this->hasColumn('id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'fixed' => false,
			'unsigned' => true,
			'primary' => true,
			'autoincrement' => true,
			));
		$this->hasColumn('name', 'string', 32, array(
			'type' => 'string',
			'length' => 32,
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'default' => '',
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('owner', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'fixed' => false,
			'unsigned' => true,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('title', 'string', null, array(
			'type' => 'string',
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('caption', 'string', 64, array(
			'type' => 'string',
			'length' => 64,
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'default' => '',
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('description', 'string', null, array(
			'type' => 'string',
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('hint', 'string', null, array(
			'type' => 'string',
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('keywords', 'string', null, array(
			'type' => 'string',
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('position', 'integer', 2, array(
			'type' => 'integer',
			'length' => 2,
			'fixed' => false,
			'unsigned' => true,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('active', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('access', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'fixed' => false,
			'unsigned' => true,
			'primary' => false,
			'default' => '5',
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('visible', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'fixed' => false,
			'unsigned' => true,
			'primary' => false,
			'default' => '1',
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('template', 'string', 64, array(
			'type' => 'string',
			'length' => 64,
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'default' => '',
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('type', 'string', 32, array(
			'type' => 'string',
			'length' => 32,
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'default' => 'default',
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('content', 'string', null, array(
			'type' => 'string',
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('options', 'string', null, array(
			'type' => 'string',
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('created', 'timestamp', null, array(
			'type' => 'timestamp',
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'default' => '0000-00-00 00:00:00',
			'notnull' => true,
			'autoincrement' => false,
			));
		$this->hasColumn('updated', 'timestamp', null, array(
			'type' => 'timestamp',
			'fixed' => false,
			'unsigned' => false,
			'primary' => false,
			'default' => '0000-00-00 00:00:00',
			'notnull' => true,
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