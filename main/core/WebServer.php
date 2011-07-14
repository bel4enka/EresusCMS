<?php
/**
 * ${product.title} ${product.version}
 *
 * Интерфейс к веб-серверу
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
 * Интерфейс к веб-серверу
 *
 * <b>История изменений</b>
 *
 * <i>2.16</i>
 *
 * - Переименован из WebServer в Eresus_WebServer
 *
 * @package Eresus
 * @since 2.15
 */
class Eresus_WebServer
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_WebServer
	 * @since 2.15
	 */
	private static $instance;

	/**
	 * Корневая директория веб-сервера (виртуального хоста)
	 *
	 * @var string
	 * @since 2.15
	 */
	private $documentRoot;

	/**
	 * Возвращает экземпляр-одиночку класса
	 *
	 * В зависимости от используемого веб-сервера может возвращать объекты разных классов — потомков
	 * Eresus_WebServer. Например, для сервера Apache будет возвращён {@link Eresus_WebServer_Apache}.
	 *
	 * Пример:
	 *
	 * <code>
	 * $server = Eresus_WebServer::getInstance();
	 * </code>
	 *
	 * @return Eresus_WebServer
	 *
	 * @since 2.15
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			switch (true)
			{
				case substr(PHP_SAPI, 0, 5) == 'apache':
					self::$instance = new Eresus_WebServer_Apache();
				break;

				default:
					self::$instance = new self();
				break;
			}
		}
		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает корневую директорию веб-сервера
	 *
	 * Папки в возвращаемом пути всегда разделены символов «/». Путь никогда не содержит «/» на
	 * конце.
	 *
	 * Пример:
	 *
	 * <code>
	 * $server = Eresus_WebServer::getInstance();
	 * $docRoot = $server->getDocumentRoot()
	 * </code>
	 *
	 * $docRoot может быть таким:
	 *
	 * <samp>/home/user_name/public_html</samp>
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getDocumentRoot()
	{
		return $this->documentRoot;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает заголовки запроса
	 *
	 * Возвращает ассоциативный массив, где ключами выступают имена заголовков.
	 *
	 * Этот возвращает только следующие заголовки:
	 * - Accept
	 * - Accept-Encoding
	 * - Accept-Language
	 * - Connection
	 * - Host
	 * - Referer
	 * - User-Agent
	 *
	 * Однако потомки Eresus_WebServer, специфичные для конкретного веб-сервера, могут возвращать
	 * дополнительные заголовки.
	 *
	 * @return array
	 *
	 * @since 2.16
	 * @see Eresus_WebServer_Apache::getRequestHeaders()
	 */
	public function getRequestHeaders()
	{
		$knownHeaders = array(
			'HTTP_ACCEPT' => 'Accept',
			'HTTP_ACCEPT_CHARSET' => 'Accept-Encoding',
			'HTTP_ACCEPT_LANGUAGE' => 'Accept-Language',
			'HTTP_CONNECTION' => 'Connection',
			'HTTP_HOST' => 'Host',
			'HTTP_REFERER' => 'Referer',
			'HTTP_USER_AGENT' => 'User-Agent'
		);
		$headers = array();
		foreach ($knownHeaders as $key => $header)
		{
			if (isset($_SERVER[$key]))
			{
				$headers[$header] = $_SERVER[$key];
			}
		}

		return $headers;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Конструктор
	 *
	 * @return Eresus_WebServer
	 *
	 * @since 2.15
	 */
	private function __construct()
	{
		$path = realpath($_SERVER['DOCUMENT_ROOT']);
		if (DIRECTORY_SEPARATOR != '/')
		{
			$path = str_replace($path, DIRECTORY_SEPARATOR, '/');
		}
		$this->documentRoot = $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Запрещаем клонирование
	 *
	 * @return void
	 *
	 * @since 2.15
	 */
	private function __clone()
	{
		// @codeCoverageIgnoreStart
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------
}
