<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Обрабатываемый запрос
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
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
 * @package Core
 *
 * $Id$
 */

/**
 * Обрабатываемый запрос
 *
 * @package Core
 * @since 2.16
 */
class Eresus_CMS_Request
{
	/**
	 * Сообщение HTTP
	 *
	 * @var Eresus_HTTP_Message
	 */
	protected $message;

	/**
	 * Создаёт запрос на основе окружения приложения
	 *
	 * @return Eresus_CMS_Request
	 *
	 * @since 2.16
	 */
	public function __construct()
	{
		$this->message = Eresus_HTTP_Message::fromEnv(Eresus_HTTP_Message::TYPE_REQUEST);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает объект Eresus_HTTP_Message
	 *
	 * @return Eresus_HTTP_Message
	 *
	 * @since 2.16
	 */
	public function getHttpMessage()
	{
		return $this->message;
	}
	//-----------------------------------------------------------------------------
}
