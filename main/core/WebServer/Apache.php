<?php
/**
 * ${product.title} ${product.version}
 *
 * Интерфейс к веб-серверу Apache
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
 * @package Eresus
 *
 * $Id$
 */

/**
 * Интерфейс к веб-серверу Apache
 *
 * Этот класс использует специфические функции, доступны только если PHP работает как модуль
 * Apache. Подробнее см. {@link http://php.net/manual/en/book.apache.php}.
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_WebServer_Apache extends Eresus_WebServer
{
	/**
	 * Кэш зоголовков
	 *
	 * @var array
	 */
	private $headers;

	/**
	 * Возвращает заголовки запроса
	 *
	 * @return array
	 *
	 * @since 2.16
	 * @see Eresus_WebServer::getRequestHeaders()
	 */
	public function getRequestHeaders()
	{
		if (!$this->headers)
		{
			$this->headers = apache_request_headers();
		}
		return $this->headers;
	}
	//-----------------------------------------------------------------------------
}
