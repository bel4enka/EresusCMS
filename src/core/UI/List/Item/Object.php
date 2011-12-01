<?php
/**
 * ${product.title}
 *
 * Элемент {@link Eresus_Eresus_UI_List списка} — объект
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
 * Элемент {@link Eresus_Eresus_UI_List списка} — объект
 *
 * @package Eresus
 */
class Eresus_UI_List_Item_Object implements Eresus_UI_List_Item_Interface
{
	/**
	 * Объект
	 *
	 * @var object
	 */
	private $object;

	/**
	 * Имя свойства с ID
	 *
	 * @var string
	 */
	private $id = 'id';

	/**
	 * Имя поля «вкл/выкл»
	 *
	 * @var string
	 */
	private $enabled = 'enabled';

	/**
	 * Конструктор элемента
	 *
	 * @param object $object  объект
	 * @param array  $map     карта соответствия свойств
	 *
	 * @since 2.17
	 */
	public function __construct($object, array $map = array())
	{
		if (!is_object($object))
		{
			throw new InvalidArgumentException('First argument must be an object but ' .
				gettype($object) . ' given');
		}
		$this->object = $object;
		if (isset($map['id']))
		{
			$this->id = $map['id'];
		}
		if (isset($map['enabled']))
		{
			$this->enabled = $map['enabled'];
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Eresus_UI_List_Item_Interface::getId()
	 */
	public function getId()
	{
		return $this->object->{$this->id};
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Eresus_UI_List_Item_Interface::isEnabled()
	 */
	public function isEnabled()
	{
		return $this->object->{$this->enabled};
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает свойство объекта
	 *
	 * @param string $key
	 *
	 * @return mixed
	 *
	 * @since 2.17
	 */
	public function __get($key)
	{
		return $this->object->{$key};
	}
	//-----------------------------------------------------------------------------
}