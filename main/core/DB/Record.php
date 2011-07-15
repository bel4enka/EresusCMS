<?php
/**
 * ${product.title} ${product.version}
 *
 * Запись БД
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
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
 * @since 2.16
 */
class Eresus_DB_Record extends Doctrine_Record
{
	/**
	 * Аксессор-десериализатор
	 *
	 * Подробнее см. {@link serializeMutator}.
	 *
	 * @param bool   $load       ???
	 * @param string $fieldName  имя поля к которому применяется аксессор
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function unserializeAccessor($load = true, $fieldName = null)
	{
		$value = $this->_get($fieldName, false);
		if (!is_string($value) || strlen($value) == 0)
		{
			$this->_set($fieldName, array(), false);
		}
		else
		{
			$this->_set($fieldName, unserialize($value), false);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Мутатор-сериализатор
	 *
	 * Сериализует значение свойства модели.
	 *
	 * <b>Примеры</b>
	 *
	 * <code>
	 * class User extends Eresus_DB_Record
	 * {
	 * 	public function setTableDefinition()
	 * 	{
	 * 		$this->setTableName('users');
	 * 		$this->hasColumns(array(
	 * 			…
	 * 			'profile' => array(
	 * 				'type' => 'string',
	 * 			)
	 * 		));
	 * 		$this->hasAccessorMutator('profile', 'unserializeAccessor', 'serializeMutator');
	 * 	}
	 * }
	 * </code>
	 *
	 * В этом примере у модели определяется поле «profile», которое в БД представлено текстовым полем.
	 * Этому полю назначаются аксессор и мутатор, которые будут выполнять сериализацию при записи и
	 * десериализацию при чтении свойства. Таким образом, если в программе установить свойство
	 * «profile» следующим образом:
	 *
	 * <code>
	 * $user = new User();
	 * $user->profile = array('a' => 'b');
	 * $user->save();
	 * </code>
	 *
	 * …то в БД в поле «profile» попадёт значение «a:1:{s:1:"a";s:1:"b";}». И наоборот, при чтении
	 * этого свойства:
	 *
	 * <code>
	 * $user = Eresus_DB_ORM::getTable('User')->find(…);
	 * print_r($user->profile);
	 * </code>
	 *
	 * …выведет:
	 *
	 * <samp>
	 * Array(
	 * 	'a' => 'b'
	 * )
	 * </samp>
	 *
	 * @param mixed  $value     исходные данные
	 * @param bool   $load      ???
	 * @param string $fieldName имя поля к которому применяется мутатор
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function serializeMutator($value, $load = true, $fieldName = null)
	{
		$this->_set($fieldName, serialize($value), $load);
	}
	//-----------------------------------------------------------------------------

}
