<?php
/**
 * Eresus Core
 *
 * @version 0.1.3
 *
 * Compiled version
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
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: compiled.head.php 480 2010-02-18 18:24:45Z mk $
 */

/*
 * Ensure that we using supported PHP version
 */

/**
 * Required PHP version
 */
if ( !defined('ERESUS_CORE_PHP_VERSION') )
	define('ERESUS_CORE_PHP_VERSION', '5.2.1');

/* Check PHP version */
if (PHP_VERSION < ERESUS_CORE_PHP_VERSION)
	die(
		'Eresus Core: Invalid PHP version '.PHP_VERSION.
		'. Needed '.ERESUS_CORE_PHP_VERSION." or later.\n"
	);

define('ERESUS_CORE_COMPILED', true);

/**
 * Eresus Core version
 */
define('ERESUS_CORE_VERSION', '0.1.3');

/**
 * Emergency memory buffer size in KiB
 *
 * @see EresusFatalErrorHandler
 */
define('ERESUS_MEMORY_OVERFLOW_BUFFER', 64);

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
			if (Core::testMode()) exit(-1);
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

		if (Core::testMode()) $message .= " ($description)";

		if (PHP::checkVersion('5.3')) {

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
		if (PHP::checkVersion('5.3'))
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

		if (Core::testMode()) $message .= " ($description)";

		if (PHP::checkVersion('5.3')) {

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
		if (PHP::checkVersion('5.3'))
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


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   PHP Functions
 *
 *   ...
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/**
 * Major and minor version numbers (N.N.x-xx)
 */
define('VERSION_ID', 0x01);

/**
 * Major version number (N.x.x-xx)
 */
define('VERSION_MAJOR', 0x02);

/**
 * Minor version number (x.N.x-xx)
 */
define('VERSION_MINOR', 0x03);

/**
 * Release number (x.x.N-xx)
 */
define('VERSION_RELEASE', 0x04);

/**
 * Extra information (x.x.x-NN)
 */
define('VERSION_EXTRA', 0x05);

/**
 * PHP information
 *
 * Part of functions was taken from a {@link http://limb-project.com/ Limb3 project}
 *
 * @package Core
 * @since 0.0.1
 */
class PHP {

	/**
	 * Plain PHP version
	 * @var string
	 */
	private static $phpVersion = PHP_VERSION;

	/**
	 * Parsed version cache
	 * @var string
	 */
	private static $version = null;

	/**
	 * Parsed open_basedir list
	 *
	 * @var array
	 */
	protected static $open_basedir;

	/**
	 * Substitute PHP version with specified value
	 *
	 * @param string $version
	 *
	 * @since 0.1.1
	 */
	public static function setVersion($version)
	{
		self::$phpVersion = is_null($version) ? PHP_VERSION : $version;
		self::$version = null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get PHP version
	 *
	 * @param int $part  Version part to return
	 * @return string
	 *
	 * @see VERSION_XXX constants
	 */
	public static function version($part = null)
	{
		if (is_null($part))
			return self::$phpVersion;

		if (is_null(self::$version)) {
			/* Parse PHP version only once */
			preg_match('/^(\d+)\.(\d+)\.(\d+).?(.+)?/', self::$phpVersion, $v);
			self::$version[VERSION_ID]      = $v[1] . '.' . $v[2];
			self::$version[VERSION_MAJOR]   = $v[1];
			self::$version[VERSION_MINOR]   = $v[2];
			self::$version[VERSION_RELEASE] = isset($v[3]) ? $v[3] : 0;
			self::$version[VERSION_EXTRA]   = isset($v[4]) ? $v[4] : 0;
		}
		$result = self::$version[$part];

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if current PHP version is equal or higher then $version
	 *
	 * @param string $version
	 * @return bool
	 *
	 * @since 0.1.1
	 */
	static function checkVersion($version)
	{
		return version_compare(self::$phpVersion, $version, '>=');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get php.ini option
	 *
	 * @param string $key  Option name
	 * @return mixed
	 *
	 * @since 0.1.1
	 */
	public static function iniGet($key)
	{
		return Core::testModeIsSet("ini.$key") ? Core::testModeGet("ini.$key") : ini_get($key);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check for CLI SAPI
	 *
	 * @return bool
	 *
	 * @since 0.0.1
	 */
	static function isCLI()
	{
		if (Core::testModeIsSet('PHP::isCLI')) return Core::testModeGet('PHP::isCLI');
		return PHP_SAPI == 'cli';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check for CGI SAPI
	 *
	 * @return bool
	 *
	 * @since 0.0.1
	 */
	static function isCGI()
	{
		return strncasecmp(PHP_SAPI, 'CGI', 3) == 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check for web server SAPI
	 *
	 * @return bool
	 *
	 * @since 0.0.1
	 */
	static function isModule()
	{
		return !self::isCGI() && isset($_SERVER['GATEWAY_INTERFACE']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if path is in open_basedir list
	 *
	 * If open_basedir option not set method returns 'true' for any path.
	 *
	 * @param string $path  Path to check
	 * @return bool
	 *
	 * @since 0.1.1
	 */
	public static function inOpenBaseDir($path)
	{
		if (! self::iniGet('open_basedir'))
			return true;

		if (! self::$open_basedir)
			self::$open_basedir = explode(PATH_SEPARATOR, self::iniGet('open_basedir'));

		if ($path == '.')
			$path = getcwd();

		foreach (self::$open_basedir as $dir)
			if (substr($path, 0, strlen($dir)) == $dir)
				return true;

		return false;
	}
	//-----------------------------------------------------------------------------
}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   System Functions
 *
 *   ...
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/**
 * System information
 *
 * Part of functions was taken from a Limb3 project -
 * {@link http://limb-project.com/}
 *
 * @package Core
 * @since 0.0.1
 */
class System {

	/**
	 * Init
	 *
	 * @since 0.0.1
	 * @todo UnitTest
	 */
	public static function init()
	{
		@$timezone = date_default_timezone_get();
		date_default_timezone_set($timezone);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if system is a UNIX-like
	 *
	 * @return bool
	 *
	 * @since 0.0.1
	 * @todo UnitTest for OSes  other then UNIX
	 */
	static function isUnixLike()
	{
		return DIRECTORY_SEPARATOR == '/';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if system is a Microsoft Windows
	 *
	 * @return bool
	 *
	 * @since 0.0.1
	 * @todo UnitTest for OSes  other then UNIX
	 */
	static function isWindows()
	{
		if (Core::testModeGet('System::isWindows')) return true;
		return strncasecmp(PHP_OS, 'WIN', 3) == 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if system is a MacOS
	 *
	 * @return bool
	 *
	 * @since 0.0.1
	 * @todo UnitTest for OSes  other then UNIX
	 */
	static function isMac()
	{
		return strncasecmp(PHP_OS, 'MAC', 3) == 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get system time zone
	 *
	 * @return string
	 *
	 * @since 0.0.1
	 * @link http://ru2.php.net/timezones List of timezones
	 *
	 * @todo UnitTest
	 */
	public static function getTimezone()
	{
		return date_default_timezone_get();
	}
	//-----------------------------------------------------------------------------
}



/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Filesystem Functions
 *
 *   ...
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


/**
 * Exception thrown in case of FS runtime error
 *
 * @package Core
 */
class EresusFsRuntimeException extends EresusRuntimeException {}


/**
 * Exception thrown if path not exists
 *
 * @package Core
 */
class EresusFsPathNotExistsException extends EresusFsRuntimeException {

	/**
	 * Creates new exception object
	 *
	 * @param string    $path [optional]         Path
	 * @param string    $description [optional]  Extended information
	 * @param Exception $previous [optional]     Previous exception
	 */
	function __construct()
	{
		if (func_num_args() > 0) {

			$path = func_get_arg(0);
			$description = func_num_args() > 1 ? func_get_arg(1) : null;
			$previous = func_num_args() > 2 ? func_get_arg(2) : null;

			$message = "Path \"$path\" does not exists";

			if ($description)
				$message .= ' ' . $description;

			parent::__construct($message, 'Path not exists', $previous);

		} else parent::__construct('Path not exists');
	}
	//-----------------------------------------------------------------------------
}



/**
 * Exception thrown if file not exists
 *
 * @package Core
 */
class EresusFsFileNotExistsException extends EresusFsPathNotExistsException {

	/**
	 * Creates new exception object
	 *
	 * @param string    $filename [optional]     Filename
	 * @param string    $description [optional]  Extended information
	 * @param Exception $previous [optional]     Previous exception
	 */
	function __construct()
	{
		if (func_num_args() > 0) {

			$filename = func_get_arg(0);
			$description = func_num_args() > 1 ? func_get_arg(1) : null;
			$previous = func_num_args() > 2 ? func_get_arg(2) : null;

			$message = "File \"$filename\" does not exists";

			if ($description)
				$message .= ' ' . $description;

			EresusFsRuntimeException::__construct($message, 'File not exists', $previous);

		} else EresusFsRuntimeException::__construct('File not exists');
	}
	//-----------------------------------------------------------------------------
}



/**
 * Filesystem abstraction layer
 *
 * This class provides static methods for system independent file operations.
 * Special driver classes are used for particular file systems.
 *
 * The most important goal of FS is uniform file names for UNIX and Windows
 * systems.
 *
 * @package Core
 */
class FS {

	/**
	 * Filesystem driver
	 * @var GenericFS
	 */
	static private $driver;

	/**
	 * Init FS module
	 *
	 * Load FS driver for current system
	 */
	static public function init($driver = null)
	{
		eresus_log(__METHOD__, LOG_DEBUG, '(%s)', $driver);
		self::$driver = null;

		/* User defined driver */
		if ($driver) {

			if ($driver instanceof GenericFS)
				self::$driver = $driver;
			else
				eresus_log(__METHOD__, LOG_ERR, 'Invalid FS driver: '.gettype($driver));

		}

		/* Autodetect */
		if (is_null(self::$driver)) {

			eresus_log(__METHOD__, LOG_DEBUG, 'Autodetecting file system...');

			if (System::isWindows()) {

				self::$driver = new WindowsFS();

			}

		}

		/* Generic driver */
		if (is_null(self::$driver))
			self::$driver = new GenericFS();

		eresus_log(__METHOD__, LOG_DEBUG, 'Using FS driver: %s', self::$driver);
		eresus_log(__METHOD__, LOG_DEBUG, 'Current directory: %s', self::canonicalForm(getcwd()));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get current FS driver
	 *
	 * @return GenericFS|null
	 */
	public static function driver()
	{
		return self::$driver;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Normalize file name
	 *
	 * In terms of FS, normal form of file name is:
	 *   - Unix-like directory separator (/)
	 *   - Absence of substitution symbols ('../', './')
	 *
	 * @param string $filename
	 * @return string
	 */
	static public function normalize($filename)
	{
		return self::$driver->normalize($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Convert canonical (UNIX) filename to filesystem native form
	 *
	 * @param string $filename
	 * @return string
	 *
	 * @see FS::canonicalForm()
	 */
	static public function nativeForm($filename)
	{
		return self::$driver->nativeForm($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Convert filename from filesystem native form to canonical (UNIX)
	 *
	 * @param string $filename
	 * @return string
	 *
	 * @see FS::nativeForm()
	 */
	static public function canonicalForm($filename)
	{
		return self::$driver->canonicalForm($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Checks if file exists in file system
	 *
	 * @param string $filename
	 * @return bool
	 */
	static public function exists($filename)
	{
		return self::$driver->exists($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get normalized directory name of a given filename
	 *
	 * @param string $filename
	 * @return string
	 */
	static public function dirname($filename)
	{
		return self::$driver->dirname($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Tells whether the filename is a regular file
	 *
	 * @param string $filename  Path to the file
	 * @return bool  Returns true if the filename exists and is a regular file
	 */
	static public function isFile($filename)
	{
		return self::$driver->isFile($filename);
	}
	//-----------------------------------------------------------------------------


	/**
	 * Tells whether the filename is a directory
	 *
	 * @param string $filename  Path to the file
	 * @return bool  Returns true if the filename exists and is a directory
	 */
	static public function isDir($filename)
	{
		return self::$driver->isDir($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Tells whether the filename is a symbolic link
	 *
	 * @param string $filename  Path to the file
	 * @return bool  Returns true if the filename exists and is a symbolic link
	 */
	static public function isLink($filename)
	{
		return self::$driver->isLink($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Tells whether the filename is readable
	 *
	 * @param string $filename  Path to the file
	 * @return bool  Returns true if the file or directory specified by filename
	 *               exists and is readable
	 */
	static public function isReadable($filename)
	{
		return self::$driver->isReadable($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Tells whether the filename is writable
	 *
	 * @param string $filename  Path to the file
	 * @return bool  Returns true if the file or directory specified by filename
	 *               exists and is writable
	 */
	static public function isWritable($filename)
	{
		return self::$driver->isWritable($filename);
	}
	//-----------------------------------------------------------------------------

}


/**
 * Generic file system class
 *
 * @package Core
 */
class GenericFS {

	/**
	 * Normalize file name
	 *
	 * Function converts given filename to the normal UNIX form:
	 *
	 * /some/path/filename
	 *
	 * 1. Adds slash at the end of directory name (see $type)
	 *
	 * @param string $filename         File name to normalize
	 * @return string
	 */
	public function normalize($filename)
	{

		$filename = $this->expandParentLinks($filename);

		$filename = $this->tidy($filename);

		return $filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Correct some errors
	 *
	 * 1. Replace multiple serial directory separators with one
	 * 2. Replace "/./" with "/"
	 * 3. Replace "/../" by stripping parent directory
	 * 4. Trim last "/"
	 *
	 * @param string $filename
	 * @return string
	 */
	protected function tidy($filename)
	{
		$filename = preg_replace('~/{2,}~', '/', $filename);
		$filename = str_replace('/./', '/', $filename);
		$filename = preg_replace('~^./~', '', $filename);
		if (substr($filename, -1) == '/')
			$filename = substr($filename, 0, -1);

		return $filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Expand links to parent directory ('..')
	 *
	 * @param string $filename
	 * @return string
	 */
	protected function expandParentLinks($filename)
	{
		if (strpos($filename, '..') === false)
			return $filename;

		$path = $filename;

		if ($path) {

			$parts = explode('/', $path);

			for ($i = 0; $i < count($parts); $i++) {

				if ($parts[$i] == '..') {
					if ($i > 1) {
						array_splice($parts, $i-1, 2);
						$i -= 2;
					} else {
						array_splice($parts, $i, 1);
						$i -= 1;
					}
				}

			}

			$path = implode('/', $parts);

		}

		$filename = $path;

		return $filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Convert canonical (UNIX) filename to filesystem native form
	 *
	 * @param string $filename
	 * @return string
	 *
	 * @see GenericFS::canonicalForm()
	 */
	public function nativeForm($filename)
	{
		return $filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Convert filename from filesystem native form to canonical (UNIX)
	 *
	 * @param string $filename
	 * @return string
	 *
	 * @see GenericFS::nativeForm()
	 */
	public function canonicalForm($filename)
	{
		return $filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get normalized file directory name
	 * @param string $filename
	 * @return string
	 */
	public function dirname($filename)
	{
		$path = $filename;

		$lastDirSep = strrpos($path, '/');
		if ($lastDirSep !== false)
			$path = substr($path, 0, $lastDirSep);

		if ($path === '')
			$path = '.';

		$path = $this->normalize($path);

		return $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Checks if file exists in file system
	 * @param string $filename
	 * @return bool
	 */
	public function exists($filename)
	{
		return file_exists($this->nativeForm($filename));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Tells whether the filename is a regular file
	 *
	 * @param string $filename  Path to the file
	 * @return bool  Returns true if the filename exists and is a regular file
	 */
	public function isFile($filename)
	{
		return is_file($this->nativeForm($filename));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Tells whether the filename is a directory
	 *
	 * @param string $filename  Path to the file
	 * @return bool  Returns true if the filename exists and is a directory
	 */
	public function isDir($filename)
	{
		return is_dir($this->nativeForm($filename));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Tells whether the filename is a symbolic link
	 *
	 * @param string $filename  Path to the file
	 * @return bool  Returns true if the filename exists and is a symbolic link
	 */
	public function isLink($filename)
	{
		return is_link($this->nativeForm($filename));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Tells whether the filename is readable
	 *
	 * @param string $filename  Path to the file
	 * @return bool  Returns true if the file or directory specified by filename
	 *               exists and is readable
	 */
	public function isReadable($filename)
	{
		return is_readable($this->nativeForm($filename));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Tells whether the filename is writable
	 *
	 * @param string $filename  Path to the file
	 * @return bool  Returns true if the file or directory specified by filename
	 *               exists and is writable
	 */
	public function isWritable($filename)
	{
		return is_writable($this->nativeForm($filename));
	}
	//-----------------------------------------------------------------------------

}

/**
 * Microsoft® Windows® file system driver class
 *
 * @package Core
 *
 */
class WindowsFS extends GenericFS {

	/**
	 * Convert canonical (UNIX) filename to Windows native form
	 *
	 * @param string $filename
	 * @return string
	 *
	 * @see WindowsFS::canonicalForm()
	 */
	public function nativeForm($filename)
	{
		/* Look for drive letter */
		if (preg_match('~^/[a-z]:/~i', $filename)) {

			$drive = substr($filename, 1, 1);
			$filename = substr($filename, 4);

		} else $drive = false;

		/* Convert slashes */
		$filename = str_replace('/', '\\', $filename);

		/* Prepend drive letter if needed */
		if ($drive) {
			$filename = $drive . ':\\' . $filename;
		}

		return $filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Convert filename from Windows native form to canonical (UNIX)
	 *
	 * @param string $filename
	 * @return string
	 *
	 * @see WindowsFS::nativeForm()
	 */
	public function canonicalForm($filename)
	{
		/* Convert slashes */
		$filename = str_replace('\\', '/', $filename);

		/* Prepend drive letter with slash if needed */
		if (substr($filename, 1, 1) == ':')
			$filename = '/' . $filename;

		return $filename;
	}
	//-----------------------------------------------------------------------------

}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Error & Exception Handling
 *
 *   ...
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/**
 * Error handler
 *
 * @param int    $errno       Error type
 * @param string $errstr      Error description
 * @param string $errfile     Filename where error occured
 * @param int    $errline     Line where error occured
 * @param array  $errcontext  Context symbol table
 */
function EresusErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
{
	/* Zero value of 'error_reporting' means that "@" operator was used, if so, exiting */
	if (error_reporting() == 0) return true;

	/*
	 *  Note: Actualy only E_WARNING, E_NOTICE, E_USER_ERROR, E_USER_WARNING,
	 *  E_USER_NOTICE and E_STRICT can be handled by this function
	 */

	/* Convert error code to log level */
	switch ($errno) {
		case E_STRICT:
		case E_NOTICE:
		case E_USER_NOTICE:
			$level = LOG_NOTICE;
		break;
		case E_WARNING:
		case E_USER_WARNING:
			$level = LOG_WARNING;
		break;
		default: $level = LOG_ERR;
	}

	if ($level < LOG_NOTICE) {

		throw new ErrorException($errstr, $errno, $level, $errfile, $errline);

	} else {

		$logMessage = sprintf(
			"%s in %s:%s",
			$errstr,
			$errfile,
			$errline
		);
		eresus_log(__FUNCTION__, $level, $logMessage);

	}

	return true;
}
//-----------------------------------------------------------------------------

/**
 * Exception handler
 *
 * @param Exception $e  Exception object
 *
 */
function EresusExceptionHandler($e)
{
	if (! ($e instanceof EresusExceptionInterface))
		$e = new EresusRuntimeException(null, null, $e);

	Core::handleException($e);
}
//-----------------------------------------------------------------------------

/**
 * Fatal error handler
 *
 * Perfomance note: this function disposes at begin and allocates at the end
 * memory buffer for memory overflow error handling. These operations slows down
 * output for 1-2%.
 */
function EresusFatalErrorHandler($output)
{
	# Free emergency buffer
	unset($GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER']);
	if (preg_match('/(parse|fatal) error:.*in .* on line/Ui', $output, $m)) {
		$GLOBALS['ERESUS_CORE_FATAL_ERROR_HANDLER'] = true;
		switch(strtolower($m[1])) {
			case 'fatal': $priority = LOG_CRIT; $message = 'Fatal error (see log for more info)'; break;
			case 'parse': $priority = LOG_EMERG; $message = 'Parse error (see log for more info)'; break;
	}
		eresus_log(__FUNCTION__, $priority, trim($output));
		if (!PHP::isCLI())
			header('Content-type: text/plain', true);

		return $message . "\n";
	}
	$GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER'] = str_repeat('x', ERESUS_MEMORY_OVERFLOW_BUFFER * 1024);
	/* Return 'false' to output buffer */
	return false;
}
//-----------------------------------------------------------------------------


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
	 * Test mode switch
	 * @var bool
	 */
	static private $testMode = false;

	/**
	 * Test mode settings
	 * @var array
	 */
	static private $testModeOptions;

	/**
	 * Application
	 * @var EresusApplication
	 * @see exec, app()
	 */
	static private $app = null;

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

		System::init();
		FS::init();

		eresus_log(__METHOD__, LOG_DEBUG, '()');

		self::initExceptionHandling();

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
	 * Init exception handling
	 *
	 */
	static private function initExceptionHandling()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		/* Reserve memory for emergency needs */
		$GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER'] = str_repeat('x', ERESUS_MEMORY_OVERFLOW_BUFFER * 1024);

		/* Override php.ini settings */
		ini_set('html_errors', 0); // Some cosmetic setup

		set_error_handler('EresusErrorHandler');
		eresus_log(__METHOD__, LOG_DEBUG, 'Error handler installed');

		set_exception_handler('EresusExceptionHandler');
		eresus_log(__METHOD__, LOG_DEBUG, 'Exception handler installed');

		/*
		 * PHP has no standart methods to intercept some error types (e.g. E_PARSE or E_ERROR),
		 * but there is a way to do this - register callback function via ob_start.
		 * But not in CLI mode.
		 */
		if (! PHP::isCLI())
		{
			if (ob_start('EresusFatalErrorHandler', 4096))
				eresus_log(__METHOD__, LOG_DEBUG, 'Fatal error handler installed');
			else
				eresus_log(
					LOG_NOTICE, __METHOD__,
					'Fatal error handler not instaled! Fatal error will be not handled!'
				);
		}

		eresus_log(__METHOD__, LOG_DEBUG, 'done');
	}
	//-----------------------------------------------------------------------------

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
		eresus_log('Core', LOG_ERR, $logMessage);

		if ($previous)
			self::logException($previous, 'Previous exception:');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Default Eresus Core exception handler.
	 *
	 * If exception was not caught by application it will be handled with this method.
	 *
	 * @param Exception $e
	 */
	static public function handleException($e)
	{
		$app = self::app();
		if ($app && method_exists($app, 'handleException')) {

			$app->handleException($e);

		} else {

			Core::logException($e, 'Unhandled exception:');

			if (!PHP::isCLI())
				header('Content-type: text/plain', true);

			echo get_class($e);

			if ($e instanceof EresusExceptionInterface)
				echo ': ' . $e->getMessage();

			if (PHP::isCLI() && !self::testMode())
				exit($e->getCode());

		}

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
	 * Make instance of application and execute it
	 *
	 * @param string $class  Application class name. Class must be derived from
	 *                       {@link EresusApplication}
	 * @return int  Exit code
	 *
	 * @see $app, app(), EresusApplication
	 */
	static public function exec($class)
	{
		if (!class_exists($class, false))
			throw new EresusRuntimeException(
				"Application class '$class' does not exists", 'Invalid application class'
			);

		if (!is_subclass_of($class, 'EresusApplication'))
			throw new EresusRuntimeException(
				"Application '$class' must be descendant of EresusApplication", 'Invalid application class'
			);

		self::$app = new $class();

		try {

			eresus_log(__METHOD__, LOG_DEBUG, 'executing %s', $class);
			$exitCode = self::$app->main();
			eresus_log(__METHOD__, LOG_DEBUG, '%s done with code: %d', $class, $exitCode);

		} catch (Exception $e) {

			self::handleException($e);
			$exitCode = $e->getCode() ? $e->getCode() : 0xFFFF;

		}
		self::$app = null;
		return $exitCode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get application object or null if there is no application instance
	 *
	 * @return object(EresusApplication)
	 *
	 * @see $app, exec(), EresusApplication
	 */
	static public function app()
	{
		return self::$app;
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
		$filename = FS::nativeForm($filename);

		/*
		 * Try relative to current working directory
		 */

		eresus_log(__METHOD__, LOG_DEBUG, 'Try relative to current directory...');

		if (FS::exists($filename)) {

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

			if (! PHP::inOpenBaseDir($dir)) {
				eresus_log(
					__METHOD__, LOG_DEBUG, 'Path "%s" is out of open_basedir list. Skipping for now.', $dir
				);
				$unsafe []= $dir;
				continue;
			}

			eresus_log(__METHOD__, LOG_DEBUG, 'Probing "%s"', $dir . DIRECTORY_SEPARATOR . $filename);
			if (FS::exists($dir . DIRECTORY_SEPARATOR . $filename)) {

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
			throw new EresusFsFileNotExistsException(
				$filename,
				"File '$filename' not found in '".get_include_path()."'",
				$e
			);

		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Switch test mode
	 *
	 * @param bool $state
	 * @return bool  Current state
	 */
	static public function testMode($state = null)
	{
		if (!is_null($state)) self::$testMode = $state;
		return self::$testMode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set test mode option
	 *
	 * @param string $option
	 * @param mixed  $value
	 */
	static public function testModeSet($option, $value)
	{
		self::$testModeOptions[$option] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if test mode option is set
	 *
	 * @param string $option
	 * @return bool
	 */
	static public function testModeIsSet($option)
	{
		if (!self::testMode()) return null;
		return isset(self::$testModeOptions[$option]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get test mode option
	 *
	 * @param string $option
	 * @return mixed
	 */
	static public function testModeGet($option)
	{
		return self::testMode() ? ecArrayValue(self::$testModeOptions, $option) : null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Unset test mode option
	 *
	 * @param string $option
	 */
	static public function testModeUnset($option)
	{
		if (isset(self::$testModeOptions[$option]))
			unset(self::$testModeOptions[$option]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Object to be returned by app() method in a test mode
	 *
	 * @param object $app
	 */
	static public function testSetApplication($app)
	{
		if (self::testMode()) self::$app = $app;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Test initExceptionHandling method
	 */
	static public function testInitExceptionHandling()
	{
		if (self::testMode())
			self::initExceptionHandling();
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

if (!defined('ERESUS_TEST_MODE'))
	Core::init();


/**
 * Eresus application prototype
 *
 * Must be overriden by user application class. See {@link main()} for
 * more details.
 *
 * @see main(), Core::exec()
 *
 * @package Core
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
abstract class EresusApplication {

	/**
	 * Holds path to application root directory
	 *
	 * Setting in {@link initFS()}. Use {@link getFsRoot()} to get this value.
	 *
	 * @var string
	 * @see getFsRoot(), initFS()
	 */
	protected $fsRoot;

	/**
	 * Main application function
	 *
	 * Developer must implement this method in his application.
	 *
	 * This method will be called by {@link Core::exec()}.
	 *
	 * <code>
	 * class MyApp extends EresusApplication {
	 *
	 *   public function main()
	 *   {
	 *     // Main code of your application goes here:
	 *     // 1. You can do some init tasks
	 *     // 2. You can do some usefull job ;-)
	 *     // 3. At the end you can do some finalizing tasks
	 *     return $exitCode;
	 *   }
	 * }
	 * </code>
	 *
	 * @return int  Exit code
	 * @see Core::exec()
	 */
	abstract public function main();
	//-----------------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * 1. Inits FS related parts of application
	 * 2. If application has method called 'autoload' registers it through
	 *    {@link Core::registerAutoloader}
	 *
	 * There is no need to call constructor directly. It will be called
	 * automaticly from {@link Core::exec()}
	 *
	 * @return EresusApplication
	 * @see initFS(), Core::exec(), Core::registerAutoloader()
	 */
	function __construct()
	{

		$this->initFS();
		if (method_exists($this, 'autoload'))
			Core::registerAutoloader(array($this, 'autoload'));

	}
	//-----------------------------------------------------------------------------

	/**
	 * Init FS related parts of application
	 *
	 * - Sets {@link fsRoot} by calling {@link detectFsRoot}
	 *
	 * @return void
	 *
	 * @see fsRoot, detectFsRoot()
	 */
	protected function initFS()
	{
		$this->fsRoot = $this->detectFsRoot();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Trying to determine application root directory
	 *
	 * In CLI mode $GLOBALS['argv'][0] used.
	 *
	 * In other modes $_SERVER['SCRIPT_FILENAME'] used.
	 *
	 * @return string
	 * @see fsRoot, getFsRoot()
	 */
	protected function detectFsRoot()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');
		$path = false;

		switch (true) {

			case PHP::isCLI():
				$path = reset($GLOBALS['argv']);
				eresus_log(__METHOD__, LOG_DEBUG, 'Using global $argv variable: %s', $path);
				$path = FS::canonicalForm($path);
				$path = FS::dirname($path);
			break;

			default:
				eresus_log(__METHOD__, LOG_DEBUG, 'Using $_SERVER["SCRIPT_FILENAME"]: %s',
					$_SERVER['SCRIPT_FILENAME']);

				$path = FS::canonicalForm($_SERVER['SCRIPT_FILENAME']);
				$path = FS::dirname($path);
				/*
				 * TODO: The CGI SAPI supports CLI SAPI behaviour with a -C switch
				 * when run from the command line.
				 */

		}

		$path = FS::normalize($path);
		eresus_log(__METHOD__, LOG_DEBUG, '"%s"', $path);

		return $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get application root directory
	 *
	 * @return string
	 * @see fsRoot
	 */
	public function getFsRoot()
	{
		return $this->fsRoot;
	}
	//-----------------------------------------------------------------------------

}

/**
 * DB module settings
 *
 * @package DB
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 * @deprecated since 0.1.3
 */
class DBSettings
{
	private static $dsn;
	/**
	 * Set DSN
	 *
	 * @param string $dsn
	 */
	public static function setDSN($dsn)
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		self::$dsn = $dsn;
		DB::lazyConnection($dsn);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set code page
	 *
	 * @param string $codepage
	 */
	public static function setCodepage($codepage)
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		DB::lazyConnection(self::$dsn . '?charset=' . $codepage);
	}
	//-----------------------------------------------------------------------------
}



/**
 * DB Runtime Exception
 *
 * @package DB
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class DBRuntimeException extends EresusRuntimeException {
}



/**
 * DB Query Exception
 *
 * @package DB
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class DBQueryException extends DBRuntimeException {

	/**
	 * Creates new exception object
	 *
	 * @param ezcQuery  $query [optional]    Problem query
	 * @param string    $message [optional]  Error message
	 * @param Exception $previous [optional] Previous exception
	 */
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



/**
 * Database interface
 *
 * @package DB
 */
class DB implements ezcBaseConfigurationInitializer
{
	/**
	 * Список DSN "ленивых" соединений
	 * @var array(mixed => string)
	 */
	private static $lazyConnectionDSNs = array();

	/**
	 * Connects to DB
	 *
	 * @param string $dsn              Connection DSN string
	 * @param string $name [optional]  Optional connection name
	 * @return ezcDbHandler
	 * @throws DBRuntimeException
	 */
	public static function connect($dsn, $name = false)
	{
		eresus_log(__METHOD__, LOG_DEBUG, '("%s", %s)', $dsn, $name);

		try
		{
			$db = ezcDbFactory::create($dsn);
		}
			catch (Exception $e)
		{
			throw new DBRuntimeException("Can not connect to '$dsn'", "Database connection failed", $e);
		}

		$db->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
		ezcDbInstance::set($db, $name);
		return $db;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Configures lazy connection to DB
	 *
	 * @param string $dsn              Connection DSN string
	 * @param string $name [optional]  Optional connection name
	 * @return void
	 */
	public static function lazyConnection($dsn, $name = false)
	{
		eresus_log(__METHOD__, LOG_DEBUG, '("%s", %s)', $dsn, $name);
		self::$lazyConnectionDSNs[$name] = $dsn;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Returns connection handler
	 *
	 * @param string $name [optional]  Optional connection name
	 * @return ezcDbHandler
	 */
	public static function getHandler($name = false)
	{
		return ezcDbInstance::get($name);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Sets connection handler
	 *
	 * @param ezcDbHandler $handler
	 * @param string       $name [optional]  Optional connection name
	 *
	 */
	public static function setHandler(ezcDbHandler $handler, $name = false)
	{
		ezcDbInstance::set($handler, $name);
	}
	//-----------------------------------------------------------------------------

	/**
	 * eZ Components lazy init
	 *
	 * @param bool|string $name  Connection name
	 * @return ezcDbHandler
	 * @internal
	 * @ignore
	 */
	public static function configureObject($name)
	{
		eresus_log(__METHOD__, LOG_DEBUG, '(%s)', $name);

		if (!isset(self::$lazyConnectionDSNs[$name]))
			throw new DBRuntimeException('DSN for lazy connection "'.$name.'" not found');

		$dsn = self::$lazyConnectionDSNs[$name];
		$db = self::connect($dsn, $name);

		if (substr($dsn, 0, 5) == 'mysql' && preg_match('/charset=(.*?)(&|$)/', $dsn, $m))
		{
			$db->query("SET NAMES {$m[1]}");
		}

		return $db;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set test instance
	 * @param object $instance
	 * @deprecated since 0.1.3 in favor of DB::setHandler
	 */
	public static function setTestInstance($instance)
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		self::setHandler($instance);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get connection
	 *
	 * @return object
	 * @deprecated since 0.1.3 in favor of DB::getHandler
	 */
	public static function getInstance()
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		return self::getHandler();
	}
	//-----------------------------------------------------------------------------

	/**
	 * SELECT
	 * @return ezcQuerySelect
	 * @deprecated since 0.1.3 in favor of DB::getHandler()->createSelectQuery
	 */
	public static function createSelectQuery()
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		$db = self::getInstance();
		return $db->createSelectQuery();
	}
	//-----------------------------------------------------------------------------

	/**
	 * UPDATE
	 * @return ezcQueryUpdate
	 * @deprecated since 0.1.3 in favor of DB::getHandler()->createUpdateQuery
	 */
	public static function createUpdateQuery()
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		$db = self::getInstance();
		return $db->createUpdateQuery();
	}
	//-----------------------------------------------------------------------------

	/**
	 * INSERT
	 * @return ezcQueryInsert
	 * @deprecated since 0.1.3 in favor of DB::getHandler()->createInsertQuery
	 */
	public static function createInsertQuery()
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		$db = self::getInstance();
		return $db->createInsertQuery();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Delete
	 * @return ezcQueryDelete
	 * @deprecated since 0.1.3 in favor of DB::getHandler()->createDeleteQuery
	 */
	public static function createDeleteQuery()
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		$db = self::getInstance();
		return $db->createDeleteQuery();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Execute query
	 * @param ezQuery $query
	 * @return mixed
	 */
	public static function execute($query)
	{
		try {

			$stmt = $query->prepare();
			if (LOG_DEBUG) {
				$insider = new DBQueryInsider;
				$query->doBind($insider);
				$s = $insider->subst($query);
				eresus_log(__METHOD__, LOG_DEBUG, 'Query "%s"', $s);
			}
			$result = $stmt->execute();

		} catch (Exception $e) {

			throw new DBQueryException($query, null, $e);

		}

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Fetch row from DB response
	 * @param ezcQuery $query
	 * @return array
	 */
	public static function fetch($query)
	{
		if (LOG_DEBUG) {
			$insider = new DBQueryInsider;
			$query->doBind($insider);
			$s = $insider->subst($query);
			eresus_log(__METHOD__, LOG_DEBUG, 'Query "%s"', $s);
		}
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

	/**
	 * Get response rows
	 * @param ezcQuery $query
	 * @return array
	 */
	public static function fetchAll($query)
	{
		if (LOG_DEBUG) {
			$insider = new DBQueryInsider;
			$query->doBind($insider);
			$s = $insider->subst($query);
			eresus_log(__METHOD__, LOG_DEBUG, 'Query "%s"', $s);
		}

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



/**
 * Query Insider
 *
 * Internal class for substitution in doBind method to get values
 * set with bindValue or bindParam methods.
 *
 * @package DB
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 * @internal
 * @ignore
 *
 */
class DBQueryInsider extends PDOStatement {

	/**
	 * Values
	 * @var array
	 */
	protected $values = array();

	/**
	 * Bind value
	 *
	 * @param paramno
	 * @param param
	 * @param type[optional]
	 */
	public function bindValue($paramno, $param, $type = null)
	{
		switch ($type) {

			case PDO::PARAM_BOOL:
			break;

			case PDO::PARAM_INT:
			break;

			case PDO::PARAM_STR:
				$param = is_null($param) ?
					'NULL' :
					"'" . addslashes($param) . "'";
			break;
		}

		$this->values[$paramno] = $param;

	}
	//-----------------------------------------------------------------------------

	/**
	 * Bind param
	 *
	 * @param paramno
	 * @param param
	 * @param type[optional]
	 * @param maxlen[optional]
	 * @param driverdata[optional]
	 */
	public function bindParam($paramno, &$param, $type = null, $maxlen = null, $driverdata = null)
	{
		$this->bindValue($paramno, $param, $type);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Substitute values in query
	 *
	 * @param string $query
	 * @return string
	 */
	public function subst($query)
	{
		foreach ($this->values as $key => $value)
			$query = preg_replace("/$key(\s|,|$)/", "$value$1", $query);

		return $query;
	}
	//-----------------------------------------------------------------------------

}

ezcBaseInit::setCallback('ezcInitDatabaseInstance', 'DB');


/**
 * Including Dwoo
 */
include_once '3rdparty/dwoo/dwooAutoload.php';

/**
 * Template package settings
 *
 * This class can be used to configure behavor of the Template package.
 *
 * @package Template
 *
 */
class TemplateSettings {

	/**
	 * Global substitution value to be used in all templates
	 * @var array
	 */
	private static $gloablValues = array();

	/**
	 * Set global substitution value to be used in all templates
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public static function setGlobalValue($name, $value)
	{
		self::$gloablValues[$name] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get global substitution value
	 *
	 * @param string $name
	 * @return null|mixed  Null will be returned if value not set
	 */
	public static function getGlobalValue($name)
	{
		return ecArrayValue(self::$gloablValues, $name);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Remove global substitution value
	 *
	 * @param string $name
	 */
	public static function removeGlobalValue($name)
	{
		if (isset(self::$gloablValues[$name])) unset(self::$gloablValues[$name]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get all global substitution values
	 *
	 * @return array
	 */
	public static function getGlobalValues()
	{
		return self::$gloablValues;
	}
	//-----------------------------------------------------------------------------
}


/**
 * Template file
 *
 * @package Template
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class TemplateFile extends Dwoo_Template_File {

}


/**
 * Template
 *
 * <b>CONFIGURATION</b>
 * Templte uses Core::getValue to read its configuration:
 *
 * <b>core.template.templateDir</b>
 * Directory where templates located.
 *
 * <b>core.template.compileDir</b>
 * Directory to store compiled templates.
 *
 * <b>core.template.charset</b>
 * Charset of template files.
 *
 * <b>core.template.fileExtension</b>
 * Default extensions of template files.
 *
 * @package Template
 */
class Template
{
	/**
	 * Dwoo object
	 * @var Dwoo
	 */
	protected $dwoo;

	/**
	 * Template file object
	 * @var TemplateFile
	 */
	protected $file;

	/**
	 * Constructor
	 * @var string $filename [optional]  Template file name
	 */
	public function __construct($filename = null)
	{
		$compileDir = $this->detectCompileDir();
		$compileDir = FS::nativeForm($compileDir);
		$this->dwoo = new Dwoo($compileDir);

		if (Core::getValue('core.template.charset'))
			$this->dwoo->setCharset(Core::getValue('core.template.charset'));

		if ($filename) $this->loadFile($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Load template file
	 * @param string $filename  Template file name
	 */
	public function loadFile($filename)
	{
		$templateDir = $this->detectTemplateDir();
		$fileExtension = $this->detectFileExtension();
		$templateDir = FS::normalize($templateDir);
		$template = $templateDir . '/' . $filename . $fileExtension;
		$template = FS::nativeForm($template);
		$this->file = new TemplateFile($template, null, $filename, $filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Compile template
	 *
	 * @param array $data [optional]  Data for template
	 *
	 * @return string
	 */
	function compile($data = null)
	{
		if ($data)
			$data = array_merge($data, TemplateSettings::getGlobalValues());
		else
			$data = TemplateSettings::getGlobalValues();

		return $this->dwoo->get($this->file, $data);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Detect directory where templates located
	 *
	 * @return string
	 */
	protected function detectTemplateDir()
	{
		$compileDir = Core::getValue('core.template.templateDir', '');

		return $compileDir;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Detect template files extension
	 *
	 * @return string
	 */
	protected function detectFileExtension()
	{
		$fileExtension = Core::getValue('core.template.fileExtension', '');

		return $fileExtension;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Detect directory where compiled templates will be stored
	 *
	 * @return string
	 */
	protected function detectCompileDir()
	{
		$compileDir = Core::getValue('core.template.compileDir', '');

		return $compileDir;
	}
	//-----------------------------------------------------------------------------
}


/**
 * HTTP Headers
 *
 * @package HTTP
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class HttpHeaders {

	/**
	 * Headers
	 * @var array
	 */
	protected $headers = array();

	/**
	 * Constructor
	 */
	function __construct()
	{
	}
	//-----------------------------------------------------------------------------

	/**
	 * Add header
	 * @param HttpHeader $header
	 */
	function add($header)
	{
		$this->headers []= $header;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get all headers
	 * @return array
	 */
	function getAll()
	{
		return $this->headers;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Send headers to UA
	 */
	public function send()
	{
		$headers = $this->getAll();

		foreach($headers as $header) $header->send();
	}
	//-----------------------------------------------------------------------------
}
//-----------------------------------------------------------------------------

/**
 * HTTP Header
 *
 * @package HTTP
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class HttpHeader {

	/**
	 * Header name
	 * @var string
	 */
	protected $name;

	/**
	 * Header value
	 * @var string
	 */
	protected $value;

	/**
	 * Constructor
	 * @param string $name
	 * @param string $value
	 */
	public function __construct($name, $value)
	{
		$this->name = $name;
		$this->value = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Return header as string
	 * @return string
	 */
	public function __toString()
	{
		return $this->name . ': ' . $this->value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Send header to UA
	 */
	public function send()
	{
		if (!PHP::isCLI()) header($this);
	}
	//-----------------------------------------------------------------------------
}
//-----------------------------------------------------------------------------


/**
 * HTTP Toolkit
 *
 * @package HTTP
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class HTTP
{

	/**
	 * HTTP request object
	 * @var HTTPRequest
	 */
	static private $request;

	/**
	 * Sets test instance of HttpRequest
	 *
	 * @param HttpRequest|null $request
	 */
	static public function setTestRequest($request)
	{
		if (Core::testMode())
			self::$request = $request;
	}
	//-----------------------------------------------------------------------------

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
		eresus_log(__METHOD__, LOG_DEBUG, $uri);

		$header = 'Location: '.$uri;

		if ($permanent)
			header($header, true, 301);
		else
			header($header);

		if (!Core::testMode()) exit;
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
 *
 * @package HTTP
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class HttpRequest
{
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
					if (Core::testModeGet('magic_quotes_gpc') && !get_magic_quotes_gpc()) {
						/* Emulating parse_str behavor... */
						foreach ($this->request['args'] as $key => $value)
							$this->request['args'][$key] = addslashes($value);
					}
					if (
						$this->request['args'] &&
						(get_magic_quotes_gpc() || Core::testModeGet('magic_quotes_gpc'))
					)
						$this->request['args'] = ecStripSlashes($this->request['args']);
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

					if (
						$this->request['args'] &&
						(get_magic_quotes_gpc() || Core::testModeGet('magic_quotes_gpc'))
					)
						$this->request['args'] = ecStripSlashes($this->request['args']);

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

			$this->request['method'] = isset($_SERVER['REQUEST_METHOD']) ?
				strtoupper($_SERVER['REQUEST_METHOD']) :
				'GET';

		}

		$result = $this->request['method'];

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set request method
	 *
	 * @param string $value
	 */
	public function setMethod($value)
	{
		$this->request['method'] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get host from request
	 * @return string
	 */
	public function getHost()
	{
		if (!isset($this->request['host'])) {

			$this->request['host'] = isset($_SERVER['HTTP_HOST']) ?
				strtolower($_SERVER['HTTP_HOST']) :
				'localhost';

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
	 *
	 * @param string $arg                Atgument name
	 * @param mixed  $filter [optional]  Filter
	 * @return mixed
	 */
	public function arg($arg, $filter = null)
	{
		if (!isset($this->request['args'][$arg]))
			return null;

		$result =  $this->request['args'][$arg];

		switch (true)
		{
			case is_callable($filter, false, $callback):
				if (is_array($filter) && is_object($filter[0]))
					$result = $filter[0]->$filter[1]($result);
				else
					$result = $callback($result);
			break;

			case is_string($filter):

				switch ($filter)
				{
					case 'int':
					case 'integer':
							$result = intval(filter_var($result, FILTER_SANITIZE_NUMBER_INT));
					break;
					case 'float':
							$result = floatval(filter_var($result, FILTER_SANITIZE_NUMBER_FLOAT,
								FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND |
								FILTER_FLAG_ALLOW_SCIENTIFIC));
					break;
					default:
						$result = preg_replace($filter, '', $result);
					break;
				}

			break;
		}

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see function arg
	 */
	public function getArg($arg, $filter = null)
	{
		return $this->arg($arg, $filter);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set value of GET or POST argument
	 *
	 * @param string $arg
	 * @param mixed  $value
	 */
	public function setArg($arg, $value)
	{
		$this->request['args'][$arg] = $value;
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
	 *
	 * Local root is a part of URI after host name which will be cutted from result
	 * of HttpRequest::getLocal.
	 *
	 * <code>
	 * $req = new HttpRequest('http://example.org/some/path/script?query');
	 * echo $req->getLocal(); // '/some/path/script?query'
	 * $req->setLocalRoot('/some');
	 * echo $req->getLocal(); // '/path/script?query'
	 * </code>
	 *
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




/**
 * HTTP Response
 *
 * @package HTTP
 *
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

