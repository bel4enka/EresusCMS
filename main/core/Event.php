<?php
/**
 * ${product.title}
 *
 * Событие
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
 * Событие
 *
 * Каждое событие:
 * 1. имеет имя, напр. «coreInited», «authFailed», «pageRendered»;
 * 2. относится к определённому классу, напр. {@link Eresus_Event_Generic},
 *    {@link Eresus_Event_Security} и т. д.
 *
 * @package Eresus
 * @since 2.20
 */
abstract class Eresus_Event
{
	/**
	 * Имя события
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Отправитель события
	 *
	 * @var object
	 */
	private $sender;

	/**
	 * Конструктор события
	 *
	 * @param string $name    имя события
	 * @param object $sender  отправитель события
	 *
	 * @return Eresus_Event
	 *
	 * @since 2.20
	 */
	public function __construct($name, $sender)
	{
		if (!is_string($name))
		{
			throw new InvalidArgumentException('Argument 1 passed to ' . __METHOD__ .
				' must be a string, ' . gettype($name) . ' given');
		}
		if (!is_object($sender))
		{
			throw new InvalidArgumentException('Argument 2 passed to ' . __METHOD__ .
				' must be an object, ' . gettype($sender) . ' given');
		}
		$this->name = $name;
		$this->sender = $sender;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает имя события
	 *
	 * @return string
	 *
	 * @since 2.20
	 */
	public function getName()
	{
		return $this->name;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает отправителя
	 *
	 * @return object
	 *
	 * @since 2.20
	 */
	public function getSender()
	{
		return $this->sender;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отправляет событие обработчикам
	 *
	 * @return void
	 *
	 * @uses Eresus_Event_Manager::getInstance()
	 * @uses Eresus_Event_Manager::dispatch()
	 * @since 2.20
	 */
	public function dispatch()
	{
		Eresus_Event_Manager::getInstance()->dispatch($this);
	}
	//-----------------------------------------------------------------------------
}
