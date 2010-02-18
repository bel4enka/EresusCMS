<?php
/**
 * Eresus Core
 *
 * @version 0.1.3
 *
 * Application prototype
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
 * $Id$
 */

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