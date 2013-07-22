<?php

/**
 * @deprecated с 3.01 используйте {@link Eresus_Kernel::log()}
 */
function eresus_log()
{
    call_user_func_array(array('Eresus_Kernel', 'log'), func_get_args());
}

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

        parent::__construct($message, 0, $previous);

		$this->description = $description;
	}

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
        return parent::getPrevious();
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

        parent::__construct($message, 0, $previous);

		$this->description = $description;
	}

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
        return parent::getPrevious();
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
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, $filename);
		if (substr($filename , -4) != '.php') {
            Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Adding ".php" extension');
			$filename .= '.php';
		}
		$this->filename = $filename;
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Table file: %s', $this->filename);
	}

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

        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Searching for %s in %s', $className, $this->filename);

		if (isset($this->table[$className])) {

			$filename = $this->table[$className];
            Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Found record: %s => %s', $className, $filename);
			if (substr($filename, -4) != '.php')
				$filename .= '.php';

			if (file_exists($filename))
            {
				include $filename;
			}
		}

		$loaded = Core::classExists($className);
		$result = $loaded ? 'Success' : 'Failed';
        Eresus_Kernel::log(
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
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Loading autoload table from %s', $this->filename);

		try
        {
			$this->table = include $this->filename;

		}
        catch (Exception $e)
        {
            Eresus_Kernel::log(
				__METHOD__, LOG_ERR, 'Can\'t load table from "%s": %s', $this->filename,
                $e->getMessage()
			);
			$this->filename = false;

		}

        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, $this->table ? 'success' : 'failed');
	}
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

        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '[%d] ("%s")', $_depth, $className);

		foreach (self::$tables as $table) {
            Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '[%d] Trying "%s"', $_depth, $table->getFileName());
			$loaded = $table->load($className);
			$classExists = Core::classExists($className);
            Eresus_Kernel::log(
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
		if (self::$initState)
			return;

		self::$initState = 1;

		/*
		 * eZ Components
		 */
		$currentDir = dirname(__FILE__);
		set_include_path($currentDir . '/3rdparty/ezcomponents' . PATH_SEPARATOR .
            get_include_path());
		include_once 'Base/src/base.php';

		spl_autoload_register(array('Core', 'autoload'));

        EresusClassAutoloader::add('core.autoload');

		self::$initState = 2;
	}

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

        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '[%d] (%s)', $_depth, $className);

		if (self::classExists($className)) {
            Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '[%d] Class %s exists', $_depth, $className);
			$_depth--;
			return;
		}

		/*
		 * Search in autoload table
		 */
		EresusClassAutoloader::load($className);

        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '[%d] Calling ezcBase::autoload()', $_depth);

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

            Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '[%d] Class %s loaded', $_depth, $className);
			$_depth--;
			return;

		} else {

            Eresus_Kernel::log(
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

                Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '[%d] Call "%s"', $_depth, $debug_handlerAsString);

			}

			call_user_func(self::$autoloaders[$i], $className);
			if (self::classExists($className))
				break;

		}

        Eresus_Kernel::log(
			__METHOD__, LOG_DEBUG, '[%d] %s', $_depth,
			self::classExists($className) ? 'success' : 'failed'
		);
		$_depth--;

	}

	/**
	 * Writes exception description to log
	 *
	 * @param Exception $e
	 * @param string    $msg [optional]  Message
	 */
	static public function logException($e, $msg = null)
	{
		if ($e instanceof EresusExceptionInterface) {

			$description = $e->getDescription();
			$previous = $e->getPreviousException();

		} else {

			$description = '(no description)';
			$previous = null;

		}

		$logMessage = sprintf(
			"%s in %s at %s\nMessage: %s\nDescription: %s\nBacktrace:\n%s\n",
			get_class($e),
			$e->getFile(),
			$e->getLine(),
			$e->getMessage(),
			$description,
			$trace = $e->getTraceAsString()
		);
        Eresus_Kernel::log('Core', LOG_ERR, $logMessage);

		if ($previous)
			self::logException($previous, 'Previous exception:');
	}

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
            Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'registering handler "%s"', $callback);
		}

		array_unshift(self::$autoloaders, $autoloader);

	}
}


/*****************************************************************************
 *
 *   Functions
 *
 *****************************************************************************/

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
