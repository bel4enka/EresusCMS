<?php
/**
 * Eresus Core
 *
 * @version 0.1.0
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
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id$
 */

/**
 * Eresus application prototype
 *
 * @package Core
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 * @see Core::exec()
 */
abstract class EresusApplication {

	/**
	 * Internal options
	 * @var array
	 */
	protected $opt = array();

	/**
	 * Application root directory
	 * @var string
	 */
	protected $fsRoot;

	/**
	 * Main application function
	 * @return int  Exit code
	 */
	abstract public function main();

	/**
	 * Constructor
	 */
	function __construct()
	{

		$this->initFS();
		if (method_exists($this, 'autoload')) Core::registerAutoloader(array($this, 'autoload'));

	}
	//-----------------------------------------------------------------------------

	protected function initFS()
	{
		$this->fsRoot = $this->detectFsRoot();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Trying to determine application root directory
	 */
	protected function detectFsRoot()
	{
		elog(__METHOD__, LOG_DEBUG, '()');
		$path = false;

		switch (true) {

			case PHP::isCLI():
				elog(__METHOD__, LOG_DEBUG, 'Using global $argv variable');
				$path = reset($GLOBALS['argv']);
				$path = FS::canonicalForm($path);
				$path = FS::dirname($path);
				if (Core::testMode()) $path = null;
				if (!$path) {
					elog(__METHOD__, LOG_DEBUG, 'In addition using getcwd()');
					$path = getcwd();

					$path = FS::canonicalForm($path);
				}
			break;

			default:
				elog(__METHOD__, LOG_DEBUG, 'Using getcwd()');
				$path = getcwd();
				$path = FS::canonicalForm($path);
				#TODO: The CGI SAPI supports CLI SAPI behaviour with a -C switch when run from the command line.

		}

		$path = FS::normalize($path, 'dir');
		elog(__METHOD__, LOG_DEBUG, '"%s"', $path);

		return $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get internal option value
	 *
	 * @deprecated Use Registry instead of application to store global values
	 *
	 * @param string $section  Option section
	 * @param string $key      Option key
	 * @return mixed  Result or NULL if option does not exists
	 */
	public function getOpt($section, $key)
	{
		return isset($this->opt[$section][$key]) ? $this->opt[$section][$key] : null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set internal option value
	 *
	 * @deprecated Use Registry instead of application to store global values
	 *
	 * @param string $section           Option section
	 * @param string $key               Option key
	 * @param mixed  $value             Option value
	 * @param bool   $force [optional]  Create value if not exists
	 */
	protected function setOpt($section, $key, $value, $force = true)
	{
		if ($force || isset($this->opt[$section][$key])) $this->opt[$section][$key] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get application root directory
	 *
	 * @param string $filename [optional]  Optional filename to attach to directory
	 * @return string
	 */
	public function getFsRoot($filename = null)
	{
		if ($filename) return FS::normalize($this->fsRoot . $filename);

		return $this->fsRoot;
	}
	//-----------------------------------------------------------------------------

	/**
   * Load application module
   *
   * @param string $module             Module path and name
   * @param bool   $silent [optional]  Don't throw an exception if module not exists
   *
   * @return mixed
   *
   * @throws EresusRuntimeException
	 */
	public function load($module, $silent = false)
	{
		elog(__METHOD__, LOG_DEBUG, "($module)");
		$filename = FS::nativeForm($this->getFsRoot($module . '.php'));

		try {

			$exists = is_file($filename);
			elog(__METHOD__, LOG_DEBUG, 'File "%s" %s', $filename, $exists ? 'exists' : 'not exists');

			if ($silent && !$exists) return null;

			$result = include_once $filename;

		} catch (EresusRuntimeException $e) {

			throw new EresusRuntimeException($e->getDescription(), 'Can not load module "'.$module.'"', $e);

		}
		elog(__METHOD__, LOG_DEBUG, "loaded module '$filename'");
		return $result;
	}
	//-----------------------------------------------------------------------------

}