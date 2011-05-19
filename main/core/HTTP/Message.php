<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Сообщение HTTP
 *
 * @copyright 2004, Eresus Project, http://eresus.ru/
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
 * @package HTTP
 *
 * $Id: CMS.php 1625 2011-05-19 11:49:09Z mk $
 */

/**
 * Сообщение HTTP
 *
 * @package HTTP
 * @since 2.16
 */
class Eresus_HTTP_Message
{
	/**
	 * Тип сообщение не определён
	 * @var int
	 */
	const TYPE_NONE = 0;

	/**
	 * Это сообщение — запрос HTTP
	 * @var int
	 */
	const TYPE_REQUEST = 1;

	/**
	 * Это сообщение — ответ HTTP
	 * @var int
	 */
	const TYPE_RESPONSE = 2;

	/**
	 * Тип сообщения
	 * @var int
	 */
	private $type;

	/**
	 * Версия протокола
	 * @var string
	 */
	private $httpVersion;

	/**
	 * Метод запроса
	 * @var string
	 */
	private $requestMethod;

	/**
	 * Код ответа
	 * @var int
	 */
	private $responseCode;

	/**
	 * Сооздаёт сообщение из окружения приложения
	 *
	 * @param int    $messageType  тип сообщения
	 * @param string $className    имя класса создаваемого объекта
	 *
	 * @return Eresus_HTTP_Message|null  объект сообщения или null в случае неудачи
	 *
	 * @throws RuntimeException если класса $className не существует
	 * @throws InvalidArgumentException если $className не является потомком Eresus_HTTP_Message
	 */
	static public function fromEnv($messageType, $className = 'Eresus_HTTP_Message')
	{
		if (!class_exists($className, true))
		{
			throw new RuntimeException("Class \n$className\" not exists");
		}

		$message = new $className();

		if (! ($message instanceof self))
		{
			throw new InvalidArgumentException("\"$className\" must be a descendent of " . __CLASS__);
		}

		$message->setType($messageType);

		/*
		 * Определяем версию протокола
		 */
		if (isset($_SERVER['SERVER_PROTOCOL']) &&
			($dividerPosition = strpos($_SERVER['SERVER_PROTOCOL'], '/')))
		{
			$httpVersion = substr($_SERVER['SERVER_PROTOCOL'], $dividerPosition + 1);
		}
		else
		{
			$httpVersion = '1.0';
		}

		$message->setHttpVersion($httpVersion);

		/*
		 * Определяем метод запроса
		 */
		if (isset($_SERVER['REQUEST_METHOD']))
		{
			$message->setRequestMethod(strtoupper($_SERVER['REQUEST_METHOD']));
		}
		else
		{
			$message->setRequestMethod(self::METH_GET);
		}

		return $message;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает тип сообщения
	 *
	 * @param int $type  один из Eresus_HTTP_Message::TYPE_*
	 * @return void
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает тип сообщения
	 *
	 * @return int  один из Eresus_HTTP_Message::TYPE_*
	 */
	public function getType()
	{
		return $this->type;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает версию HTTP
	 *
	 * @param string $version
	 * @return bool  возвращает true в случае успеха, и false если передан неправильный номер версии
	 *               (не 1.0 или 1.1)
	 */
	public function setHttpVersion($version)
	{
		// Version validation pattern
		$pattern = '~^1\.[01]$~';

		if (! preg_match($pattern, $version))
		{
			return false;
		}

		$this->httpVersion = $version;
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get the HTTP Protocol Version of the Message
	 *
	 * @return string  Returns the HTTP protocol version as string
	 */
	public function getHttpVersion()
	{
		return $this->httpVersion;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set the Request Method of the HTTP Message
	 *
	 * @param string $method  The request method name.
	 *                         {@link http://tools.ietf.org/html/rfc2068#section-5.1.1
	 *                         See RFC2068 section 5.1.1}
	 *                         for list of acceptable methods
	 * @return bool  TRUE on success, or FALSE if the message is not of type
	 *                HttpMessage::TYPE_REQUEST or an invalid request method was supplied
	 */
	public function setRequestMethod($method)
	{
		if ($this->getType() !== self::TYPE_REQUEST)
			return false;

		$method = strtoupper($method);
		$REQUEST_METHODS = array('OPTIONS', 'GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'TRACE');

		if (!in_array($method, $REQUEST_METHODS))
			return false;

		$this->requestMethod = $method;
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get the Request Method of the Message
	 *
	 * @return string  Request method name on success, or FALSE if the message is not of type
	 *                  HttpMessage::TYPE_REQUEST.
	 */
	public function getRequestMethod()
	{
		if ($this->getType() !== self::TYPE_REQUEST)
			return false;

		return $this->requestMethod;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set response code
	 *
	 * @param int $code  Response code
	 * @return bool  TRUE on success, or FALSE if the message is not of type
	 *                HttpMessage::TYPE_RESPONSE or the response code is out of range (100-510).
	 * @since 0.2.0
	 */
	public function setResponseCode($code)
	{
		if ($this->getType() != self::TYPE_RESPONSE)
		{
			return false;
		}

		$isInt = is_int($code);
		$isValidString = is_string($code) && ctype_digit($code);

		if (!$isInt && !$isValidString)
		{
			throw new EresusTypeException($code, 'int', 'Ivalid HTTP response code value type.');
		}

		if ($code < 100 || $code > 510)
		{
			return false;
		}

		$this->responseCode = intval($code);
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Returns response code
	 *
	 * @return Returns the HTTP response code if the message is of type HttpMessage::TYPE_RESPONSE,
	 *          else FALSE.
	 * @since 0.2.0
	 */
	public function getResponseCode()
	{
		if ($this->getType() != self::TYPE_RESPONSE)
		{
			return false;
		}

		return $this->responseCode;
	}
	//-----------------------------------------------------------------------------

}