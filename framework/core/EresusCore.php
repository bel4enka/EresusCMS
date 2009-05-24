<?php
/**
 * Eresus Core
 *
 * @version 0.1.0
 *
 * Init module
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
 * @subpackage Kernel
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id$
 */


/*
 * Ensure that we using supported PHP version
 */

/**
 * Required PHP version
 */
if ( !defined('ERESUS_CORE_PHP_VERSION') ) define('ERESUS_CORE_PHP_VERSION', '5.1.0');

# Check PHP version
if (PHP_VERSION < ERESUS_CORE_PHP_VERSION)
	die("Eresus Core: Invalid PHP version ".PHP_VERSION.". Needed ".ERESUS_CORE_PHP_VERSION." or later.\n");

if (@constant('ERESUS_CORE_USE_COMPILED'))
	include_once 'kernel.compiled.php';
else
	include_once 'kernel.php';

Core::init();

if (! PHP::checkVersion('5.1.2')) {

	/**
	 * Class autoload function
	 *
	 * This function is only needed if PHP is older than 5.1.2
	 *
	 * @param string $className
	 * @internal
	 * @ignore
	 */
	function __autoload($className)
	{
		Core::autoload($className);
	}
	//-----------------------------------------------------------------------------

}