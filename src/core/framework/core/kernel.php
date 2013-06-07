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
	 */
	static function isWindows()
	{
		return strncasecmp(PHP_OS, 'WIN', 3) == 0;
	}

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

	/**
	 * Attempts to create the directory specified by pathname
	 *
	 * @param string   $pathname   The directory path.
	 * @param int      $mode       The file access mode. Default is 0777.
	 * @param bool     $recursive  Allows the creation of nested directories specified
	 *                              in the pathname. Defaults to FALSE.
	 * @param resource $context    Function context.
	 *
	 * @return bool
	 *
	 * @uses dirUmask
	 */
	public static function mkDir($pathname, $mode = 0777, $recursive = false, $context = null)
	{
		return self::$driver->mkDir($pathname, $mode, $recursive, $context);
	}
	//-----------------------------------------------------------------------------

}


/**
 * Generic file system class
 *
 * @package Core
 */
class GenericFS
{
	/**
	 * Directory create umask
	 * @var int
	 */
	public $dirUmask = 0000;

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

	/**
	 * Attempts to create the directory specified by pathname
	 *
	 * @param string   $pathname   The directory path.
	 * @param int      $mode       The file access mode. Default is 0777.
	 * @param bool     $recursive  Allows the creation of nested directories specified
	 *                              in the pathname. Defaults to FALSE.
	 * @param resource $context    Function context.
	 *
	 * @return bool
	 *
	 * @uses dirUmask
	 */
	public function mkDir($pathname, $mode = 0777, $recursive = false, $context = null)
	{
		$umask = umask($this->dirUmask);
		if (is_null($context))
			$result = mkdir($pathname, $mode, $recursive);
		else
			$result = mkdir($pathname, $mode, $recursive, $context);
		umask($umask);
		return $result;
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

			if (file_exists($filename))
            {
				include $filename;
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

		try
        {
			$this->table = include $this->filename;

		}
        catch (Exception $e)
        {
			eresus_log(
				__METHOD__, LOG_ERR, 'Can\'t load table from "%s": %s', $this->filename,
                $e->getMessage()
			);
			$this->filename = false;

		}

		eresus_log(__METHOD__, LOG_DEBUG, $this->table ? 'success' : 'failed');
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
		if (self::$initState)
			return;

		self::$initState = 1;

		System::init();
		FS::init();

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
			eresus_log(__METHOD__, LOG_DEBUG, 'registering handler "%s"', $callback);
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
