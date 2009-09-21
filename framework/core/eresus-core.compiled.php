<?php
/**
 * ${product.title}
 *
 * @version ${product.version}
 *
 * Compiled version
 *
 * @copyright 2007-${year}, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
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
 * @package Kernel
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id$
 */





abstract class EresusApplication {

	
	protected $fsRoot;

	
	abstract public function main();
	//-----------------------------------------------------------------------------

	
	function __construct()
	{

		$this->initFS();
		if (method_exists($this, 'autoload'))
			Core::registerAutoloader(array($this, 'autoload'));

	}
	//-----------------------------------------------------------------------------

	protected function initFS()
	{
		$this->fsRoot = $this->detectFsRoot();
	}
	//-----------------------------------------------------------------------------

	
	protected function detectFsRoot()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');
		$path = false;

		switch (true) {

			case PHP::isCLI():
				eresus_log(__METHOD__, LOG_DEBUG, 'Using global $argv variable');
				$path = reset($GLOBALS['argv']);
				$path = FS::canonicalForm($path);
				$path = FS::dirname($path);
				if (Core::testMode())
					$path = null;
				if (!$path) {
					eresus_log(__METHOD__, LOG_DEBUG, 'In addition using getcwd()');
					$path = getcwd();

					$path = FS::canonicalForm($path);
				}
			break;

			default:
				eresus_log(__METHOD__, LOG_DEBUG, 'Using getcwd()');
				$path = getcwd();
				$path = FS::canonicalForm($path);
				#TODO: The CGI SAPI supports CLI SAPI behaviour with a -C switch when run from the command line.

		}

		$path = FS::normalize($path, 'dir');
		eresus_log(__METHOD__, LOG_DEBUG, '"%s"', $path);

		return $path;
	}
	//-----------------------------------------------------------------------------

	
	public function getFsRoot($filename = null)
	{
		if ($filename)
			return FS::normalize($this->fsRoot . $filename);

		return $this->fsRoot;
	}
	//-----------------------------------------------------------------------------

}



class DBSettings implements ezcBaseConfigurationInitializer
{
	
	static private $dsn;

	
	static private $codepage = 'UTF8';

	
	public static function setDSN($dsn)
	{
		self::$dsn = $dsn;
	}
	//-----------------------------------------------------------------------------

	
	public static function setCodepage($codepage)
	{
		self::$codepage = $codepage;
	}
	//-----------------------------------------------------------------------------

	
	public static function configureObject($instance)
	{
		eresus_log(__METHOD__, LOG_DEBUG, '(%s)', $instance);

		switch ( $instance ) {

			case false:

				if (self::$dsn) {

					$dsn = self::$dsn;
					$codepage = self::$codepage;

				} else {

					eresus_log(__METHOD__, LOG_DEBUG, 'null');
					return null;

				}

				eresus_log(__METHOD__, LOG_DEBUG, 'Using DSN: %s', $dsn);
				$db = ezcDbFactory::create($dsn);

				#FIXME Next may be valid only for MySQL
				try {

					if ($codepage)
						$db->query('SET NAMES ' . $codepage);

				} catch (Exception $e) {}

				return $db;
		}
	}
	//-----------------------------------------------------------------------------
}




class DBRuntimeException extends EresusRuntimeException {
}




class DBQueryException extends DBRuntimeException {

	
	function __construct($query = null, $message = null, $previous = null)
	{
		if ($query instanceof ezcQuery) {

			$insider = new DBQueryInsider;
			$query->doBind($insider);
			$query = $insider->subst($query);
		}

		if (is_null($message))
			$message = 'Database query failed';

		if (!is_null($previous))
			$query = $previous->getMessage() . ': ' . $query;

		parent::__construct($query, $message, $previous);
	}
	//-----------------------------------------------------------------------------

}




