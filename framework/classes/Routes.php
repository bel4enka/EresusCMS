<?php
/**
 * Eresus Framework
 *
 * @version 0.0.1
 *
 * Routes
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
 * @subpackage MVC
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: Routes.php 177 2009-05-31 14:08:30Z mekras $
 */

/**
 * Route
 *
 * <code>
 * 	$route = new Route('/', '*', 'SomeController');
 *  $route->process() // Will call SomeController::execute()
 *  $route->someMethod() // Will call SomeController::someMethod()
 * </code>
 *
 * @package Framework
 * @subpackage MVC
 *
 * @author mekras
 *
 */
class Route {

	/**
	 * URI pattern
	 * @var string
	 */
	protected $pattern;

	/**
	 * HTTP method
	 * @var string
	 */
	protected $httpMethod;

	/**
	 * Route handler
	 * @var string|array
	 */
	protected $handler;

	/**
	 * Route handler object
	 * @var object
	 */
	protected $instance;

	/**
	 * Request
	 * @var HttpRequest
	 */
	protected $request;

	/**
	 * Request
	 * @var HttpResponse
	 */
	protected $response;

	/**
	 * Constructor
	 *
	 * @param string       $pattern
	 * @param string       $httpMethod
	 * @param string|array $handler
	 */
	public function __construct($pattern, $httpMethod, $handler)
	{
		$this->pattern = $pattern;
		$this->httpMethod = $httpMethod;

		if (!is_string($handler) && !is_array($handler) && !is_object($handler))
			throw new EresusTypeException($handler, 'object, string or array(2)', 'Invalid type of route handler');

		$this->handler = $handler;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Call of handler method
	 *
	 * @param string $method
	 * @param array  $args
	 *
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		$instance = $this->getHandlerInstance();
		return call_user_func_array(array($instance, $method), $args);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set request object
	 * @param HttpRequest $request
	 */
	public function setRequest(HttpRequest $request)
	{
		$this->request = $request;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set response object
	 * @param HttpResponse|null $response
	 */
	public function setResponse($response)
	{
		if (!($response instanceof HttpResponse) && !is_null($response))
			throw new EresusTypeException($response, 'HttpResponse or null');

		$this->response = $response;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check request route match
	 *
	 * @param string $uri
	 * @param string $method [optional]
	 *
	 * @return bool
	 */
	public function match($uri, $method = 'GET')
	{
		if (!$this->internalMethodMatch($method)) return false;
		if (!$this->internalUriMatch($uri)) return false;
		$this->internalUpdateRequest();
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Handler call
	 *
	 * @param mixed args [optional] ...
	 *
	 * @return mixed
	 */
	public function process()
	{
		$instance = $this->getHandlerInstance();
		$method   = $this->getHandlerMethod();

		if ($instance instanceof FrontController) {
			$instance->setRequest($this->request);
			$instance->setResponse($this->response);
		}

		$args = func_get_args();

		return call_user_func_array(array($instance, $method), $args);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get handler instance (singleton)
	 * @return object
	 */
	protected function getHandlerInstance()
	{
		if ($this->instance) return $this->instance;

		switch (true) {

			case is_object($this->handler):
				$this->instance = $this->handler;
			break;

			case is_array($this->handler):
				$class = reset($this->handler);
				$this->instance = new $class();
			break;

			case is_string($this->handler):
				$class = $this->handler;
				$this->instance = new $class();
			break;

			default:
				throw new EresusTypeException($this->handler, 'object, array(2) or string');
		}

		return $this->instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get handler method name
	 * @return string
	 */
	protected function getHandlerMethod()
	{
		switch (true) {

			case is_array($this->handler):
				$method = $this->handler[1];
			break;

			case is_string($this->handler):
				if (!$this->instance) $this->getHandlerInstance();
				switch (true) {
					case $this->instance instanceof MvcController: $method = 'execute'; break;
					case $this->instance instanceof MvcView: $method = 'render'; break;
					default:
						throw new EresusRuntimeException('Can not determine default method for '.get_class($this->instance), 'Routing error');
				}
			break;

		}

		return $method;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Update request object
	 */
	protected function internalUpdateRequest()
	{
		$root = $this->request->getLocalRoot() . $this->pattern;
		$this->request->setLocalRoot($root);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check request method
	 *
	 * @param string $method
	 *
	 * @return bool
	 */
	protected function internalMethodMatch($method)
	{
		if ($this->httpMethod == '*') return true;
		return $this->httpMethod == $method;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check URI
	 *
	 * @param string $uri
	 *
	 * @return bool
	 */
	protected function internalUriMatch($uri)
	{
		if (strlen($this->pattern))
			return strpos($uri, $this->pattern) === 0;
		else
			return strlen($uri) == 0;
	}
	//-----------------------------------------------------------------------------

}


/**
 * Regular expression based route
 *
 * @package Framework
 * @subpackage MVC
 * @author mekras
 *
 */
class RegExpRoute extends Route {

	/**
	 * Method args
	 * @var array
	 */
	protected $args = array();

	/**
	 * URI root to strip from request
	 *
	 * @var string
	 */
	protected $uriRoot;

	/**
	 * Constructor
	 *
	 * @param string       $pattern
	 * @param string       $httpMethod
	 * @param string|array $handler
	 * @param string       $uriRoot [optional]
	 */
	public function __construct($pattern, $httpMethod, $handler, $uriRoot = null)
	{
		$this->pattern = $pattern;
		$this->httpMethod = $httpMethod;
		$this->uriRoot = $uriRoot;

		if (!is_string($handler) && !is_array($handler))
			throw new EresusTypeException($handler, 'string or array(2)', 'Invalid type of route handler');

		$this->handler = $handler;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check URI
	 *
	 * @param string $uri
	 *
	 * @return bool
	 */
	protected function internalUriMatch($uri)
	{
		if (! preg_match($this->pattern, $uri, $this->args)) return false;
		array_shift($this->args);
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Update request object
	 */
	protected function internalUpdateRequest()
	{
		if (is_null($this->uriRoot)) {

			$uri = $this->request->getLocal();
			$suffix = preg_replace($this->pattern, '', $uri);
			$path = substr($uri, 0, strlen($uri) - strlen($suffix));

		} else $path = $this->uriRoot;

		$root = $this->request->getLocalRoot() . $path;
		$this->request->setLocalRoot($root);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Handler call
	 *
	 * @param mixed $args [optional] ...
	 *
	 * @return mixed
	 */
	public function process()
	{
		$instance = $this->getHandlerInstance();
		$method   = $this->getHandlerMethod();

		if ($instance instanceof FrontController) {
			$instance->setRequest($this->request);
			$instance->setResponse($this->response);
		}

		$args = array_merge(func_get_args(), $this->args);

		return call_user_func_array(array($instance, $method), $args);
	}
	//-----------------------------------------------------------------------------
}


/**
 * Main routing class
 *
 * @package Framework
 * @subpackage MVC
 *
 * @author mekras
 *
 */
class Router {

	/**
	 * Request
	 * @var HttpRequest
	 */
	private $request;

	/**
	 * Response
	 * @var HttpResponse
	 */
	private $response;

	/**
	 * Routes table
	 * @var array
	 */
	private $routes = array();

	/**
	 * Default route
	 * @var Route
	 */
	private $default;

	/**
	 * Constructor
	 * @param HttpRequest  $request [optional]
	 * @param HttpResponse $response [optional]
	 */
	public function __construct(HttpRequest $request = null, HttpResponse $response = null)
	{
		$this->request = $request ? $request : HTTP::request();
		if ($response) $this->response = $response;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Add routes
	 * @param Route $route1
	 * ...
	 * @param Route $routeN
	 */
	public function add()
	{
		for ($i = 0; $i < func_num_args(); $i++) {
			$route = func_get_arg($i);
			$route->setRequest($this->request);
			$route->setResponse($this->response);
			$this->routes []= $route;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set default route
	 * @param Route $route
	 */
	public function setDefault($route)
	{
		$route->setRequest($this->request);
		$route->setResponse($this->response);
		$this->default = $route;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Find matching route
	 * @return null|Route
	 */
	public function find()
	{
		$uri = $this->request->getLocal();
		$method = $this->request->getMethod();

		foreach ($this->routes as $route)
			if ($route->match($uri, $method))
				return $route;

		return $this->default;
	}
	//-----------------------------------------------------------------------------
}
//-----------------------------------------------------------------------------

