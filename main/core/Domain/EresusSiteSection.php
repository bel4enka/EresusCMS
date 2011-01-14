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
class Section extends EresusActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see Doctrine_Record_Abstract::setTableDefinition()
	 */
	public function setTableDefinition()
	{
		$this->setTableName('pages');

		$this->hasColumns(array(
			'id' => array(
				'type' => 'integer',
				'length' => 4,
				'fixed' => false,
				'unsigned' => true,
				'primary' => true,
				'autoincrement' => true,
			),
			'name' => array(
				'type' => 'string',
				'length' => 32,
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '',
				'notnull' => true,
				'autoincrement' => false,
			),
			'owner' => array(
				'type' => 'integer',
				'length' => 4,
				'fixed' => false,
				'unsigned' => true,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
			),
			'title' => array(
				'type' => 'string',
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '',
				'notnull' => true,
				'autoincrement' => false,
			),
			'caption' => array(
				'type' => 'string',
				'length' => 64,
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '',
				'notnull' => true,
				'autoincrement' => false,
			),
			'description' => array(
				'type' => 'string',
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '',
				'notnull' => true,
				'autoincrement' => false,
			),
			'hint' => array(
				'type' => 'string',
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '',
				'notnull' => true,
				'autoincrement' => false,
			),
			'keywords' => array(
				'type' => 'string',
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '',
				'notnull' => true,
				'autoincrement' => false,
			),
			'position' => array(
				'type' => 'integer',
				'length' => 2,
				'fixed' => false,
				'unsigned' => true,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
			),
			'active' => array(
				'type' => 'integer',
				'length' => 1,
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
			),
			'access' => array(
				'type' => 'integer',
				'length' => 1,
				'fixed' => false,
				'unsigned' => true,
				'primary' => false,
				'default' => '5',
				'notnull' => true,
				'autoincrement' => false,
			),
			'visible' => array(
				'type' => 'integer',
				'length' => 1,
				'fixed' => false,
				'unsigned' => true,
				'primary' => false,
				'default' => '1',
				'notnull' => true,
				'autoincrement' => false,
			),
			'template' => array(
				'type' => 'string',
				'length' => 64,
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '',
				'notnull' => true,
				'autoincrement' => false,
			),
			'type' => array(
				'type' => 'string',
				'length' => 32,
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => 'default',
				'notnull' => true,
				'autoincrement' => false,
			),
			'content' => array(
				'type' => 'string',
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'notnull' => true,
				'autoincrement' => false,
			),
			'options' => array(
				'type' => 'string',
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '',
				'notnull' => true,
				'autoincrement' => false,
			),
			'created' => array(
				'type' => 'timestamp',
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => null,
				'notnull' => false,
				'autoincrement' => false,
			),
			'updated' => array(
				'type' => 'timestamp',
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => null,
				'notnull' => false,
				'autoincrement' => false,
			)
		));
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Doctrine_Record::setUp()
	 */
	public function setUp()
	{
		$this->hasAccessorMutator('options', 'unserializeAccessor', 'serializeMutator');
	}
	//-----------------------------------------------------------------------------
}