class DB {

	
	private static $testInstance;

	
	public static function setTestInstance($instance)
	{
		self::$testInstance = $instance;
	}
	//-----------------------------------------------------------------------------

	
	public static function getInstance()
	{
		if (!self::$testInstance) return ezcDbInstance::get();

		return self::$testInstance;
	}
	//-----------------------------------------------------------------------------

	
	public static function createSelectQuery()
	{
		$db = self::getInstance();
		return $db->createSelectQuery();
	}
	//-----------------------------------------------------------------------------

	
	public static function createUpdateQuery()
	{
		$db = self::getInstance();
		return $db->createUpdateQuery();
	}
	//-----------------------------------------------------------------------------

	
	public static function createInsertQuery()
	{
		$db = self::getInstance();
		return $db->createInsertQuery();
	}
	//-----------------------------------------------------------------------------

	
	public static function createDeleteQuery()
	{
		$db = self::getInstance();
		return $db->createDeleteQuery();
	}
	//-----------------------------------------------------------------------------

	
	public static function execute($query)
	{
		$stmt = $query->prepare();

		try {

			eresus_log(__METHOD__, LOG_DEBUG, 'Query "%s"', $query);
			$result = $stmt->execute();

		} catch (Exception $e) {

			$exception = new DBQueryException($query, null, $e);
			if (Core::testMode()) throw new $exception;
			Core::handleException($exception);
			$result = false;

		}

		return $result;
	}
	//-----------------------------------------------------------------------------

	
	public static function fetch($query)
	{
		eresus_log(__METHOD__, LOG_DEBUG, 'Query "%s"', $query->getQuery());
		$stmt = $query->prepare();

		try {

			$stmt->execute();

		} catch (Exception $e) {

			throw new DBQueryException($query, null, $e);

		}

		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return $result;
	}
	//-----------------------------------------------------------------------------

	
	public static function fetchAll($query)
	{
		eresus_log(__METHOD__, LOG_DEBUG, 'Query "%s"', $query->getQuery());
		$stmt = $query->prepare();

		try {

			$stmt->execute();

		} catch (Exception $e) {

			throw new DBQueryException($query, null, $e);

		}

		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//-----------------------------------------------------------------------------

}




class DBQueryInsider extends PDOStatement {

	
	protected $values = array();

	
	public function bindValue($paramno, $param, $type = null)
	{
		switch ($type) {

			case PDO::PARAM_BOOL:
			break;

			case PDO::PARAM_INT:
			break;

			case PDO::PARAM_STR:
				$param = '"' . addslashes($param) . '"';
			break;
		}

		$this->values[$paramno] = $param;

	}
	//-----------------------------------------------------------------------------

	
	public function bindParam($paramno, &$param, $type = null, $maxlen = null, $driverdata = null)
	{
		$this->bindValue($paramno, $param, $type);
	}
	//-----------------------------------------------------------------------------

	
	public function subst($query)
	{
		foreach ($this->values as $key => $value)
			$query = preg_replace("/$key(\s|,|$)/", "$value$1", $query);

		return $query;
	}
	//-----------------------------------------------------------------------------

}

ezcBaseInit::setCallback('ezcInitDatabaseInstance', 'DBSettings');




include_once '3rdparty/dwoo/dwooAutoload.php';


class TemplateSettings {

	
	private static $gloablValues = array();

	
	public static function setGlobalValue($name, $value)
	{
		self::$gloablValues[$name] = $value;
	}
	//-----------------------------------------------------------------------------

	
	public static function getGlobalValue($name)
	{
		return ecArrayValue(self::$gloablValues, $name);
	}
	//-----------------------------------------------------------------------------

	
	public static function removeGlobalValue($name)
	{
		if (isset(self::$gloablValues[$name])) unset(self::$gloablValues[$name]);
	}
	//-----------------------------------------------------------------------------

	
	public static function getGlobalValues()
	{
		return self::$gloablValues;
	}
	//-----------------------------------------------------------------------------
}



class TemplateFile extends Dwoo_Template_File {

}



class Template
{
	
