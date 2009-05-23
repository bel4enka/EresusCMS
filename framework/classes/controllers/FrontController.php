<?php
/**
 * Eresus Framework
 *
 * @version 0.0.1
 *
 * Front controller
 *
 * @copyright 2007-2008, Eresus Project, http://eresus.ru/
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
 * @package Framework
 * @subpackage Controllers
 *
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: FrontController.php 125 2009-05-15 13:18:02Z mekras $
 */

/**
 * Front controller
 *
 * @package Framework
 * @subpackage Controllers
 *
 */
class FrontController extends MvcController {

	/**
	 * Incoming request
	 * @var HttpRequest
	 */
	protected $request;

	/**
	 * Response
	 * @var HttpResponse
	 */
	protected $response;

	/**
	 * Constructor
	 */
	public function __construct($request = null, $response = null)
	{
		$this->request = $request;
		$this->response = $response;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set request
	 */
	public function setRequest($request)
	{
		$this->request = $request;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set response
	 */
	public function setResponse($response)
	{
		$this->response = $response;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Execute controller
	 * @return HttpResponse
	 */
	public function execute()
	{
	}
	//-----------------------------------------------------------------------------
}
