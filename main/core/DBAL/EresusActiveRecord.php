<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Активная запись
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
 * @package EresusCMS
 * @subpackage DBAL
 *
 * $Id$
 */

/**
 * Активная запись
 *
 * @package EresusCMS
 * @subpackage DBAL
 * @since 2.16
 */
class EresusActiveRecord extends Doctrine_Record
{

	/**
	 * Аксесор-десериализатор
	 *
	 * @param bool   $load
	 * @param string $fieldName имя поля к которому применяется аксессор
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
	 * @param mixed $value  исходные данные
	 * @param bool  $load
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
