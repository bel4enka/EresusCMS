<?php
/**
 * Eresus Core
 *
 * @version 0.1.0
 *
 * Registry pattern
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
 * @package Core
 * @subpackage Misc
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: Registry.php 274 2009-05-18 07:23:08Z mekras $
 */

/**
 * Registry pattern
 *
 * Global storage for shared data.
 *
 * @package Core
 * @subpackage Misc
 *
 * @author mekras
 *
 */
class Registry {

	/**
	 * Registry data
	 * @var array
	 */
	private static $data = array();

	/**
	 * Check if entry exists in registry
	 *
	 * @param scalar $key
	 *
	 * @return bool
	 */
	public static function exists($key)
	{
		return isset(self::$data[$key]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Put new data in registry
	 *
	 * @param scalar $key
	 * @param mixed $value
	 *
	 * @throws EresusLogicException
	 * @see register
	 */
	public static function put($key, $value)
	{
		if (self::exists($key)) throw new EresusLogicException("Key '$key' allready exists in registry.", 'Can not put value in registry');

		self::$data[$key] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Store data in registry
	 *
	 * Instead of 'put' method this one does not throw exception if $key exists
	 *
	 * @param scalar $key
	 * @param mixed $value
	 *
	 * @see put
	 */
	public static function set($key, $value)
	{
		self::$data[$key] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Alias for 'put'
	 *
	 * @param scalar $key
	 * @param mixed $value
	 *
	 * @see put
	 */
	public static function register($key, $value)
	{
		self::put($key, $value);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get entry from registry
	 * @param scalar $key
	 * @return mixed
	 * @throws EresusLogicException
	 */
	public static function get($key)
	{
		if (!self::exists($key)) throw new EresusLogicException("Key '$key' not found in registry.", 'Value does not exists in registry');

		return self::$data[$key];
	}
	//-----------------------------------------------------------------------------

	/**
	 * Remove entry from registry
	 * @param scalar $key
	 */
	public static function remove($key)
	{
		if (self::exists($key)) unset(self::$data[$key]);
	}
	//-----------------------------------------------------------------------------
}
//-----------------------------------------------------------------------------

