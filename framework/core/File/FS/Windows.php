<?php
/**
 * Eresus Core
 *
 * @version 0.1.0
 *
 * Microsoft(R) Windows(TM) file system driver
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
 * @subpackage FS
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: Windows.php 284 2009-05-19 12:59:35Z mekras $
 */

/**
 * Microsoft(R) Windows(TM) file system driver class
 *
 * @package Core
 * @subpackage FS
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
