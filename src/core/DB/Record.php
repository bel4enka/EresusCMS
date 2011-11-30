<?php
/**
 * ${product.title}
 *
 * Запись БД
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
 * Запись БД
 *
 * Этот класс должен использоваться в качестве предка для всех моделей, используемых в Eresus CMS
 * вместо {@link Doctrine_Record}. Помимо предоставления новых возможностей, это позволяет CMS
 * добавлять функционал, контролирующий поведение моделей.
 *
 * <b>Примеры</b>
 *
 * <code>
 * class Catalog_Item extends Eresus_DB_Record
 * {
 * 	public function setTableDefinition()
 * 	{
 * 		$this->setTableName('users');
 * 		$this->hasColumns(array(
 * 			'id' => array(
 * 				'type' => 'integer',
 * 				'primary' => true,
 * 				'autoincrement' => true,
 * 			),
 *			'login' => array(
 *				'type' => 'string',
 *				'length' => 16,
 *			),
 *			...
 *		));
 *	}
 * }
 * </code>
 *
 * <b>Доступ к свойствам, как к элементам массива</b>
 *
 * Eresus_DB_Record (как потомок {@link Doctrine_Access}) поддерживает интерфейс
 * {@link http://php.net/ArrayAccess ArrayAccess}, что позволяет работать с моделью как с
 * ассоциативным массивом. Например, если у модели есть свойство «someProp», то следующие записи
 * будут эквивалентны:
 *
 * <code>
 * echo $model->someProp;
 * echo $model['someProp'];
 * </code>
 *
 * Также это можно использовать в шаблонах:
 *
 * <code>
 * {$model.someProp}
 * </code>
 *
 * @package Eresus
 * @since 2.17
 */
abstract class Eresus_DB_Record extends Doctrine_Record
{
}
