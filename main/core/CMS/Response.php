<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Ответ CMS
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
 * @package CMS
 *
 * $Id$
 */

/**
 * Ответ CMS
 *
 * @package CMS
 * @since 2.16
 */
class Eresus_CMS_Response
{
	/**
	 * Запрос обработан успешно
	 *
	 * @var int
	 */
	const OK = 200;

	/**
	 * Запрошенный ресурс не найден
	 *
	 * @var int
	 */
	const NOT_FOUND = 404;

	/**
	 * Код ответа
	 *
	 * @var int
	 */
	private $code;

	/**
	 * Тело ответа
	 *
	 * @var string
	 */
	private $body;

	/**
	 * Создаёт ответ
	 *
	 * @param string $body  тело ответа
	 * @param int    $code  код ответа HTTP
	 *
	 * @return Eresus_CMS_Response
	 *
	 * @since 2.16
	 */
	public function __construct($body, $code = 200)
	{
		$this->body = $body;
		$this->code = $code;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отправляет ответ пользователю
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function send()
	{
		$msg = new Eresus_HTTP_Response();
		$msg->setStatus($this->code);
		$msg->setBody($this->body);
		$msg->send();
	}
	//-----------------------------------------------------------------------------
}
