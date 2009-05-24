<?php
/**
 * Eresus Core
 *
 * @version 0.1.0
 *
 * HTTP Response
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
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: HttpResponse.php 270 2009-05-15 08:48:26Z mekras $
 */


/**
 * HTTP Response
 * @package Core
 * @subpackage HTTP
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class HttpResponse {

	/**
	 * HTTP headers
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