	protected $dwoo;

	
	protected $file;

	
	public function __construct($filename = null)
	{
		$compileDir = $this->detectCompileDir();
		$compileDir = FS::nativeForm($compileDir);
		$this->dwoo = new Dwoo($compileDir);

		if (Registry::exists('core.template.charset'))
			$this->dwoo->setCharset(Registry::get('core.template.charset'));

		if ($filename) $this->loadFile($filename);
	}
	//-----------------------------------------------------------------------------

	
	public function loadFile($filename)
	{
		$templateDir = $this->detectTemplateDir();
		$fileExtension = $this->detectFileExtension();
		$templateDir = FS::normalize($templateDir, 'dir');
		$template = $templateDir . $filename . $fileExtension;
		$template = FS::nativeForm($template);
		$this->file = new TemplateFile($template, null, $filename, $filename);
	}
	//-----------------------------------------------------------------------------

	
	function compile($data = null)
	{
		if ($data)
			$data = array_merge($data, TemplateSettings::getGlobalValues());
		else
			$data = TemplateSettings::getGlobalValues();

		return $this->dwoo->get($this->file, $data);
	}
	//-----------------------------------------------------------------------------

	
	protected function detectTemplateDir()
	{
		if (Registry::exists('core.template.templateDir')) {

			$compileDir = Registry::get('core.template.templateDir');

		} else $compileDir = '';

		return $compileDir;
	}
	//-----------------------------------------------------------------------------

	
	protected function detectFileExtension()
	{
		if (Registry::exists('core.template.fileExtension'))
			$fileExtension = Registry::get('core.template.fileExtension');

		else $fileExtension = '';

		return $fileExtension;
	}
	//-----------------------------------------------------------------------------

	
	protected function detectCompileDir()
	{
		if (Registry::exists('core.template.compileDir')) {

			$compileDir = Registry::get('core.template.compileDir');

		} else $compileDir = '';

		return $compileDir;
	}
	//-----------------------------------------------------------------------------
}




class Registry {

	
	private static $data = array();

	
	public static function exists($key)
	{
		return isset(self::$data[$key]);
	}
	//-----------------------------------------------------------------------------

	
	public static function put($key, $value)
	{
		if (self::exists($key)) throw new EresusLogicException("Key '$key' allready exists in registry.", 'Can not put value in registry');

		self::$data[$key] = $value;
	}
	//-----------------------------------------------------------------------------

	
	public static function set($key, $value)
	{
		self::$data[$key] = $value;
	}
	//-----------------------------------------------------------------------------

	
	public static function register($key, $value)
	{
		self::put($key, $value);
	}
	//-----------------------------------------------------------------------------

	
	public static function get($key)
	{
		if (!self::exists($key)) throw new EresusLogicException("Key '$key' not found in registry.", 'Value does not exists in registry');

		return self::$data[$key];
	}
	//-----------------------------------------------------------------------------

	
	public static function remove($key)
	{
		if (self::exists($key)) unset(self::$data[$key]);
	}
	//-----------------------------------------------------------------------------
}
//-----------------------------------------------------------------------------





class HttpHeaders {

	
	protected $headers = array();

	
	function __construct()
	{
	}
	//-----------------------------------------------------------------------------

	
	function add($header)
	{
		$this->headers []= $header;
	}
	//-----------------------------------------------------------------------------

	
	function getAll()
	{
		return $this->headers;
	}
	//-----------------------------------------------------------------------------

	
	public function send()
	{
		$headers = $this->getAll();

		foreach($headers as $header) $header->send();
	}
	//-----------------------------------------------------------------------------
}
//-----------------------------------------------------------------------------


class HttpHeader {

	
	protected $name;

	
	protected $value;

	
	public function __construct($name, $value)
	{
		$this->name = $name;
		$this->value = $value;
	}
	//-----------------------------------------------------------------------------

	
	public function __toString()
	{
		return $this->name . ': ' . $this->value;
	}
	//-----------------------------------------------------------------------------

	
	public function send()
	{
		if (!PHP::isCLI()) header($this);
	}
	//-----------------------------------------------------------------------------
}
//-----------------------------------------------------------------------------




class HTTP {

	
	static private $request;

	
	static public function setTestRequest($request)
	{
		if (Core::testMode())
			self::$request = $request;
	}
	//-----------------------------------------------------------------------------

	
	static public function request()
	{
		if (!self::$request) self::$request = new HttpRequest();
		return self::$request;
	}
	//-----------------------------------------------------------------------------

	
	static public function redirect($uri, $permanent = false)
	{
		eresus_log(__METHOD__, LOG_DEBUG, $uri);

		$header = 'Location: '.$uri;

		if ($permanent)
			header($header, true, 301);
		else
			header($header);

		if (!Core::testMode()) exit;
	}
	//-----------------------------------------------------------------------------

	
	static public function goback()
	{
		if (isset($_SERVER['HTTP_REFERER']))
			self::redirect($_SERVER['HTTP_REFERER']);
	}
	//-----------------------------------------------------------------------------
}
//-----------------------------------------------------------------------------


class HttpRequest {
	
