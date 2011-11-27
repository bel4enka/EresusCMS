<?php
/**
 * Eresus Core
 *
 * @version 0.1.3
 *
 * Kernel module
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
 * @package Core
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id$
 */

/**
 * Eresus Core version
 */
define('ERESUS_CORE_VERSION', '0.1.3');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Logging Functions
 *
 *   ...
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/**
 * Write message to log
 *
 * This function writes message to log. By default only messages with
 * LOG_ERR or higher priorities are logging. This can be changed by
 * defining constant with name ERESUS_LOG_LEVEL.
 *
 * Log file defined by PHP ini settings error_log and log_errors.
 *
 * @param string|array $sender              Sender name. Use __METHOD__,
 *                                           array(get_class($this), __METHOD__) or __FUNCTION__
 * @param int          $priority            Message priority. See LOG_XXX
 * @param string       $message             Message. Can contain substitutions (see sprintf)
 * @param mixed        $arg,... [optional]  Some variables
 */
function eresus_log($sender, $priority, $message)
{
	/*
	 * Because of LOG_XXX constants values order, we use ">" to check if message
	 * priority is lower than current log level
	 */
	$ERESUS_LOG_LEVEL = defined('ERESUS_LOG_LEVEL') ? ERESUS_LOG_LEVEL : LOG_ERR;

	if ($priority > $ERESUS_LOG_LEVEL)
		return;

	if (is_array($sender))
		$sender = implode('/', $sender);

	/* Substitute vars if any */
	if (@func_num_args() > 3) {
		$args = array();
		for($i = 3; $i < @func_num_args(); $i++) {
			$var = func_get_arg($i);
			if (is_object($var))
				$var = get_class($var);
			$args []= $var;
		}
		$message = vsprintf($message, $args);
	}

	/* Add sender */
	if (empty($sender))
		$sender = 'unknown';

	$message = $sender . ': ' . $message;

	/* Add priority info */
	switch ($priority) {
		case LOG_DEBUG:   $priorityName = 'debug';    break;
		case LOG_INFO:    $priorityName = 'info';     break;
		case LOG_NOTICE:  $priorityName = 'notice';   break;
		case LOG_WARNING: $priorityName = 'warning';  break;
		case LOG_ERR:     $priorityName = 'error';    break;
		case LOG_CRIT:    $priorityName = 'critical'; break;
		case LOG_ALERT:   $priorityName = 'ALERT';    break;
		case LOG_EMERG:   $priorityName = 'PANIC';    break;
		default: $priorityName = 'unknown';
	}
	$message = '[' . $priorityName . '] ' . $message;

	/* Log message */
	if (!error_log($message)) {

		if (!syslog($priority, $message)) {
			fputs(STDERR, "Can not log message!\n");
		}

	}

}
//-----------------------------------------------------------------------------



/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Exceptions
 *
 *   ...
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


/**
 * Eresus exception interface
 *
 * Eresus Core uses extended interface for exceptions, which provides:
 * - Detailed description for occured exception
 * - Method to get real exception class name (for wrapper exceptions)
 * - Own method to get trace as string (for wrapper exceptions)
 * - Implements PHP 5.3 "getPrevious"-like functional
 *
 * As soon as Eresus exceptions can be derived from a different
 * standard PHP exceptions they must all implement this interface.
 *
 * @package Core
 */
interface EresusExceptionInterface {

	/**
	 * Full exception description
	 *
	 * @return string
	 */
	public function getDescription();

	/**
	 * Get previous exception
	 *
	 * @return Exception
	 */
	public function getPreviousException();
}


/**
 * Exception thrown if an error which can only be found on runtime occurs
 *
 * @package Core
 */
class EresusRuntimeException extends RuntimeException implements EresusExceptionInterface {

	/**
	 * Previous exception
	 *
	 * @var Exception
	 */
	protected $previous;

