<?php
/**
 * ${product.title}
 *
 * Менеджер событий
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
 * $Id: ACL.php 1748 2011-07-27 08:03:10Z mk $
 */

/**
 * Менеджер событий
 *
 * В задачи менеджера входит:
 * 1. регистрация обработчиков событий;
 * 2. отправка поступивших событий обработчикам.
 *
 * @package Eresus
 * @since 2.20
 */
class Eresus_Event_Manager
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_Event_Manager
	 */
	private static $instance = null;

	/**
	 * Список обработчиков
	 *
	 * @var array
	 */
	private $handlers = array();

	/**
	 * Карта привязок обработчиков к событиям
	 *
	 * @var array
	 */
	private $map = array();

	/**
	 * Возвращает экземпляр класса
	 *
	 * @return Eresus_Event_Manager
	 *
	 * @since 2.20
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Регистрирует обработчик событий
	 *
	 * @param callback $handler    обработчик
	 * @param string   $eventName  имя события
	 *
	 * @return int  идентификатор обработчика для последующего доступа к нему
	 *
	 * @since 2.20
	 */
	public function register($handler, $eventName)
	{
		if (!is_callable($handler))
		{
			throw new InvalidArgumentException('Argument 1 passed to ' . __METHOD__ .
				' must be a valid callback');
		}
		if (!is_string($eventName))
		{
			throw new InvalidArgumentException('Argument 2 passed to ' . __METHOD__ .
						' must be a string, ' . gettype($eventName) . ' given');
		}
		$this->handlers []= $handler;
		end($this->handlers);
		$index = key($this->handlers);
		if (!isset($this->map[$eventName]))
		{
			$this->map[$eventName] = array();
		}
		$this->map[$eventName] []= $index;
		return $index;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отправляет событие обработчикам
	 *
	 * @param Eresus_Event $event
	 *
	 * @return void
	 *
	 * @since 2.20
	 */
	public function dispatch(Eresus_Event $event)
	{
		if (isset($this->map[$event->getName()]))
		{
			foreach ($this->map[$event->getName()] as $index)
			{
				$handler = $this->handlers[$index];
				call_user_func($handler, $event);
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Скрываем конструктор
	 *
	 * @return void
	 *
	 * @since 2.20
	 */
	// @codeCoverageIgnoreStart
	private function __construct()
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

	/**
	 * Блокируем клонирование
	 *
	 * @return void
	 *
	 * @since 2.20
	 */
	// @codeCoverageIgnoreStart
	private function __clone()
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------
}
