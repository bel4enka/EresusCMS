<?php
/**
 * Eresus Core
 *
 * @version 0.1.0
 *
 * HTTP Module
 *
 * @copyright 2007-2009, Eresus Project, http://eresus.ru/
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
 * @package Core
 * @subpackage HTTP
 *
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: HTTP.php 277 2009-05-18 14:44:36Z mekras $
 */


/**
 * HTTP Toolkit
 *
 * @package Core
 * @subpackage HTTP
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class HTTP {

	/**
	 * HTTP request object
	 * @var HTTPRequest
	 */
	static private $request;

	/**
	 * Returns an instance of a HttpRequest class
	 *
	 * Object instancing only once
	 *
	 * @return HttpRequest
	 */
	static public function request()
	{
		if (!self::$request) self::$request = new HttpRequest();
		return self::$request;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Redirect UA to another URI and terminate program
	 *
	 * @param string $uri                  New URI
	 * @param bool   $permanent[optional]  Send '301 Moved permanently'
	 */
	static public function redirect($uri, $permanent = false)
	{
		elog(__METHOD__, LOG_DEBUG, $uri);

		$header = 'Location: '.$uri;

		if ($permanent) header($header, true, 301);
		else header($header);
		die;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Redirect UA to previous URI
	 *
	 * Method uses $_SERVER['HTTP_REFERER'] to determine previous URI. If this
	 * variable not set then method will do nothing. In last case developers can
	 * use next scheme:
	 *
	 * <code>
	 *  # ...Some actions...
	 *
	 * 	HTTP::goback();
	 *  HTTP::redirect('some_uri');
	 * </code>
	 *
	 * So if there is nowhere to go back user will be redirected to some fixed URI.
	 *
	 * @see redirect
	 */
	static public function goback()
	{
		if (isset($_SERVER['HTTP_REFERER']))
			self::redirect($_SERVER['HTTP_REFERER']);
	}
	//-----------------------------------------------------------------------------
}
//-----------------------------------------------------------------------------

/**
 * HTTP Request
 * @package Core
 * @subpackage HTTP
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class HttpRequest {
	/**
	 * Parsed HTTP request
	 * @var array
	 */
	protected $request = array();

	/**
	 * Local URI root
	 * @var string
	 * @see getLocal
	 */
	protected $localRoot = '';

	/**
	 * Constructor
	 *
	 * @param string|HTTPRequest $source [optional]  Source for request
	 *
	 * @throws EresusTypeException
	 */
	function __construct($source = null)
	{
		switch (true) {

			case is_object($source) && $source instanceof HttpRequest:
				$this->request = $source->toArray();
			break;

			case is_string($source):
				$this->request = @parse_url($source);
				$this->request['local'] = $this->getPath();
				if ($this->getQuery()) {
					$this->request['local'] .= '?' . $this->getQuery();
					parse_str($this->getQuery(), $this->request['args']);
				}
			break;

			case is_null($source):
				if (!PHP::isCLI()) {
					if (isset($_SERVER['REQUEST_URI'])) $this->request = @parse_url($_SERVER['REQUEST_URI']);
					$this->request['local'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
					$this->request['args'] = array_merge($_GET, $_POST);
				}
			break;

			default:
				throw new EresusTypeException($source, 'HttpRequest, string or NULL');
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Return current request as array
	 * @return array
	 * @internal
	 * @ignore
	 */
	public function toArray()
	{
		return $this->request;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get protocol scheme
	 * @return string
	 */
	public function getScheme()
	{
		if (!isset($this->request['scheme'])) {

			$this->request['scheme'] = 'http';

		}

		$result = $this->request['scheme'];

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get request method
	 * @return string
	 */
	public function getMethod()
	{
		if (!isset($this->request['method'])) {

			$this->request['method'] = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

		}

		$result = $this->request['method'];

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get host from request
	 * @return string
	 */
	public function getHost()
	{
		if (!isset($this->request['host'])) {

			$this->request['host'] = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : 'localhost';

		}

		$result = $this->request['host'];

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get path (directory and filename) from request
	 * @return string
	 */
	public function getPath()
	{
		if (!isset($this->request['path'])) {

			$this->request['path'] = '/';

		}

		$result = $this->request['path'];

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get directory name from request
	 * @return string
	 */
	public function getDirectory()
	{
		if (!isset($this->request['directory'])) {

			/*
			 * dirname can ommit last directory if path does not contain file name.
			 * To avoid this we can check trailing slash.
			 */
			$path = $this->getPath();
			$this->request['directory'] = substr($path, -1) == '/' ? $path : dirname($path) . '/';

		}

		$result = $this->request['directory'];

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get file name (without directory) from request
	 * @return string
	 */
	public function getFile()
	{
		if (!isset($this->request['file'])) {

			$this->request['file'] = basename($this->getPath());

		}

		$result = $this->request['file'];

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get query (after the question mark "?")
	 * @return string
	 */
	public function getQuery()
	{
		if (!isset($this->request['query'])) {

			$this->request['query'] = '';

		}

		$result = $this->request['query'];

		if (get_magic_quotes_gpc() || Core::testModeGet('magic_quotes_gpc'))
			$result = stripslashes($result);

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Return all values of GET or POST arguments
	 * @return array
	 */
	public function getArgs()
	{
		$result = $this->request['args'];

		if (get_magic_quotes_gpc() || Core::testModeGet('magic_quotes_gpc'))
			$result = array_map('stripslashes', $result);

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Return value of GET or POST argument
	 * @param string $arg  Atgument name
	 * @return mixed
	 */
	public function arg($arg)
	{
		$result = isset($this->request['args'][$arg]) ? $this->request['args'][$arg] : null;

		if ($result && (get_magic_quotes_gpc() || Core::testModeGet('magic_quotes_gpc')))
			$result = stripslashes($result);

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get local part of URI
	 * @return string
	 */
	public function getLocal()
	{
		$result = $this->request['local'];

		if ($this->localRoot && strpos($result, $this->localRoot) === 0)
			$result = substr($result, strlen($this->localRoot));

		if ($result === false) return '';
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Return full URI
	 * @return string
	 */
	public function __toString()
	{
		$request = $this->getScheme().'://'.$this->getHost().$this->getPath();
		if ($this->getQuery()) $request .= '?' . $this->getQuery();
		return $request;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set local root
	 * @param string $root
	 */
	public function setLocalRoot($root)
	{
		$this->localRoot = $root;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get local root
	 * @return string
	 */
	public function getLocalRoot()
	{
		return $this->localRoot;
	}
	//-----------------------------------------------------------------------------

}
//-----------------------------------------------------------------------------