	/**
	 * Full description of an exception
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Creates new exception object
	 *
	 * $message must be a short exception description wich can be safely
	 * showed to user. And $description can contain a full description
	 * wich will be logged.
	 *
	 * @param string    $description [optional]  Extended information
	 * @param string    $message	[optional]     Error message
	 * @param Exception $previous [optional]     Previous exception
	 */
	function __construct($description = null, $message = null, $previous = null)
	{
		if (is_null($description) || empty($description))
			$description = '(no description)';

		if (is_null($message))
			$message = preg_replace('/([a-z])([A-Z])/', '$1 $2', get_class($this));

		if (version_compare(PHP_VERSION, '5.3', '>=')) {

			parent::__construct($message, 0, $previous);

		} else {

			parent::__construct($message, 0);
			$this->previous = $previous;

		}

		$this->description = $description;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Returns value of the $description property
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get previous exception
	 *
	 * @return Exception
	 */
	public function getPreviousException()
	{
		if (version_compare(PHP_VERSION, '5.3', '>='))
			return parent::getPrevious();

		else
			return $this->previous;
	}
	//-----------------------------------------------------------------------------
}


/**
 * Exception thrown if a logic expression is invalid
 *
 * @package Core
 */
class EresusLogicException extends LogicException implements EresusExceptionInterface {

	/**
	 * Previous exception
	 *
	 * @var Exception
	 */
	protected $previous;

	/**
	 * Full description of an exception
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Creates new exception object
	 *
	 * $message must be a short exception description wich can be safely
	 * showed to user. And $description can contain a full description
	 * wich will be logged.
	 *
	 * @param string    $description [optional]  Extended information
	 * @param string    $message	[optional]     Error message
	 * @param Exception $previous [optional]     Previous exception
	 */
	function __construct($description = null, $message = null, $previous = null)
	{
		if (is_null($description) || empty($description))
			$description = '(no description)';

		if (is_null($message))
			$message = preg_replace('/([a-z])([A-Z])/', '$1 $2', get_class($this));

		if (version_compare(PHP_VERSION, '5.3', '>=')) {

			parent::__construct($message, 0, $previous);

		} else {

			parent::__construct($message, 0);
			$this->previous = $previous;

		}

		$this->description = $description;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Returns value of the $description property
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get previous exception
	 *
	 * @return Exception
	 */
	public function getPreviousException()
	{
		if (version_compare(PHP_VERSION, '5.3', '>='))
			return parent::getPrevious();

		else
			return $this->previous;
	}
	//-----------------------------------------------------------------------------
}



/**
 * Exception thrown if encountered value of unexpected type
 *
 * @see __construct for more info
 *
 * @package Core
 */
class EresusTypeException extends EresusLogicException {

	/**
	 * Creates new exception object
	 *
	 * @param mixed     $var [optional]           Variable with a type problem
	 * @param string    $expectedType [optional]  Expected type
	 * @param string    $description [optional]   Extended information
	 * @param Exception $previous [optional]      Previous exception
	 */
	function __construct()
	{
		if (func_num_args() > 0) {

			$var = func_get_arg(0);
			$expectedType = func_num_args() > 1 ? func_get_arg(1) : null;
			$description = func_num_args() > 2 ? func_get_arg(2) : null;
			$previous = func_num_args() > 3 ? func_get_arg(3) : null;

			$actualType = gettype($var);
			if (is_object($var))
				$actualType .= ' of class ' . get_class($var);

			if (is_null($expectedType))
				$message = 'Unexpected value type: ' . $actualType;
			else
				$message = 'Expecting ' .	$expectedType . ' but got "' . $actualType .'"';

			if ($description)
				$message .= ' ' . $description;

			parent::__construct($message, 'Type error', $previous);

		} else parent::__construct('Type error');
	}
	//-----------------------------------------------------------------------------
}

/**
 * Exception thrown if unexpected value encountered
 *
 * @package Core
 */
class EresusValueException extends EresusRuntimeException {

