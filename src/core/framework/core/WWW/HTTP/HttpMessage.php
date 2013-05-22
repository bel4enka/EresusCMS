<?php
/**
 * Eresus Core
 *
 * @version 0.1.3
 *
 * HTTP Message
 *
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license http://www.gnu.org/licenses/gpl.txt GPL License 3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package HTTP
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: HttpMessage.php 938 2010-06-11 12:38:12Z mk $
 */
//namespace Eresus\Core\WWW\HTTP;

/**
 * HTTP Message
 *
 * @package HTTP
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class HttpMessage
{
	/**
	 * Message has is of no specific type
	 * @var int
	 */
	const TYPE_NONE = 0;

	/**
	 * Message is a request style HTTP message
	 * @var int
	 */
	const TYPE_REQUEST = 1;

	/**
	 * Message is a response style HTTP message
	 * @var int
	 */
	const TYPE_RESPONSE = 2;

	/**
	 * Message type
	 * @var int
	 */
	private $type;

	/**
	 * Protocol version
	 * @var string
	 */
	private $httpVersion;

	/**
	 * Request method
	 * @var string
	 */
	private $requestMethod;

	/**
	 * Create an HttpMessage object from script environment
	 *
	 * Result always be NULL in CLI mode.
	 *
	 * @param int    $messageType          The message type. See HttpMessage type constants.
	 * @param string $className[optional]  A class extending HttpMessage
	 *
	 * @return HttpMessage|null  Returns an HttpMessage object on success or NULL on failure.
	 *
	 * @throws EresusRuntimeException if $className not exists
	 * @throws EresusValueException if $className not a descendent of HttpMessage
	 */
	static public function fromEnv($messageType, $className = 'HttpMessage')
	{
		if (PHP::isCLI())
			return null;

		/*
		 * Create message instance
		 */
		if (!class_exists($className, true))
			throw new EresusRuntimeException("Class \n$className\" not exists");

		$message = new $className();

		if (! ($message instanceof HttpMessage))
			throw new EresusValueException('className', $className,
				"\"$className\" must be a descendent of HttpMessage");

		// Message type
		$message->setType($messageType);

		/*
		 * Protocol version
		 */
		if (isset($_SERVER['SERVER_PROTOCOL']))
		{
			$dividerPosition = strpos($_SERVER['SERVER_PROTOCOL'], '/');

			if ($dividerPosition)
				$httpVersion = substr($_SERVER['SERVER_PROTOCOL'], $dividerPosition + 1);
			else
				$httpVersion = '1.0';
		}
		else
		{
			$httpVersion = '1.0';
		}

		$message->setHttpVersion($httpVersion);

		return $message;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set Message Type
	 *
	 * @param int $type  One of HttpMessage::TYPE_*
	 * @return void
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get Message Type
	 *
	 * @return int One of HttpMessage::TYPE_*
	 */
	public function getType()
	{
		return $this->type;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set the HTTP Protocol version of the Message
	 *
	 * @param string $version
	 * @return bool  Returns TRUE on success, or FALSE if supplied version is out of range (1.0/1.1)
	 */
	public function setHttpVersion($version)
	{
		// Version validation pattern
		$pattern = '~^1\.[01]$~';

		if (! preg_match($pattern, $version))
			return false;

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
	 *                         {@link http://tools.ietf.org/html/rfc2068#section-5.1.1 See RFC2068 section 5.1.1}
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
}