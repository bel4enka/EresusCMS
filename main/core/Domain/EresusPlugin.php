<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модель плагина
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
Doctrine_Manager::getInstance()->bindComponent('PluginInfo', 'doctrine');

/**
 * Класс информации о плагине
 *
 * @property string $name
 * @property int    $active
 * @property int    $content
 * @property string $settings
 * @property string $title
 * @property string $version
 * @property string $description
 *
 * @package EresusCMS
 */
class PluginInfo extends EresusActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see Doctrine_Record_Abstract::setTableDefinition()
	 */
	public function setTableDefinition()
	{
		$this->setTableName('plugins');
		$this->hasColumns(array(
			'name' => array(
				'type' => 'string',
				'length' => 32,
				'fixed' => false,
				'unsigned' => false,
				'primary' => true,
				'autoincrement' => false,
			),
			'active' => array(
				'type' => 'integer',
				'length' => 1,
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '1',
				'notnull' => true,
				'autoincrement' => false,
			),
			'content' => array(
				'type' => 'integer',
				'length' => 1,
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
			),
			'settings' => array(
				'type' => 'string',
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'notnull' => false,
				'autoincrement' => false,
			),
			'title' => array(
				'type' => 'string',
				'length' => 64,
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '',
				'notnull' => false,
				'autoincrement' => false,
			),
			'version' => array(
				'type' => 'string',
				'length' => 16,
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '',
				'notnull' => false,
				'autoincrement' => false,
			),
			'description' => array(
				'type' => 'string',
				'length' => 255,
				'fixed' => false,
				'unsigned' => false,
				'primary' => false,
				'default' => '',
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
		$this->hasAccessorMutator('settings', 'unserializeAccessor', 'serializeMutator');
	}
	//-----------------------------------------------------------------------------
}