	/**
	 * Creates new exception object
	 *
	 * @param string    $valueName [optional]    Value name
	 * @param mixed     $value [optional]        Value
	 * @param string    $description [optional]  Extended information
	 * @param Exception $previous [optional]     Previous exception
	 */
	function __construct()
	{
		if (func_num_args() > 0) {

			$valueName = func_get_arg(0);
			$value = func_num_args() > 1 ? func_get_arg(1) : null;
			$description = func_num_args() > 2 ? func_get_arg(2) : null;
			$previous = func_num_args() > 3 ? func_get_arg(3) : null;

			if (is_null($value))
				$message = "Invalid value of \"$valueName\"";
			else
				$message = "\"$valueName\" has invalid value: $value";

			if ($description)
				$message .= ' ' . $description;

			parent::__construct($message, 'Invalid value', $previous);

		} else parent::__construct('Invalid value');
	}
	//-----------------------------------------------------------------------------
}


/**
 * Exception thrown if property not exists
 *
 * @package Core
 */
class EresusPropertyNotExistsException extends EresusLogicException {

	/**
	 * Creates new exception object
	 *
	 * @param string    $property [optional]     Property name
	 * @param string    $class [optional]        Class name
	 * @param string    $description [optional]  Extended information
	 * @param Exception $previous [optional]     Previous exception
	 */
	function __construct()
	{
		if (func_num_args() > 0) {

			$property = func_get_arg(0);
			$class = func_num_args() > 1 ? func_get_arg(1) : null;
			$description = func_num_args() > 2 ? func_get_arg(2) : null;
			$previous = func_num_args() > 3 ? func_get_arg(3) : null;

			if (is_null($class))
				$message = "Property \"$property\" does not exists";
			else
				$message = "Property \"$property\" does not exists in class \"$class\"";

			if ($description)
				$message .= ' ' . $description;

			parent::__construct($message, 'Property not exists', $previous);

		} else parent::__construct('Property not exists');
	}
	//-----------------------------------------------------------------------------
}


/**
 * Exception thrown if method not exists
 *
 * @package Core
 */
class EresusMethodNotExistsException extends EresusLogicException {

	/**
	 * Creates new exception object
	 *
	 * @param string    $method [optional]       Method name
	 * @param string    $class [optional]        Class name
	 * @param string    $description [optional]  Extended information
	 * @param Exception $previous [optional]     Previous exception
	 */
	function __construct()
	{
		if (func_num_args() > 0) {

			$method = func_get_arg(0);
			$class = func_num_args() > 1 ? func_get_arg(1) : null;
			$description = func_num_args() > 2 ? func_get_arg(2) : null;
			$previous = func_num_args() > 3 ? func_get_arg(3) : null;

			if (is_null($class))
				$message = "Method \"$method\" does not exists";
			else
				$message = "Method \"$method\" does not exists in class \"$class\"";

			if ($description)
				$message .= ' ' . $description;

			parent::__construct($message, 'Method not exists', $previous);

		} else parent::__construct('Method not exists');
	}
	//-----------------------------------------------------------------------------
}



/**
 * Class autoload table
 *
 * @package Core
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class EresusClassAutoloadTable {

	/**
	 * Filename
	 * @var string
	 */
	protected $filename;

	/**
	 * Table
	 * @var array
	 */
	protected $table;

