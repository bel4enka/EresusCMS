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
	 * Путь до корня сайта относительно домена
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Корневой URL
	 *
	 * @var string
	 */
	protected $rootURL;

	/**
	 * Создаёт запрос на основе окружения приложения
	 *
	 * @param Eresus_HTTP_Message $message  запрос HTTP
	 * @param string              $prefix   путь до корня сайта относительно домена
	 *
	 * @return Eresus_CMS_Request
	 *
	 * @since 2.16
	 */
	public function __construct(Eresus_HTTP_Message $message, $prefix)
	{
		$this->message = $message;
		$this->prefix = $prefix;
		$this->rootURL = Eresus_HTTP_Toolkit::buildURL($message->getRequestUrl(), array(),
			Eresus_HTTP_Toolkit::URL_STRIP_PATH) . '/' . $prefix;
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

	/**
	 * Проверяет, является ли запрос запросом POST
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	public function isGET()
	{
		return $this->getHttpMessage()->getRequestMethod() == 'GET';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет, является ли запрос запросом POST
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	public function isPOST()
	{
		return $this->getHttpMessage()->getRequestMethod() == 'POST';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает аргументы GET
	 *
	 * @return Eresus_HTTP_Request_Arguments
	 *
	 * @since 2.16
	 */
	public function getQuery()
	{
		return $this->getHttpMessage()->getQuery();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает аргументы POST
	 *
	 * @return Eresus_HTTP_Request_Arguments
	 *
	 * @since 2.16
	 */
	public function getPost()
	{
		return $this->getHttpMessage()->getPost();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает путь к текущей вертуальной директории относительно корня сайта
	 *
	 * Примеры:
	 *
	 * - Для "….org/" будет ""
	 * - Для "….org/dir/" будет "/dir"
	 * - Для "….org/dir/file.ext" будет "/dir"
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getBasePath()
	{
		$path = '/' . substr($this->message->getRequestUrl(), strlen($this->rootURL));
		if (substr($path, -1) == '/')
		{
			$path = substr($path, 0, -1);
		}
		else
		{
			$path = dirname($path);
			if ($path == '/')
			{
				$path = '';
			}
		}
		return $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает часть запроса, соответствующую пути от корня сайта
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getPath()
	{
		$path = substr($this->message->getRequestUrl(), strlen($this->rootURL));
		$path = dirname($path);
		return $path;
	}
	//-----------------------------------------------------------------------------
}
