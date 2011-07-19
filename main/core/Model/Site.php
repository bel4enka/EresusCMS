<?php
/**
 * ${product.title}
 *
 * Сайт, обслуживаемый CMS
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
 * Модель сайта
 *
 * @property int	  $id           идентификатор
 * @property string $host         хост сайта
 * @property string $root         корневой URL сайта
 * @property string $name         краткое имя сайта
 * @property string $title        заголовок сайта
 * @property bool   $reverse      выводить элементы заголовка в обратном порядке
 * @property string $div          разделитель элементов заголовка
 * @property string $keywords     ключевые слова сайта
 * @property string $description  описание сайта
 *
 * @package	Eresus
 *
 * @since 2.16
 */
class Eresus_Model_Site extends Eresus_DB_Record
{
	/**
	 * @see Doctrine_Record_Abstract::setTableDefinition()
	 *
	 * @since 2.16
	 */
	public function setTableDefinition()
	{
		$this->setTableName('sites');

		$this->hasColumns(array(
			'id' => array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => true,
				'primary' => true,
				'autoincrement' => true,
			),
			'host' => array(
				'type' => 'string',
				'length' => 255,
				'default' => '',
				'notnull' => true,
			),
			'root' => array(
				'type' => 'string',
				'length' => 255,
				'default' => '',
				'notnull' => true,
			),
			'name' => array(
				'type' => 'string',
				'length' => 63,
				'default' => '',
				'notnull' => true,
			),
			'title' => array(
				'type' => 'string',
				'default' => '',
				'notnull' => true,
			),
			'reverse' => array(
				'type' => 'integer',
				'length' => 1,
				'default' => '1',
				'notnull' => true,
			),
			'div' => array(
				'type' => 'string',
				'length' => 31,
				'default' => ' &ndash; ',
				'notnull' => true,
			),
			'description' => array(
				'type' => 'string',
				'default' => '',
				'notnull' => true,
			),
			'keywords' => array(
				'type' => 'string',
				'default' => '',
				'notnull' => true,
			)
		));
	}
	//-----------------------------------------------------------------------------
}
