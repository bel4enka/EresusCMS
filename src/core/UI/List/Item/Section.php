<?php
/**
 * ${product.title}
 *
 * Элемент {@link Eresus_Eresus_UI_List списка} — раздел сайта
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
 * Элемент {@link Eresus_Eresus_UI_List списка} — раздел сайта
 *
 * @package Eresus
 */
class Eresus_UI_List_Item_Section extends Eresus_UI_List_Item_Object
{
	/**
	 * Имя поля «вкл/выкл»
	 *
	 * @var string
	 */
	protected $enabled = 'active';

	/**
	 * Конструктор элемента
	 *
	 * @param Eresus_Entity_Section $object  объект
	 *
	 * @since 2.17
	 */
	public function __construct(Eresus_Entity_Section $object)
	{
		parent::__construct($object);
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
		switch ($key)
		{
			case 'typeTitle':
				$types = Eresus_Kernel::sc()->plugins->getContentTypes();
				return isset($types[$this->type]) ? $types[$this->type]['title'] : false;
		}
		return parent::__get($key);
	}
	//-----------------------------------------------------------------------------
}