	/**
	 * Constructor
	 *
	 * @param string $filename
	 */
	public function __construct($filename)
	{
		eresus_log(__METHOD__, LOG_DEBUG, $filename);
		if (substr($filename , -4) != '.php') {
			eresus_log(__METHOD__, LOG_DEBUG, 'Adding ".php" extension');
			$filename .= '.php';
		}
		$this->filename = $filename;
		eresus_log(__METHOD__, LOG_DEBUG, 'Table file: %s', $this->filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get table filename
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Try to load class
	 *
	 * @param string $className
	 * @return bool
	 */
	public function load($className)
	{
		if (!$this->filename)
			return false;

		if (!$this->table)
			$this->loadTable();

		if (!$this->table)
			return false;

		eresus_log(__METHOD__, LOG_DEBUG, 'Searching for %s in %s', $className, $this->filename);

		if (isset($this->table[$className])) {

			$filename = $this->table[$className];
			eresus_log(__METHOD__, LOG_DEBUG, 'Found record: %s => %s', $className, $filename);
			if (substr($filename, -4) != '.php')
				$filename .= '.php';

			try {

				Core::safeInclude($filename);

			} catch (EresusRuntimeException $e) {

				throw new EresusRuntimeException(
					'Can not load class "'.$className.'" from "'.$filename.'" (' . $e->getDescription() . ')',
					null, $e
				);

			}

		}

		$loaded = Core::classExists($className);
		$result = $loaded ? 'Success' : 'Failed';
		eresus_log(
			__METHOD__, LOG_DEBUG, '%s loading %s using table %s', $result, $className, $this->filename
		);

		return $loaded;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Load table from file
	 */
	protected function loadTable()
	{
		eresus_log(__METHOD__, LOG_DEBUG, 'Loading autoload table from %s', $this->filename);

		try {

			$this->table = Core::safeInclude($this->filename, true);

		} catch (EresusFsFileNotExistsException $e) {

			eresus_log(
				__METHOD__, LOG_ERR, 'Can\'t load table from "%s": %s', $this->filename, $e->getDescription()
			);
			$this->filename = false;

		}

		eresus_log(__METHOD__, LOG_DEBUG, $this->table ? 'success' : 'failed');
	}
	//-----------------------------------------------------------------------------

}

/**
 * Eresus class autoloader
 *
 * @package Core
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class EresusClassAutoloader {

	/**
	 * Tables
	 * @var array
	 */
	private static $tables = array();

	/**
	 * Add
	 * @param $filename
	 * @return unknown_type
	 */
	static public function add($filename)
	{
		self::$tables []= new EresusClassAutoloadTable($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Load class
	 * @param string $className
	 */
	static public function load($className)
	{
		static $_depth = 0;

		$_depth++;

		eresus_log(__METHOD__, LOG_DEBUG, '[%d] ("%s")', $_depth, $className);

		foreach (self::$tables as $table) {
			eresus_log(__METHOD__, LOG_DEBUG, '[%d] Trying "%s"', $_depth, $table->getFileName());
			$loaded = $table->load($className);
			$classExists = Core::classExists($className);
			eresus_log(
				__METHOD__, LOG_DEBUG,
				'[%d] Result for "%s": %b', $_depth, $table->getFileName(), $loaded
			);
			if ($loaded || $classExists)
				break;
		}

		$_depth--;
	}
	//-----------------------------------------------------------------------------
}


/**
 * Main Eresus Core class
 *
 * All methods of this class are static
 *
 * @package Core
 */
class Core {

	/**
	 * Indicates initialization state:
	 *  0 - Not inited
	 *  1 - Init in progress
	 *  2 - Init complete
	 *
	 * @var int  Initialization state
	 */
	static private $initState = 0;

	/**
	 * Internal registry
	 *
	 * @see getValue, setValue, unsetValue
	 *
	 * @var array
	 */
	static private $registry = array();

	/**
	 * __autoload handlers pool
	 *
	 * @var array
	 * @see autoload(), registerAutoloader()
	 */
	static private $autoloaders = array();

	/**
	 * Init Eresus Core
	 */
	static public function init()
	{
		/* Allow only one call of this method */
		if (self::$initState)
			return;

		/* Indicate that init in progress */
		self::$initState = 1;

		eresus_log(__METHOD__, LOG_DEBUG, '()');

		/**
		 * eZ Components
		 */
		$currentDir = dirname(__FILE__);
		set_include_path($currentDir . DIRECTORY_SEPARATOR . '3rdparty' .
			DIRECTORY_SEPARATOR . 'ezcomponents' . PATH_SEPARATOR . get_include_path());
		include_once 'Base/src/base.php';

		spl_autoload_register(array('Core', 'autoload'));

		/*
		 * If Eresus Core was NOT built with a "compile" option
		 */
		if ( ! ERESUS_CORE_COMPILED )
			EresusClassAutoloader::add('core.autoload');

		eresus_log(__METHOD__, LOG_DEBUG, 'done');

		/* Indicate that init complete */
		self::$initState = 2;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Checks if class or interface exists
	 *
	 * This method not triggering autoload
	 *
	 * @param string $name  Class or interface name
	 * @return bool
	 */
	static public function classExists($name)
	{
		return class_exists($name, false) || interface_exists($name, false);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Class autoloading handler
	 *
	 * @param string $className
	 *
	 * @internal
	 * @see autoloaders, registerAutoloader()
	 */
	static public function autoload($className)
	{
		static $_depth = 0;
		$_depth++;

		eresus_log(__METHOD__, LOG_DEBUG, '[%d] (%s)', $_depth, $className);

		if (self::classExists($className)) {
			eresus_log(__METHOD__, LOG_DEBUG, '[%d] Class %s exists', $_depth, $className);
			$_depth--;
			return;
		}

		/*
		 * Search in autoload table
		 */
		EresusClassAutoloader::load($className);

		eresus_log(__METHOD__, LOG_DEBUG, '[%d] Calling ezcBase::autoload()', $_depth);

		try {

			$_depth--;
			ezcBase::autoload($className);
			$_depth++;

		} catch (ezcBaseException $e) {

			throw new EresusRuntimeException(
				"eZ Components autoloader failed on '$className'",
				'Class not found',
				$e
			);

		}

		if (self::classExists($className)) {

			eresus_log(__METHOD__, LOG_DEBUG, '[%d] Class %s loaded', $_depth, $className);
			$_depth--;
			return;

		} else {

			eresus_log(
				__METHOD__, LOG_DEBUG,
				'[%d] ezcBase::autoload() can\'t load class "%s"', $_depth, $className
			);

		}

		for ($i = 0; $i < count(self::$autoloaders); $i++) {

			if (defined('ERESUS_LOG_LEVEL') && constant('ERESUS_LOG_LEVEL') == LOG_DEBUG) {

				switch (true) {

					case is_array(self::$autoloaders[$i]):
						$debug_className = is_object(self::$autoloaders[$i][0]) ?
							get_class(self::$autoloaders[$i][0]) :
							self::$autoloaders[$i][0];
						$debug_handlerAsString = $debug_className.'::'.self::$autoloaders[$i][1];
					break;

					case is_string(self::$autoloaders[$i]):
						$debug_handlerAsString = self::$autoloaders[$i];
					break;

					default:
						$debug_handlerAsString = gettype(self::$autoloaders[$i]);
					break;

				}

				eresus_log(__METHOD__, LOG_DEBUG, '[%d] Call "%s"', $_depth, $debug_handlerAsString);

			}

			call_user_func(self::$autoloaders[$i], $className);
			if (self::classExists($className))
				break;

		}

		eresus_log(
			__METHOD__, LOG_DEBUG, '[%d] %s', $_depth,
			self::classExists($className) ? 'success' : 'failed'
		);
		$_depth--;

	}
	//-----------------------------------------------------------------------------

	/**
	 * Set value in internal registry
	 *
	 * @param string $key    Value name
	 * @param mixed  $value  Value
	 *
	 * @see getValue, unsetValue
	 * @link http://martinfowler.com/eaaCatalog/registry.html Registry pattern
	 */
	static public function setValue($key, $value)
	{
		self::$registry[$key] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get value from internal registry
	 *
	 * @param string $key                 Value name
	 * @param mixed  $default [optional]  Optional default if value not set
	 * @return mixed  Value or $default or null
	 *
	 * @see setValue, unsetValue
	 */
	static public function getValue($key, $default = null)
	{
		if (isset(self::$registry[$key]))
			return self::$registry[$key];
		else
			return $default;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Unset value in internal registry
	 *
	 * @param string $key
	 *
	 * @see getValue, unsetValue
	 */
	static public function unsetValue($key)
	{
		unset(self::$registry[$key]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Register new autoload handler
	 *
	 * @param callback $autoloader
	 */
	static public function registerAutoloader($autoloader)
	{
		if (defined('ERESUS_LOG_LEVEL') && constant('ERESUS_LOG_LEVEL') == LOG_DEBUG) {

			switch(true) {
				case is_array($autoloader):
					$callback = is_object(reset($autoloader))
						? 'object('.get_class(current($autoloader)).')'
						: current($autoloader);
					$callback .= '::'.next($autoloader);
				break;
				default: $callback = $autoloader;
			}
			eresus_log(__METHOD__, LOG_DEBUG, 'registering handler "%s"', $callback);
		}

		array_unshift(self::$autoloaders, $autoloader);

	}
	//-----------------------------------------------------------------------------

	/**
	 * Perform safe include of a PHP-file
	 *
	 * Search order:
	 * 1. Relative to current directory
	 * 2. If path is absolute (starts with "/") then stop
	 * 3. Directories from include_path but only in open_basedir
	 * 4. Native include
	 *
	 * @param string $filename
	 * @param bool   $force     if "true" then "include" will be used instead of
	 *                          "include_once"
	 *
	 * @return mixed  Result of file inclusion
	 *
	 * @throws EresusFsFileNotExistsException, EresusRuntimeException
	 */
	static public function safeInclude($filename, $force = false)
	{
		eresus_log(__METHOD__, LOG_DEBUG, '("%s", %b)', $filename, $force);

		if (substr($filename, -4) != '.php')
			$filename .= '.php';

		$isAbsolutePath = substr($filename, 0, 1) == '/';

		/*
		 * Try relative to current working directory
		 */

		eresus_log(__METHOD__, LOG_DEBUG, 'Try relative to current directory...');

		if (file_exists($filename)) {

			eresus_log(__METHOD__, LOG_DEBUG, 'Found. Including.');

			if ($force)
				return include $filename;
			else
				return include_once $filename;
		}

		if ($isAbsolutePath) {

			eresus_log(__METHOD__, LOG_DEBUG, 'Absolute path "%s" not found', $filename);
			throw new EresusFsFileNotExistsException($filename);

		}

		/*
		 * Check include path
		 */
		$dirs = explode(PATH_SEPARATOR, get_include_path());

		$unsafe = array();

		/*
		 * At first, check directories in open_basedir list
		 */
		foreach($dirs as $dir) {

			if ($dir == '.')
				continue;

			eresus_log(__METHOD__, LOG_DEBUG, 'Probing "%s"', $dir . DIRECTORY_SEPARATOR . $filename);
			if (file_exists($dir . DIRECTORY_SEPARATOR . $filename)) {

				eresus_log(__METHOD__, LOG_DEBUG, 'File exists. Including.');

				if ($force)
					return include $filename;
				else
					return include_once $filename;
			}

		}

		/*
		 * Now try other dirs
		 */
		eresus_log(__METHOD__, LOG_DEBUG, 'Using native include');

		try {

			if ($force)
				return include $filename;
			else
				return include_once $filename;

		} catch (Exception $e) {

			/*
			 * If exception was thrown in this file then included file not found
			 * otherwise included file contains errors.
			 */
			if ($e->getFile() != __FILE__)
				throw $e;

			eresus_log(__METHOD__, LOG_DEBUG, 'Native include failed to locate file');
			throw new RuntimeException("File '$filename' not found in '".get_include_path()."'");

		}
	}
	//-----------------------------------------------------------------------------
}


/*****************************************************************************
 *
 *   Functions
 *
 *****************************************************************************/

/**
 * Get element with index $key from array $array
 *
 * If there is no element with such index function will return 'null'.
 *
 * @param array      $array
 * @param string|int $key
 * @return mixed
 */
function ecArrayValue($array, $key)
{
	return isset($array[$key]) ? $array[$key] : null;
}
//-----------------------------------------------------------------------------

/**
 * Recursive slash stripping
 *
 * @param string|array $source
 * @return string|array
 *
 * @author Ghost
 */
function ecStripSlashes($source)
{
	if (is_array($source)) {

		foreach ($source as $key => $value) {
			$source[$key] = ecStripSlashes($source[$key]);
		}

	} else {

		$source = stripslashes($source);
	}

	return $source;
}
//-----------------------------------------------------------------------------
