<?php
/**
 * Eresus Core
 *
 * @version 0.1.3
 *
 * HTTP Response
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
 * @since 0.1.3
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: HttpResponse.php 524 2010-06-05 12:53:45Z mk $
 */
//namespace Eresus\Core\WWW\HTTP;

/**
 * HTTP Response
 *
 * @package HTTP
 *
 * @since 0.1.3
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class HttpResponse
{

	/**
	 * HTTP headers
	 *
	 * @var HttpHeaders
	 */
	private $headers;

	/**
	 * Response body
	 *
	 * Response body must be a string or object with __toString method defined
	 *
	 * @var string|object
	 */
	private $body;

	/**
	 * Redirect to the given url
	 *
	 * @param string $url[optional]      The URL to redirect to
	 * @param array  $params[optional]   Associative array of query parameters (not implemented yet!)
	 * @param bool   $session[optional]  Whether to append session information (not implemented yet!)
	 * @param int    $status[optional]   Custom response status code
	 *
	 * @return bool  FALSE or exits on success with the specified redirection status code
	 *
	 * @author based on function by w999d
	 */
	public static function redirect($url = null, $params = null, $session = false, $status = null)
	{
		/* No headers can be sent before redirect */
		if (headers_sent())
			return false;

		$url = HTTP::buildURL($url);
		$httpMessage = HttpMessage::fromEnv(HttpMessage::TYPE_REQUEST);

		/* Choose HTTP status code */
		switch (true)
		{
			case $status !== null:
				$code = $status;
			break;

			case $httpMessage->getHttpVersion() == '1.0':
				$code = 302;
			break;

			default:
				$code = 303;
			break;
		}

		/* Choose HTTP status message */
		switch ($code)
		{
			case 302:
				$message = '302 Found';
			break;

			case 303:
				$message = '303 See Other';
			break;

			default:
				$message = '';
			break;
		}

		/* Sending headers */
		header('HTTP/' . $httpMessage->getHttpVersion() . ' ' . $message, true, $code);
		header('Location: ' . $url);

		/* Sending HTML page for agents which does not support Location header */
		header('Content-type: text/html; charset=UTF-8');

		$hUrl = htmlspecialchars($url);
		echo <<<PAGE
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
	<head>
		<meta http-equiv="Refresh" content="0; url='{$hUrl}'">
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
		<title>{$message}</title>
	</head>
	<body>
		<script>
			function doRedirect()
			{
				location.replace("{$hUrl}");
			}
			setTimeout("doRedirect()", 1000);
		</script>
		<p>Your browser does not support automatic redirection. Please follow <a href="{$hUrl}">this link</a>.</p>
	</body>
</html>
PAGE;

		// Stopping application
		exit($code);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param string|object $body
	 */
	function __construct($body = null)
	{
		if (!is_null($body)) $this->body = $body;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Magic property getter
	 * @param string $property
	 * @return mixed
	 */
	public function __get($property)
	{
		switch ($property) {

			case 'headers':
				if (!$this->headers) $this->headers = new HttpHeaders();
				return $this->headers;
			break;

		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set response body
	 * @param string|object $body
	 */
	public function setBody($body)
	{
		$this->body = $body;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Send reponse
	 */
	public function send()
	{
		if ($this->headers) $this->headers->send();
		echo $this->body;
	}
	//-----------------------------------------------------------------------------

}
//-----------------------------------------------------------------------------