	protected $request = array();

	
	protected $localRoot = '';

	
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
					$this->request['args'] = $_POST;
					foreach($_GET as $key => $value)
						if (!isset($this->request['args'][$key]))
							$this->request['args'][$key] = $value;

					if ($this->request['args'] && (get_magic_quotes_gpc() || Core::testModeGet('magic_quotes_gpc')))
						$this->request['args'] = ecStripSlashes($this->request['args']);

				}
			break;

			default:
				throw new EresusTypeException($source, 'HttpRequest, string or NULL');
		}
	}
	//-----------------------------------------------------------------------------

	
	public function toArray()
	{
		return $this->request;
	}
	//-----------------------------------------------------------------------------

	
	public function getScheme()
	{
		if (!isset($this->request['scheme'])) {

			$this->request['scheme'] = 'http';

		}

		$result = $this->request['scheme'];

		return $result;
	}
	//-----------------------------------------------------------------------------

	
	public function getMethod()
	{
		if (!isset($this->request['method'])) {

			$this->request['method'] = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

		}

		$result = $this->request['method'];

		return $result;
	}
	//-----------------------------------------------------------------------------

	
	public function setMethod($value)
	{
		$this->request['method'] = $value;
	}
	//-----------------------------------------------------------------------------

	
	public function getHost()
	{
		if (!isset($this->request['host'])) {

			$this->request['host'] = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : 'localhost';

		}

		$result = $this->request['host'];

		return $result;
	}
	//-----------------------------------------------------------------------------

	
	public function getPath()
	{
		if (!isset($this->request['path'])) {

			$this->request['path'] = '/';

		}

		$result = $this->request['path'];

		return $result;
	}
	//-----------------------------------------------------------------------------

	
	public function getDirectory()
	{
		if (!isset($this->request['directory'])) {

			
			$path = $this->getPath();
			$this->request['directory'] = substr($path, -1) == '/' ? $path : dirname($path) . '/';

		}

		$result = $this->request['directory'];

		return $result;
	}
	//-----------------------------------------------------------------------------

	
	public function getFile()
	{
		if (!isset($this->request['file'])) {

			$this->request['file'] = basename($this->getPath());

		}

		$result = $this->request['file'];

		return $result;
	}
	//-----------------------------------------------------------------------------

	
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

	
	public function getArgs()
	{
		$result = $this->request['args'];

		if (get_magic_quotes_gpc() || Core::testModeGet('magic_quotes_gpc'))
			$result = array_map('stripslashes', $result);

		return $result;
	}
	//-----------------------------------------------------------------------------

	
	public function arg($arg)
	{
		$result = isset($this->request['args'][$arg]) ? $this->request['args'][$arg] : null;

		return $result;
	}
	//-----------------------------------------------------------------------------

	
	public function getArg($arg)
	{
		return $this->arg($arg);
	}
	//-----------------------------------------------------------------------------

	
	public function setArg($arg, $value)
	{
		$this->request['args'][$arg] = $value;
	}
	//-----------------------------------------------------------------------------

	
	public function getLocal()
	{
		$result = $this->request['local'];

		if ($this->localRoot && strpos($result, $this->localRoot) === 0)
			$result = substr($result, strlen($this->localRoot));

		if ($result === false) return '';
		return $result;
	}
	//-----------------------------------------------------------------------------

	
	public function __toString()
	{
		$request = $this->getScheme().'://'.$this->getHost().$this->getPath();
		if ($this->getQuery()) $request .= '?' . $this->getQuery();
		return $request;
	}
	//-----------------------------------------------------------------------------

	
	public function setLocalRoot($root)
	{
		$this->localRoot = $root;
	}
	//-----------------------------------------------------------------------------

	
	public function getLocalRoot()
	{
		return $this->localRoot;
	}
	//-----------------------------------------------------------------------------

}
//-----------------------------------------------------------------------------






class HttpResponse {

	
	private $headers;

	
	private $body;

	
	function __construct($body = null)
	{
		if (!is_null($body)) $this->body = $body;
	}
	//-----------------------------------------------------------------------------

	
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

	
	public function setBody($body)
	{
		$this->body = $body;
	}
	//-----------------------------------------------------------------------------

	
	public function send()
	{
		if ($this->headers) $this->headers->send();
		echo $this->body;
	}
	//-----------------------------------------------------------------------------

}
//-----------------------------------------------------------------------------

